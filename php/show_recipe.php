<?php 

include("config.php");
include('error404.php');
session_start();

$recipe_id = isset($_GET['recipe']) ? mysqli_real_escape_string($db, $_GET['recipe']) : null;
$recipeInfo = [];
$ingredients=[];
$step_descriptions=[];
$step_images=[];
$steps=[];
$filtered_images=[];

try {
    if(isset($recipe_id)){
        //get recipe
        $stmt = $db->prepare("select * from recipes where recipe_id=?");
        $stmt->bind_param("s",$recipe_id);

        if($stmt->execute()){
            $result =  $stmt->get_result();
            $recipe_basic_info = $result->fetch_array(MYSQLI_ASSOC);
            $stmt->close();

            if($result->num_rows==0){ //if recipe doesn't exist return 404
                error404($db);
            }

            //get ingredients
            $stmt = $db->prepare("select * from ingredients where recipe_id=?");
            $stmt->bind_param("s",$recipe_id);

            if($stmt->execute()){
                $result =  $stmt->get_result();
                $stmt->close();

                
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($ingredients, array(
                        'name'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['name']))),
                        'quantity'=>$row['quantity'],
                        'type'=>$row['type']
                    ));
                }
            }
            else
                $stmt->close();

            //get preparation steps
            $stmt = $db->prepare("select * from preparation_steps where recipe_id=? order by step_num");
            $stmt->bind_param("s",$recipe_id);

            if($stmt->execute()){
                $result =  $stmt->get_result();
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($step_descriptions, array(
                        'id'=>$row['step_id'],
                        'num'=>$row['step_num'],
                        'description'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['step_description'])))
                    ));
                }
            }
            $stmt->close();
            
            //get step images
            $stmt = $db->prepare(
                "select step_images.* from step_images join preparation_steps 
                on preparation_steps.step_id=step_images.step_id 
                where preparation_steps.recipe_id=? order by step_image_num"
            );
            $stmt->bind_param("s",$recipe_id);

            if($stmt->execute()){
                $result =  $stmt->get_result();
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    array_push($step_images, array(
                        'id'=>$row['step_id'],
                        'num'=>$row['step_image_num'],
                        'step_image'=>$row['step_image']
                    ));
                }
            }
            $stmt->close();

            //filter step images
            $steps_len = count($step_descriptions);
            $step_images_len = count($step_images);

            for($i=0; $i<$steps_len; $i++){

                for($k=0; $k<$step_images_len; $k++){

                    if($step_images[$k]["id"]==$step_descriptions[$i]['id'])
                        array_push($filtered_images, $step_images[$k]["step_image"]);
                }
                
                array_push($steps, array(
                    'description'=> $step_descriptions[$i]['description'],
                    'step_images'=> $filtered_images 
                ));

                $filtered_images = array();
            }

        }
    }
    else{
        error404($db);
    }
    $db->close();
}

