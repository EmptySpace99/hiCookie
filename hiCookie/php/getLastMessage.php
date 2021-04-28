<?php
    include("config.php");
    session_start();

    try{
        if(isset($_SESSION['user_id'])){
            $stmt = $db->prepare(
                "select chat_message, from_user_id, to_user_id, 
                created_at from chat_messages
                where (to_user_id=? or from_user_id=?)
                and created_at>(SELECT chat_time from users where user_id=?)
                order by created_at desc"
            );
            $stmt->bind_param(
                "iii",
                $_SESSION['user_id'],
                $_SESSION['user_id'], 
                $_SESSION['user_id']
            );
            
            if($stmt->execute()){
                $result =  $stmt->get_result();
                $array = [];
                while($row = $result->fetch_array(MYSQLI_NUM)){
                    array_push($array,array(
                        'chat_message'=>stripslashes(str_replace("\\n","<br>",htmlentities($row[0]))),
                        'to_user_id'=>$row[1],
                        'from_user_id'=>$row[2],
                        'created_at'=>$row[3],
                        'current_user_id'=>$_SESSION['user_id'],
                    ));
                }

                echo json_encode($array); //ajax output
            }

            $stmt->close();

        }

        $db->close();

    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>