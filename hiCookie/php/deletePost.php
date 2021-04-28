<?php
    include("config.php");
    session_start();
    
    $post_id = isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;
    $shared_by = isset($_POST['shared_by']) ? mysqli_real_escape_string($db, $_POST['shared_by']) : null;
    $shared_to = isset($_POST['shared_to']) ? mysqli_real_escape_string($db, $_POST['shared_to']) : null;

    try{
        if(isset($_SESSION['user_id'])){

            if(isset($post_id)){

                //if post is shared: delete just share row
                if(
                    isset($shared_by) &&
                    isset($shared_to) &&
                    ($shared_by == $_SESSION['user_id'] ||
                    $shared_to == $_SESSION['user_id'])
                ){
                    $stmt = $db->prepare("DELETE FROM shares WHERE post_id=? and from_user_id=? and to_user_id=?");
                    $stmt->bind_param("sss",$post_id, $shared_by, $shared_to);

                    if($stmt->execute())
                        echo('success');

                    $stmt->close();
                }
                //else delete directly the post
                else{
                    //remove mediafile
                    $stmt = $db->prepare("SELECT mediapath FROM posts WHERE post_id=? and user_id=?");
                    $stmt->bind_param("ss",$post_id, $_SESSION['user_id']);

                    if($stmt->execute()){
                        $result =  $stmt->get_result();
                        $row = $result->fetch_array(MYSQLI_NUM);
                        
                        if(file_exists($row[0]))
                            unlink($row[0]); //to delete mediafile if exists
                    }
                    $stmt->close();

                    $stmt = $db->prepare("DELETE FROM posts WHERE post_id=? and user_id=?");
                    $stmt->bind_param("si",$post_id,  $_SESSION['user_id']);
                    
                    if($stmt->execute())
                        echo('success');

                    $stmt->close();
                }
            }
            
        }
        $db->close(); 
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
    
?>