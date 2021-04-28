<?php
    include("config.php");
    session_start();

    $post_id = isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;
    $to_user_id = isset($_POST['to_user_id']) ? mysqli_real_escape_string($db, $_POST['to_user_id']) : null;

    try{
        if(
            isset($_SESSION['user_id']) && 
            isset($post_id) && 
            isset($to_user_id)
        ){

            //check if we are sharing the post with the owner
            $stmt = $db->prepare("select * from posts where post_id=? and user_id=?");
            $stmt->bind_param("ii", $post_id, $to_user_id);

            if($stmt->execute()){
                $result =  $stmt->get_result();
                $stmt->close();

                if($result->num_rows==0){
                    //if he isn't the post's owner share the post
                    $stmt = $db->prepare("insert into shares values(?,?,?);");
                    $stmt->bind_param("iii", $to_user_id, $_SESSION['user_id'], $post_id);
                    if($stmt->execute()){
                        echo "Shared!";
                    }
                    else
                        echo "Already shared!";
                    
                    $stmt->close();
                }
                else
                    echo "You can't share post with the post's owner!";
            }
            else
                $stmt->close();
        }
        $db->close();
    }
    
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }

?>