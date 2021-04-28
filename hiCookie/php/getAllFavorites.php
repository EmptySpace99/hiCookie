<?php
    include("config.php");
    session_start();

    $rows=[];

    try{
        if(isset($_SESSION['user_id'])){

            //get user_image of current user
            $stmt = $db->prepare("select * from users where user_id=?");
            $stmt->bind_param("s",$_SESSION['user_id']);
            if($stmt->execute()){
                $result =  $stmt->get_result();
                $row =  $result->fetch_array(MYSQLI_ASSOC);
                $current_user_image = $row['user_image'];
                $stmt->close();
            }

            //get all posts
            $stmt = $db->prepare(
                "select p.post_id, min(user_id) as user_id,  min(firstname) as firstname, 
                min(lastname) as lastname, min(user_image) as user_image, 
                min(title) as title, min(mediapath) as mediapath, min(mediatype) as mediatype, 
                min(description) as description, 
                min(created_at) as created_at, min(likes) as likes, 
                min(like_value) as like_value, min(comments) as comments, min(shares) as shares,min(recipe_id) as recipe_id, 
                case when min(recipe_id) 
                    then true 
                    else false 
                end as isRecipe from (

                    select post_id, min(user_id) as user_id,  min(firstname) as firstname, 
                        min(lastname) as lastname, min(user_image) as user_image, 
                        min(title) as title, min(mediapath) as mediapath, min(mediatype) as mediatype, 
                        min(description) as description, 
                        min(created_at) as created_at, min(likes) as likes, 
                        min(like_value) as like_value, min(comments) as comments, min(shares) as shares from (
                
                        select posts.post_id, posts.user_id, users.firstname, users.lastname, 
                            users.user_image, posts.title, 
                            posts.mediapath, posts.mediatype, posts.description, 
                            posts.created_at, count(likes.post_id) as likes,
                            case when min(likes.user_id)=?
                                then true 
                                else false 
                            end as like_value, null as comments, null as shares from posts
                            join favorites on posts.post_id = favorites.post_id 
                            left join likes ON posts.post_id = likes.post_id 
                            join users ON posts.user_id = users.user_id
                            where favorites.user_id=? group by posts.post_id
                            
                        union all select posts.post_id, null as user_id, null as firstname, 
                            null as lastname, null as user_image, null as title, 
                            null as mediapath, null as mediatype, null as description, 
                            null as created_at, null as likes, null as like_value, 
                            count(comments.post_id) as comments, null as shares from posts 
                            join favorites on posts.post_id = favorites.post_id 
                            left join comments ON posts.post_id = comments.post_id 
                            where favorites.user_id=? group by posts.post_id
                        
                            
                        union all select posts.post_id, null as user_id, null as firstname, 
                            null as lastname, null as user_image, null as title, 
                            null as mediapath, null as mediatype, null as description, 
                            null as created_at, null as likes, null as like_value, 
                            null as comments, count(shares.post_id) as shares from posts
                            join favorites on posts.post_id = favorites.post_id  
                            left join shares ON posts.post_id = shares.post_id 
                            where favorites.user_id=? group by posts.post_id
                    
                    ) as q

                    group by post_id) as p 
                left join recipes on recipes.post_id=p.post_id 
                group by p.post_id;"
            );
            $stmt->bind_param(
                "iiii",
                $_SESSION['user_id'], 
                $_SESSION['user_id'], 
                $_SESSION['user_id'], 
                $_SESSION['user_id']
            );

            if($stmt->execute()){
                $result =  $stmt->get_result();

                while($row = $result->fetch_array(MYSQLI_ASSOC)){

                    array_push($rows,array(
                        'post_id'=> $row['post_id'],
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'user_id'=>$row['user_id'],
                        'title'=>$row['title'],
                        'mediapath'=>$row['mediapath'],
                        'mediatype'=>$row['mediatype'],
                        'description'=>$row['description'],
                        'created_at'=>$row['created_at'],
                        'user_image'=>$row['user_image'],
                        'current_user_image'=>$current_user_image,
                        'likes' => $row['likes'],
                        'like_value' => $row['like_value'],
                        'comments' => $row['comments'],
                        'shares' => $row['shares'],
                        'isFavorite' => true,
                        'recipe_id' => $row['recipe_id'],
                        'isRecipe' => $row['isRecipe']
                    ));
                }
            }
            echo json_encode($rows); //ajax output
                
            $stmt->close();
        }
        
        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
    
?>