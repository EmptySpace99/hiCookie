<?php
    include("config.php");
    session_start();

    $post_id = isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;

    try{
        if(isset( $_SESSION['user_id'])){

            $stmt = $db->prepare("DELETE FROM likes WHERE post_id=? and user_id=?");
            $stmt->bind_param("ss", $post_id, $_SESSION['user_id']);

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