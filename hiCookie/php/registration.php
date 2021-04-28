<?php
    include("config.php");
    include("normalizeChars.php");
    include("checkUserInfo.php");

    $firstname = isset($_POST['firstname']) ? ucfirst(strtolower(normalizeChars(strip_tags(trim(mysqli_real_escape_string($db, $_POST['firstname'])))))) : null;
    $lastname = isset($_POST['lastname']) ? ucfirst(strtolower(normalizeChars(strip_tags(trim(mysqli_real_escape_string($db, $_POST['lastname'])))))) : null;
    $email = isset($_POST['email']) ? strtolower(trim(mysqli_real_escape_string($db, $_POST['email']))) : null;
    $password1 = isset($_POST['pass']) ? mysqli_real_escape_string($db, $_POST['pass']) : null;
    $password2 = isset($_POST['confirm']) ? mysqli_real_escape_string($db, $_POST['confirm']) : null;

    try{

        checkUserInfo($firstname,$lastname,$email,$db);

            
        if($password1==$password2){
            $password_hash = password_hash($password1, PASSWORD_DEFAULT);
    
            $stmt= $db->prepare("INSERT INTO users VALUES (DEFAULT, ?,?,?,?, DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT,DEFAULT);");
            $stmt->bind_param("ssss", $firstname, $lastname, $email, $password_hash);
            
            if($stmt->execute()){
                echo('success');
            }
            else{
                echo('Error: Email already used.');
            }
            
            $stmt->close();
        }
        else
            echo('Error: Unmatched passwords.');
            

        $db->close();
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
?>