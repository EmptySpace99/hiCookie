<?php
    include("config.php");
    session_start();

    try{
        if(isset($_SESSION['user_id'])){

            if($stmt = $db->prepare("UPDATE users set chat_time=current_timestamp() where user_id=?")){
                $stmt->bind_param("i",$_SESSION['user_id']);
                
                if($stmt->execute()){
                    echo 'updated chat_time';
                }
                $stmt->close();
            }
        }

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>