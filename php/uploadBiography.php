<?php
    include("config.php");
    session_start();
    
    $biography = isset($_POST['biography']) ? mysqli_real_escape_string($db, $_POST['biography']) : null;

    try{
        if(isset($_SESSION['user_id']) && isset($biography)){

            if(strlen($biography)>255){
                $db->close();
                die("The biography is longer than 255 characters");
            }

            $stmt = $db->prepare("UPDATE users set biography=? where user_id=?");
            $stmt->bind_param("si",$biography, $_SESSION['user_id']);
            
            if($stmt->execute())
                echo('success='.stripslashes(str_replace("\\n","<br>",htmlentities($biography))));

            $stmt->close();
        }

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>