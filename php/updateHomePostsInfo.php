<?php
    include("config.php");
    session_start();

    $posts=[];

    try{
        if(isset($_SESSION['profile_user_id'])){

            $stmt = $db->prepare(
                "SELECT IFNULL(min(post_id),0) as post_id, IFNULL(min(likes),0) as likes,
                IFNULL(min(comments),0) as comments, IFNULL(min(shares),0) as shares from(
            
                    SELECT count(*) as shares, null as comments, null as likes, shares.post_id as post_id 
                    from(SELECT distinct shares.post_id as post_id FROM `shares` where to_user_id=?) as s
                    join shares on shares.post_id=s.post_id group by shares.post_id
            
                    union all select null as shares, count(*) as comments, null as likes, comments.post_id as post_id 
                    from(SELECT distinct shares.post_id as post_id FROM `shares` where to_user_id=?) as c
                    join comments on comments.post_id=c.post_id group by comments.post_id
            
                    union all select null as shares, null as comments, count(*) as likes,likes.post_id as post_id 
                        from(SELECT distinct shares.post_id as post_id FROM `shares` where to_user_id=?) as l
                        join likes on likes.post_id=l.post_id group by likes.post_id) as d

                group by d.post_id HAVING d.post_id IS NOT NULL

                union all select l.post_id, count(likes.post_id) as likes, comments, shares from(
    
                    select p.post_id, comments, count(shares.post_id) as shares from(

                        select q.post_id, count(comments.post_id) as comments from(
                            select distinct posts.* from posts 
                            left join followers on followers.following_id = posts.user_id 
                            where posts.user_id=? or followers.follower_id = ?) as q
                    
                    left join comments on comments.post_id=q.post_id
                    group by q.post_id) as p

                left join shares on shares.post_id=p.post_id
                group by p.post_id) as l

                left join likes on likes.post_id=l.post_id
                group by l.post_id
            ");

            $stmt->bind_param(
                "iiiii",
                $_SESSION['user_id'],
                $_SESSION['user_id'],
                $_SESSION['user_id'],
                $_SESSION['user_id'],
                $_SESSION['user_id']
            );

            if($stmt->execute()){
                $result =  $stmt->get_result();
                $stmt->close();
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
    
                    array_push($posts,array(
                        'post_id'=> $row['post_id'],
                        'likes' => $row['likes'],
                        'comments' => $row['comments'],
                        'shares' => $row['shares'],
                    ));
                }
            }
        }
        echo json_encode($posts); //ajax output

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }

   
?>