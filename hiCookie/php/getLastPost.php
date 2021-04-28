<?php
    include("config.php");
    session_start();

    $post = [];

    try{
        if(isset($_SESSION['user_id'])){

            //get last post
            $stmt = $db->prepare("SELECT posts.*, user_image, firstname, lastname FROM posts INNER JOIN users ON posts.user_id = users.user_id where users.user_id=? and posts.created_at > active_time order by post_id");
            $stmt->bind_param("i",$_SESSION['user_id']);

            $stmt->execute();
            $result =  $stmt->get_result();
            $num_rows = $result->num_rows;
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if($num_rows == 1) {

                $isOwner = false;

                if(
                    (isset($row['from_user_id']) && 
                    $_SESSION['user_id']==$row['from_user_id']) ||
                    (isset($row['to_user_id']) && 
                    $_SESSION['user_id']==$row['to_user_id']) ||
                    ($row['user_id']==$_SESSION['user_id'] && 
                    !isset($row['to_user_id']) && 
                    !isset($row['from_user_id']))
                ){
                    $isOwner = true;
                }

                array_push($post, array(
                    'post_id'=> $row['post_id'],
                    'title'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['title']))),
                    'mediapath'=>$row['mediapath'],
                    'mediatype'=>$row['mediatype'],
                    'description'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['description']))),
                    'created_at'=>$row['created_at'],
                    'firstname'=>$row['firstname'],
                    'lastname'=>$row['lastname'],
                    'user_id'=>$row['user_id'],
                    'user_image'=>$row['user_image'],
                    'current_user_image'=>$row['user_image'],
                    'isOwner'=> $isOwner,
                    'likes' => 0,
                    'comments' =>0,
                    'shares'=>0
                ));
            }

            $stmt->close();
        }

        echo json_encode($post); //ajax output
        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
    
?>