catch(Exception $e){
    $db->close();
    die('Caught exception: '.  $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/recipes.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
    <link rel="stylesheet" type="text/css" href="../css/notifications.css">
    <link rel="stylesheet" type="text/css" href="../css/footer.css">
    <link rel="stylesheet" type="text/css" href="../css/footer-mq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset=utf-8 />
    <title> hiCookie</title>
  </head>
  <body>

    <nav class="nav-bar between">
        <div class="title">
          <a href="../index.html">hiCookie</a>
          <i class="fal fa-cookie-bite"></i>
        </div>
        <div class="block-box invisible">
          <a href="home.php"><i class="fas fa-home-lg-alt"></i></a>
          <p>Home</p>
        </div>
        <div class="block-box invisible">
            <a href="create-recipe.php"> <i class="fas fa-upload"></i> </a>
            <p>Upload</p>
        </div>
        <div class="block-box invisible">
          <a href="chat.php"> <i class="fas fa-comments"></i> </a>
          <p>Chat</p>
        </div>
        <div class="block-box invisible">
          <a  href="<?php
          if(isset($_SESSION['user_id'])) 
            echo "show_profile.php?user_id=".$_SESSION['user_id'];
          else
            echo "login_form.php";
        ?>"> <i class="fas fa-user"></i> </a>
          <p>Account</p>
        </div>
        <div class="block-box invisible">
          <a href="logout.php"> <i class="fas fa-sign-out-alt"></i></a>
          <p>Logout</p>
        </div>
        <form action="search.php" method="get" class="search-bar invisible">
          <input type="text" name="search_content" placeholder="Search friends or recipes..">
          <button class="fa fa-search"></button>
        </form>
        <div class="hidden-menu-icon" onclick="document.body.classList.toggle('active')">
          <span></span>
          <span></span>
          <span></span>
        </div>
    </nav>
  
  
    <div class="hidden-menu">
        <div>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </div>
        <form action="search.php" method="get" class="search-bar invisible">
            <input type="text" name="search_content" placeholder="Search friends or recipes..">
            <button class="fa fa-search"></button>
        </form>
    </div>    
  
  
    <nav class="nav-bar mobile-menu">
        <div>
          <a href="home.php"><i class="fas fa-home-lg-alt"></i></a>
        </div>
        <div>
            <a href="create-recipe.php"> <i class="fas fa-upload"></i> </a>
        </div>
        <div>
          <a href="chat.php"> <i class="fas fa-comments"></i> </a>
        </div>
        <div>
          <a  href="<?php
          if(isset($_SESSION['user_id'])) 
            echo "show_profile.php?user_id=".$_SESSION['user_id'];
          else
            echo "login_form.php";
        ?>"> <i class="fas fa-user"></i> </a>
        </div>
    </nav>
      
      <div class=flexible-box>
         <div class=recipe-box>
            <div id="recipeTitle" class=recipe-title>
            <?php 
            if(isset($recipe_basic_info['recipe_title']))
                echo stripslashes(str_replace("\\n","<br>",htmlentities($recipe_basic_info['recipe_title'])));
            ?></div>
            <div class="inline-box center-box">
               <div class=recipe-img>
                  <?php 
                  if(isset($recipe_basic_info['recipe_image']))
                    echo "<img id=\"recipeImage\" src=".$recipe_basic_info['recipe_image']." alt=\"recipe-img\">";
                  ?>
               </div>
               <div class=column-box>
                  <div  class=inline-box>
                     <i class="fal fa-clock"></i>
                     <span>Preparation: <strong id="preparationTime">
                     <?php
                     if(isset($recipe_basic_info['preparation_time']))
                        echo $recipe_basic_info['preparation_time'];
                     ?></strong></span>
                  </div>
                  <div  class=inline-box>
                     <i class="fal fa-clock"></i>
                     <span>Cooking: <strong id="cookingTime">
                     <?php
                     if(isset($recipe_basic_info['cooking_time']))
                        echo $recipe_basic_info['cooking_time'];
                     ?></strong></span>
                  </div>
                  <div  class=inline-box>
                     <i class="fal fa-hat-chef"></i>
                     <span>Difficulty: <strong id="difficulty">
                     <?php
                      if(isset($recipe_basic_info['difficulty']))
                        echo $recipe_basic_info['difficulty'];
                     ?></strong></span>
                  </div>
                  <div  class=inline-box>
                     <i class="fal fa-sack-dollar"></i>
                     <span>Cost: <strong id="cost">
                     <?php
                      if(isset($recipe_basic_info['cost']))
                        echo $recipe_basic_info['cost'];
                     ?></strong></span>
                  </div>
                  <div  class=inline-box>
                     <i class="fal fa-users"></i>
                     <span>Ingredients for: <strong id="ingredientsForPeople">
                     <?php
                      if(isset($recipe_basic_info['for_people']))
                        echo $recipe_basic_info['for_people'];
                     ?></strong></span>
                  </div>
               </div>
            </div>
            <div class=section-title>Curiosity</div>
            <div class=section-content>
               <p id="curiosity">
               <?php
                if(isset($recipe_basic_info['more_information']))
                    echo stripslashes(str_replace("\\n","<br>",htmlentities($recipe_basic_info['more_information'])));
               ?></p>
            </div>
            <div class=section-title>Ingredients</div>
            <div class=section-content>
               <div class="inline-box">
                  <ul id="ingredients">
                      <?php 
                        $ingredients_len = count($ingredients);

                        for($i=0; $i<$ingredients_len; $i++){
                            echo '<li>'.$ingredients[$i]['name'].': '.$ingredients[$i]['quantity'].' '.$ingredients[$i]['type'].'</li>';
                        }
                      ?>
                  </ul>
               </div>
            </div>
            <div class=section-title>Preparation</div>
            <div id=preparation-steps>
                <?php

                $steps_len = count($steps);
                $stepImages_str="";
                $stepImages_len = 0;

                for($i=0; $i<$steps_len; $i++){

                    $stepImages = $steps[$i]['step_images'];
                    $stepImages_len = count($stepImages);
                    
                    for($k=0; $k<$stepImages_len; $k++){

                        $image_num = $k+1;
                        $stepImages_str .="
                        <div class=step-image>
                            <img src=".$stepImages[$k]." alt='step-image'>
                            <span>". $image_num ."</span>
                        </div>
                        ";
                    }

                    $step_num = $i+1;
                    echo '<div>
                    <div class=section-title>- Step '.$step_num.'</div>
                    <div class=\'section-content column-box\'>
                        <p>'.$steps[$i]['description'].'</p>
                        <div class=\'inline-box space-box\'>'.
                            $stepImages_str
                        .'</div>
                    </div>
                    </div>';
            
                    $stepImages_str='';
                } 
                ?>
            </div>
         </div>
      </div>

        
     <!--FOOTER-->
    <footer>
        <a href="../about.html">About</a>
        <a href="../contact_us.html">Contact us</a>
        <cite>© Copyright 2021. All rights reserved.</cite>
    </footer>

    <script src="../js/update_time.js"></script>
    <script>
        setInterval(()=>{
            updateActiveTime();
        }, 55000);
    </script>
   </body>
</html>