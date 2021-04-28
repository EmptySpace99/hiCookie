<?php
    include("config.php");
    session_start();
    
    $post_id= isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;
    $comment_content= isset($_POST['comment_content']) ? strip_tags(mysqli_real_escape_string($db, $_POST['comment_content'])) : null;

    try{
        if(isset($_SESSION['user_id']) && isset($comment_content)){
            if(strlen($comment_content)>1000){
                $comment_splitted = str_split($comment_content, 1000); //split string in substrings of length 1000
            }
            if(isset($comment_splitted)){
                $len = count($comment_splitted);
                for($i=0; $i<$len; $i++){
                    $stmt = $db->prepare("insert into comments values(DEFAULT,?,?,?,DEFAULT);");
                    $stmt->bind_param("iss",$_SESSION['user_id'], $post_id,  $comment_splitted[$i]);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            else{
                $stmt = $db->prepare("insert into comments values(DEFAULT,?,?,?,DEFAULT);");
                $stmt->bind_param("iss",$_SESSION['user_id'],$post_id, $comment_content);
                if($stmt->execute()){
                    echo 'success';
                }
                $stmt->close();
            }
            
        }
        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }


    
?>