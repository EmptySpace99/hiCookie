<?php 

include("config.php");
session_start();

try{
    if(isset($_SESSION['user_id'])){

        //remove all user media from local storage
        $stmt = $db->prepare("
        select DISTINCT * from(
            SELECT user_image as mediafile from users where user_id=?

            union all select cover_image as mediafile from users where user_id=?

            union all select recipe_image as mediafile from recipes 
                join posts on recipes.post_id=posts.post_id where posts.user_id=?

            union all select mediapath as mediafile from posts where user_id=?

            union all select step_image as mediafile from step_images 
                join preparation_steps on preparation_steps.step_id=step_images.step_id 
                join recipes on recipes.recipe_id=preparation_steps.recipe_id 
                join posts on posts.post_id=recipes.post_id where user_id = ?
        ) as q");

        $stmt->bind_param(
            "sssss",
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id']
        );
        if($stmt->execute()){
            $result =  $stmt->get_result();

            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                if(
                    isset($row['mediafile']) &&
                    $row['mediafile']!="../images/profile_image.jpg" && 
                    file_exists($row['mediafile'])
                ){
                    unlink($row['mediafile']);
                }
            }
            $stmt->close();
        
            //remove profile
            $stmt = $db->prepare("delete from users where user_id=?");
            $stmt->bind_param("i",$_SESSION['user_id']);
            
            if($stmt->execute()){
                echo "success";
            }
            $stmt->close();
        }
        else
            $stmt->close();
        
    }
    $db->close(); 
}
catch(Exception $e){
    $db->close();
    die('Caught exception: '.$e->getMessage());
}


?>