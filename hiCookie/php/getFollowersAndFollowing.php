<?php
    
    function getFollowersAndFollowing($user_id, $db){
        $followers=[];

        if(isset($_SESSION['user_id']) && isset($user_id)){

            //get all followers
            $stmt = $db->prepare(
                "SELECT firstname, lastname, user_image, user_id
                FROM users
                where users.user_id in (SELECT follower_id from followers where following_id=?);"
            );
            $stmt->bind_param("s",$user_id);

            if($stmt->execute()){

                $result =  $stmt->get_result();
                $num_rows = $result->num_rows;

                while($row = $result->fetch_array(MYSQLI_ASSOC)){

                    array_push($followers,array(
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_image'=>$row['user_image'],
                        'user_id'=>$row['user_id'],
                        'follower_value'=>true
                    ));
                }
            }

            $stmt->close();
            

            //get all following
            $stmt = $db->prepare(
                "SELECT firstname, lastname, user_image, user_id
                FROM users
                where users.user_id in (SELECT following_id from followers where follower_id=?);"
            );
            $stmt->bind_param("s",$user_id);

            if($stmt->execute()){

                $result =  $stmt->get_result();

                while($row = $result->fetch_array(MYSQLI_ASSOC)){

                    array_push($followers,array(
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_image'=>$row['user_image'],
                        'user_id'=>$row['user_id'],
                        'follower_value'=>false
                    ));
                }
            }

            $stmt->close();
        }

        return $followers;
    
    }
    
?>