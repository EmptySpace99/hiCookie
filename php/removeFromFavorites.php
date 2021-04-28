<?php
    include("config.php");
    session_start();

    $post_id = isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;

    try{
        if(isset($_SESSION['user_id'])){

            $stmt = $db->prepare("delete from favorites where user_id=? and post_id=?");
            $stmt->bind_param("ss",$_SESSION['user_id'], $post_id);

            if($stmt->execute())
                echo('success='.$post_id);

            $stmt->close();
        }

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
    
?>