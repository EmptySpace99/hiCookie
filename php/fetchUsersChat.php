<?php
    include("config.php");
    session_start();

    $users = [];

    try{
        if(isset($_SESSION['user_id'])){
            $stmt = $db->prepare(
                "select user_id, firstname, lastname, user_image from users 
                    join followers on user_id=following_id
                    where follower_id=?
                union select user_id, firstname, lastname, user_image from users 
                    join followers on user_id=follower_id
                    where following_id=?"
            );
            $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);

            if($stmt->execute()){
                $result =  $stmt->get_result();

                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    

                    array_push($users,array(
                        'user_id'=>$row['user_id'],
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_image'=> $row['user_image'],
                    ));
                }
            }

            $stmt->close();
        }

        echo json_encode($users); //ajax output

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>