<?php
    include("config.php");
    session_start();

    try{
        if(isset($_SESSION['user_id'])){
            $stmt = $db->prepare(
                'select following_id as user_id, 
                CASE WHEN active_time > FROM_UNIXTIME((UNIX_TIMESTAMP(now()) - 60)) THEN 1 ELSE 0 END AS user_status 
                FROM users join followers on users.user_id=following_id 
                where follower_id=?
                
                union select follower_id as user_id, 
                CASE WHEN active_time > FROM_UNIXTIME((UNIX_TIMESTAMP(now()) - 60)) THEN 1 ELSE 0 END AS user_status 
                FROM users join followers on users.user_id=follower_id 
                where following_id=?'
            );
            $stmt->bind_param("ii",$_SESSION['user_id'], $_SESSION['user_id']);

            if($stmt->execute()){
                $result =  $stmt->get_result();
                $array=[];

                while($row = $result->fetch_array(MYSQLI_NUM)){
                    array_push($array,array(
                        'user_id'=>$row[0],
                        'status'=>$row[1],
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
        die('Caught exception: '.$e->getMessage());
    }
?>