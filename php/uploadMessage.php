<?php
    include("config.php");
    session_start();
    
    $to_user_id= isset($_POST['to_user_id']) ? mysqli_real_escape_string($db, $_POST['to_user_id']) : null;
    $chat_message= isset($_POST['chat_message']) ? mysqli_real_escape_string($db, $_POST['chat_message']) : null; //strip tags remove also "<3" for example

    try{

        if(isset($_SESSION['user_id']) && isset($chat_message) && $chat_message!=''){

            if(strlen($chat_message)>1000){

                $message_splitted = str_split($chat_message, 1000); //split string in substrings of length 1000
            }

            if(isset($message_splitted)){

                $len = count($message_splitted);

                for($i=0; $i<$len; $i++){

                    $stmt = $db->prepare("insert into chat_messages values(DEFAULT,?,?,?,DEFAULT);");
                    $stmt->bind_param(
                        "iis",
                        $_SESSION['user_id'], 
                        $to_user_id, 
                        $message_splitted[$i]
                    );
                    $stmt->execute();
                    $stmt->close();
                }
            }
            else{
                $stmt = $db->prepare("insert into chat_messages values(DEFAULT,?,?,?,DEFAULT);");
                $stmt->bind_param(
                    "iis",
                    $_SESSION['user_id'], 
                    $to_user_id, 
                    $chat_message
                );
                $stmt->execute();
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