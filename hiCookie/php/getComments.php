<?php
    include("config.php");
    session_start();
    
    $post_id= isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;
    $comments = [];

    try{
        if(isset($_SESSION['user_id'])){
            $stmt = $db->prepare(
                "select comment_content, user_image, firstname, 
                lastname, users.user_id from comments 
                join users on comments.user_id=users.user_id 
                where post_id=?"
            ); 
            $stmt->bind_param("i",$post_id);

            if($stmt->execute()){
                $result =  $stmt->get_result();
               
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                   
                    array_push($comments,array(
                        'comment_content'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['comment_content']))),
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_id'=>$row['user_id'],
                        'user_image'=>$row['user_image'],
                    ));
                }
            }

            $stmt->close();
        }
        echo json_encode($comments); //ajax output
        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>