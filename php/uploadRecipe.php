<?php
    include("config.php");
    include("normalizeChars.php");
    include("saveMedia.php");
    include("checkValidCharacters.php");
    session_start();


    $recipeImage = isset($_POST['recipeImage']) ? mysqli_real_escape_string($db, $_POST['recipeImage']) : null;
    $recipeTitle = isset($_POST['recipeTitle']) ? ucfirst(strtolower(normalizeChars(trim(mysqli_real_escape_string($db, $_POST['recipeTitle']))))) : null;
    $preparationTime = isset($_POST['preparationTime']) ? mysqli_real_escape_string($db, $_POST['preparationTime']) : null;
    $cookingTime= isset($_POST['cookingTime']) ? mysqli_real_escape_string($db, $_POST['cookingTime']) : null;
    $difficulty = isset($_POST['difficulty']) ? mysqli_real_escape_string($db, $_POST['difficulty']) : null;
    $ingredientsForPeople = isset($_POST['ingredientsForPeople']) ? mysqli_real_escape_string($db, $_POST['ingredientsForPeople']) : null;
    $cost = isset($_POST['cost']) ? mysqli_real_escape_string($db, $_POST['cost']) : null;
    $ingredients = isset($_POST['ingredients']) ? json_decode($_POST['ingredients'], true) : null;
    $curiosity = isset($_POST['curiosity']) ? ucfirst(mysqli_real_escape_string($db, $_POST['curiosity'])) : null;
    $steps= isset($_POST['steps']) ? json_decode($_POST['steps'], true) : null;
    $recipeInfo=[];
    $recipe_num = 0;


    try {

        if(strlen($recipeTitle)==0 || strlen($recipeTitle)>255){
            $db->close();
            die("Error: recipe's title length is 0 or longer than 255 characters");
        }

        $bad_character = checkValidCharacters($recipeTitle);
        if(isset($bad_character)){
            $db->close();
            die("Error: found bad character in recipe's title: ".$bad_character);
        }

        if(strlen($curiosity)>1000){
            $db->close();
            die("Error: curiosity length is longer than 1000 characters");
        }

        if(strlen($recipeImage)==0){
            $db->close();
            die("Error: you must insert an image");
        }

        if(isset($_SESSION['user_id'])){
            $recipe_image_path = saveMedia($recipeImage, "IMG");

            if(isset($recipe_image_path)){

                //insert a post
                $stmt = $db->prepare("insert into posts values(DEFAULT,?,?,?,'IMG',DEFAULT,DEFAULT);");
                $stmt->bind_param(
                    "sss",
                    $_SESSION['user_id'],
                    $recipeTitle,
                    $recipe_image_path
                );

                if($stmt->execute()){
                    $post_id = $stmt->insert_id;
                    $stmt->close();

                    //insert recipe linked to the post
                    $stmt = $db->prepare("INSERT INTO recipes VALUES (DEFAULT, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
                    $stmt->bind_param(
                        "sssssssss",
                        $post_id,
                        $recipe_image_path,
                        $recipeTitle,
                        $preparationTime,
                        $cookingTime,
                        $difficulty,
                        $cost,
                        $ingredientsForPeople,
                        $curiosity
                    );

                    if($stmt->execute()){
                        $recipe_id = $stmt->insert_id;
                        $stmt->close();
        
                        //insert ingredients
                        $ingredients_length = count($ingredients);

                        for($i=0; $i<$ingredients_length; $i++){

                            if($ingredients[$i]['name']!=""){
                                $ingredients[$i]['name'] = ucfirst(strip_tags($ingredients[$i]['name']));
                                $stmt = $db->prepare("insert into ingredients values(DEFAULT,?,?,?,?)");
                                $stmt->bind_param(
                                    "sssi",
                                    $ingredients[$i]['name'],
                                    $ingredients[$i]['type'],
                                    $ingredients[$i]['quantity'],
                                    $recipe_id
                                );
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
        
                        //insert step descriptions
                        $steps_length = count($steps);

                        for($i=0; $i<$steps_length; $i++){
                            $description = ucfirst(strip_tags(mysqli_real_escape_string($db, $steps[$i]['description'])));
                            $step_images_length = count($steps[$i]['images']);

                            if(strlen($description)!=0 || $step_images_length!=0){ //per non inserire step vuoti
                                $stmt = $db->prepare("insert into preparation_steps values(DEFAULT,?,?,?)");
                                $stmt->bind_param(
                                    "isi",
                                    $i,
                                    $description,
                                    $recipe_id
                                );
                                $stmt->execute();
                                $step_id = $stmt->insert_id;
                                $stmt->close();
            
                                //insert step images for current step
                                for($k=0; $k<$step_images_length; $k++){
                                    $step_image_path = saveMedia($steps[$i]['images'][$k], "IMG");
            
                                    if(isset($step_image_path)){
                                        $stmt = $db->prepare("insert into step_images values(DEFAULT,?,?,?)");
                                        $stmt->bind_param(
                                            "sii",
                                            $step_image_path,
                                            $k,
                                            $step_id
                                        );
                                        $stmt->execute();
                                        $stmt->close();
                                    }
                                    
                                }
                            }
                        }

                        echo 'success='.$recipe_id;

                    }
                    else{
                        $stmt->close();//close last statement

                        //remove post created because there is no recipe linked to the post
                        $stmt = $db->prepare("DELETE FROM posts WHERE post_id=?");
                        $stmt->bind_param("s",$post_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }

        $db->close();
    }

    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }

?>