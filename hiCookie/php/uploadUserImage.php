<?php
    include("config.php");
    include("saveMedia.php");
    session_start();
    
    $mediafile = isset($_POST['mediafile']) ? mysqli_real_escape_string($db, $_POST['mediafile']) : '';
    $mediatype = isset($_POST['mediatype']) ? mysqli_real_escape_string($db, $_POST['mediatype']) : '';

    try{

        if($mediafile!='' && isset($_SESSION['user_id'])){
            
            if($mediatype=="IMG"){
                $path=saveMedia($mediafile, $mediatype);

                if(isset($path)){
                    //delete last user_image
                    $stmt = $db->prepare("select user_image from users where user_id=?");
                    $stmt->bind_param("s", $_SESSION['user_id']);

                    if($stmt->execute()){

                        $result = $stmt->get_result();
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $stmt->close();

                        if($row['user_image']!='../images/profile_image.jpg' && file_exists($row['user_image'])){
                            
                            unlink($row['user_image']);
                        }

                        //add new user image
                        $stmt = $db->prepare("UPDATE users set user_image=? where user_id=?");
                        $stmt->bind_param("si",$path, $_SESSION['user_id']);

                        if($stmt->execute()){

                            $_SESSION['user_image']=$path;
                            echo 'success='.$_SESSION['user_image'];
                        }
                        $stmt->close();
                    }
                    else
                        $stmt->close();
                    
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