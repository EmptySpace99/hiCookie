<?php
    include("config.php");
    session_start();

    $comments=[];

    try{
        if(isset($_SESSION['user_id'])){
            $stmt = $db->prepare(
                "select comment_content, user_image, firstname, 
                lastname, users.user_id, post_id from comments 
                join users on comments.user_id=users.user_id
                where comments.created_at>(SELECT comment_time from users where user_id=?) order by comments.created_at desc"
            );
            $stmt->bind_param("i",$_SESSION['user_id']);
            
            if($stmt->execute()){
                $result =  $stmt->get_result();

                while($row = $result->fetch_array(MYSQLI_ASSOC)){

                    array_push($comments,array(
                        'comment_content'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['comment_content']))),
                        'user_id'=>$row['user_id'],
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_image'=>$row['user_image'],
                        'post_id'=>$row['post_id'],
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