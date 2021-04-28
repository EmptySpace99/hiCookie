<?php
    include("config.php");
    session_start();

    $postId = isset($_POST['post_id']) ? mysqli_real_escape_string($db, $_POST['post_id']) : null;

    try{
        if(isset($_SESSION['user_id'])){
            //insert like
            $stmt = $db->prepare("insert into likes values(?,?);");
            $stmt->bind_param("ss",$postId,$_SESSION['user_id']);
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