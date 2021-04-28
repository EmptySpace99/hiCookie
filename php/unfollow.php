<?php
    include("config.php");
    session_start();


    try{
        if(
            isset($_SESSION['user_id']) && 
            isset($_SESSION['profile_user_id']) && 
            $_SESSION['user_id'] != $_SESSION['profile_user_id']
        ){

            $stmt = $db->prepare("DELETE FROM followers WHERE following_id=? and follower_id=?");
            $stmt->bind_param("ss", $_SESSION['profile_user_id'], $_SESSION['user_id']);

            if($stmt->execute()){
                echo "success";
            }
            
            $stmt->close();
        }

        $db->close();
    }
    
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
?>