<?php

function getPosts($user_id, $db){
    $posts=[];
    
    if(isset($_SESSION['user_id'])){

        //get all posts
        $stmt = $db->prepare(
            "select min(p.post_id) as post_id, min(from_user_firstname) as from_user_firstname, min(from_user_lastname) as from_user_lastname, 
            min(from_user_id) as from_user_id, min(to_user_id) as to_user_id, min(p.user_id) as user_id, min(user_image) as user_image, 
            min(firstname) as firstname, min(lastname) as lastname, min(title) as title, 
            min(mediapath) as mediapath, min(mediatype) as mediatype, 
            min(description) as description, 
            min(created_at) as created_at, IFNULL(min(likes), 0) as likes, 
            IFNULL(min(like_value),0) as like_value, IFNULL(min(comments),0) as comments, 
            IFNULL(min(shares),0) as shares, 
            case when min(favorites.user_id) = ?
                then true 
                else false 
            end as isFavorite, min(recipe_id) as recipe_id,
            min(isRecipe) as isRecipe from(

                select min(k.post_id) as post_id, min(users.firstname) as from_user_firstname, min(users.lastname) as from_user_lastname,
                min(k.from_user_id) as from_user_id, min(k.to_user_id) as to_user_id, min(k.user_id) as user_id, 
                min(k.user_image) as user_image, min(k.firstname) as firstname, min(k.lastname) as lastname, min(title) as title, 
                min(mediapath) as mediapath, min(mediatype) as mediatype, 
                min(description) as description, 
                min(k.created_at) as created_at, min(likes) as likes, 
                min(like_value) as like_value, min(comments) as comments, 
                min(shares) as shares, null as isFavorite, min(recipe_id) as recipe_id,
                min(isRecipe) as isRecipe from(
                
                    select min(q.post_id) as post_id, min(users.user_id) as user_id, min(user_image) as user_image, min(firstname) as firstname, 
                    min(lastname) as lastname,
                    min(title) as title, 
                    min(mediapath) as mediapath, min(mediatype) as mediatype, 
                    min(description) as description, 
                    min(posts.created_at) as created_at, min(likes) as likes, 
                    min(like_value) as like_value, min(comments) as comments, 
                    min(shares) as shares, 
                    min(from_user_id) as from_user_id, min(to_user_id) as to_user_id,
                    min(recipes.recipe_id) as recipe_id,
                    CASE WHEN min(recipes.recipe_id) is not null 
                        then true 
                        else false 
                    end as isRecipe from(
        
                        SELECT min(shares) as shares, min(comments) as comments, min(likes) as likes, min(like_value) as like_value, min(post_id) as post_id, min(from_user_id) as from_user_id, min(to_user_id) as to_user_id from(
        
                            SELECT count(*) as shares, null as comments, null as likes, null as like_value, shares.post_id as post_id, min(s.from_user_id) as from_user_id, min(s.to_user_id) as to_user_id
                            from(SELECT * FROM `shares` where to_user_id=?) as s
                            join shares on shares.post_id=s.post_id group by s.post_id, s.from_user_id, s.to_user_id
                    
                            union all select null as shares, count(*) as comments, null as likes, null as like_value, min(comments.post_id) as post_id, c.from_user_id, c.to_user_id
                            from(SELECT * FROM `shares` where to_user_id=?) as c
                            join comments on comments.post_id=c.post_id group by c.post_id, c.from_user_id, c.to_user_id
                        
                            union all select min(shares) as shares, min(comments) as comments, min(likes) likes, max(case when to_user_id =? then true else false end) as like_value, 
                            min(f.post_id) as post_id,  min(from_user_id) as from_user_id, min(to_user_id) as to_user_id from(
                                select null as shares, null as comments, count(*) as likes,likes.post_id as post_id, l.from_user_id, l.to_user_id
								from(SELECT * FROM `shares` where to_user_id=?) as l
                                join likes on likes.post_id=l.post_id GROUP BY l.post_id, l.from_user_id, l.to_user_id) as f 
    							join likes on likes.post_id=f.post_id GROUP BY f.post_id, f.from_user_id, f.to_user_id) as g
                        group by post_id, from_user_id, to_user_id) as q      
    
                    join posts on posts.post_id=q.post_id 
                    join users on users.user_id=posts.user_id
                    left join recipes on recipes.post_id=q.post_id
                    group by q.post_id,q.from_user_id, q.to_user_id) as k
    
                join users on users.user_id=k.from_user_id
                group by k.post_id, users.user_id, k.to_user_id, k.from_user_id) as p
            left join favorites on favorites.post_id=p.post_id
            group by p.post_id,p.from_user_id, p.to_user_id
            
            union all select f.post_id, null as from_user_firstname, null as from_user_lastname, null as from_user_id, null as to_user_id,
            min(f.user_id) as user_id , min(user_image) as user_image, min(firstname) as firstname, min(lastname) as lastname , min(title) as title, 
            min(mediapath) as mediapath, min(mediatype) as mediatype , min(description) as description, min(created_at) as created_at ,min(likes) as likes ,min(like_value) as like_value , min(comments) as comments, min(shares) as shares, 
            case when min(favorites.user_id)=?
                then true 
                else false 
            end as isFavorite, min(recipe_id) as recipe_id,
            min(isRecipe) as isRecipe from(

                select min(s.post_id) as post_id, null as from_user_firstname, null as from_user_lastname,
                null as from_user_id, null as to_user_id, min(s.user_id) as user_id, min(user_image) as user_image, min(firstname) as firstname, 
                min(lastname) as lastname , min(title) as title, 
                min(mediapath) as mediapath, min(mediatype) as mediatype, min(description) as description, min(created_at) as created_at, min(likes) as likes, min(shares) as shares, min(comments) as comments, min(recipe_id) as recipe_id, min(isRecipe) as isRecipe,
                case when min(likes.user_id)=?
                    then true 
                    else false 
                end as like_value from(

                    select min(t.post_id) as post_id, null as from_user_firstname, null as from_user_lastname,
                    null as from_user_id, null as to_user_id, min(users.user_id) as user_id, min(user_image) as user_image, 
                    min(firstname) as firstname, min(lastname) as lastname, min(title) as title, 
                    min(mediapath) as mediapath, min(mediatype) as mediatype, min(description) as description , 
                    min(posts.created_at) as created_at, min(likes) as likes, min(shares) as shares, min(comments) as comments, min(recipe_id) as recipe_id,
                    CASE WHEN min(recipes.recipe_id) is not null 
                    then true 
                    else false 
                    end as isRecipe from(

                        select min(c.post_id) as post_id, count(likes.post_id) as likes, min(comments) as comments, min(shares) as shares from(

                            select min(b.post_id) as post_id, count(comments.post_id) as comments, min(shares) as shares from(

                                select min(a.post_id) as post_id, count(shares.post_id) as shares
                                from(select * from posts where posts.user_id=?) as a

                                left join shares on shares.post_id=a.post_id GROUP BY a.post_id) as b 

                            left join comments on comments.post_id=b.post_id GROUP BY b.post_id) as c

                        left join likes on likes.post_id=c.post_id GROUP BY c.post_id) as t
                            
                    join posts on posts.post_id=t.post_id 
                    join users on users.user_id=posts.user_id
                    left join recipes on recipes.post_id=t.post_id
                    group by t.post_id) as s
                            
                left join likes on likes.post_id=s.post_id
                group by s.post_id) as f
                
            left join favorites on favorites.post_id=f.post_id
            group by f.post_id order by created_at;"
        );
        
        $stmt->bind_param(
            "iiiiiiii",
            $_SESSION['user_id'],
            $user_id, 
            $user_id,
            $_SESSION['user_id'],
            $user_id,
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $user_id
        );

        if($stmt->execute()){
            $result =  $stmt->get_result();
            $stmt->close();
            
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                $isOwner = false;

                if(
                    (isset($row['from_user_id']) && 
                    $_SESSION['user_id']==$row['from_user_id']) ||
                    (isset($row['to_user_id']) && 
                    $_SESSION['user_id']==$row['to_user_id']) ||
                    ($row['user_id']==$_SESSION['user_id'] && 
                    !isset($row['to_user_id']) && 
                    !isset($row['from_user_id']))
                ){
                    $isOwner = true;
                }
            

                array_push($posts,array(
                    'post_id'=> $row['post_id'],
                    'title'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['title']))),
                    'mediapath'=>$row['mediapath'],
                    'mediatype'=>$row['mediatype'],
                    'description'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['description']))),
                    'created_at'=>$row['created_at'],
                    'firstname'=>$row['firstname'],
                    'lastname'=>$row['lastname'],
                    'user_id'=>$row['user_id'],
                    'user_image'=>$row['user_image'],
                    'current_user_image'=>$_SESSION['user_image'],
                    'likes' => $row['likes'],
                    'like_value' => $row['like_value'],
                    'comments' => $row['comments'],
                    'shares' => $row['shares'],
                    'isFavorite' => $row['isFavorite'],
                    'recipe_id' => $row['recipe_id'],
                    'isRecipe' => $row['isRecipe'],
                    'from_user_firstname'=> $row['from_user_firstname'],
                    'from_user_lastname'=> $row['from_user_lastname'],
                    'from_user_id'=> $row['from_user_id'],
                    'to_user_id'=>$row['to_user_id'],
                    'isOwner'=> $isOwner
                ));
            }
        }
    }

    return $posts;
}
    
?>