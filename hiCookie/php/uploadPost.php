<?php
    include("config.php");
    include("saveMedia.php");
    session_start();
    
    $mediafile = isset($_POST['mediafile']) ? mysqli_real_escape_string($db, $_POST['mediafile']) : null;
    $mediatype = isset($_POST['mediatype']) ? mysqli_real_escape_string($db, $_POST['mediatype']) : null;
    $description = isset($_POST['description']) ? mysqli_real_escape_string($db, $_POST['description']) : null;
    $title = isset($_POST['title']) ? mysqli_real_escape_string($db, $_POST['title']) : null;

    try{
        if(isset($title) && !(isset($mediafile) || isset($description))){
            $db->close();
            die("You can't create post with only title");
        }

        if(isset($_SESSION['user_id'])){

            if(isset($description) && strlen($description)>1000){
                $db->close();
                die("Description length is longer than 1000 characters");
            }

            $path = saveMedia($mediafile, $mediatype);

            $stmt = $db->prepare("insert into posts values(DEFAULT,?,?,?,?,?,DEFAULT);");
            $stmt->bind_param("sssss",$_SESSION['user_id'],$title,$path,$mediatype,$description);
            
            if($stmt->execute())
                echo('success');
            $stmt->close();
        }
        $db->close();
    }
    
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.  $e->getMessage());
    }
    
?>