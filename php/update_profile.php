<?php 

include("config.php");
include("normalizeChars.php");
include("checkUserInfo.php");
session_start();

$firstname = isset($_POST['firstname']) ? ucfirst(strtolower(normalizeChars(strip_tags(trim(mysqli_real_escape_string($db, $_POST['firstname'])))))) : null;
$lastname = isset($_POST['lastname']) ? ucfirst(strtolower(normalizeChars(strip_tags(trim(mysqli_real_escape_string($db, $_POST['lastname'])))))) : null;
$email = isset($_POST['email']) ? strtolower(trim(mysqli_real_escape_string($db, $_POST['email']))) : null;
$currentPassword = isset($_POST['currentPassword']) ? mysqli_real_escape_string($db, $_POST['currentPassword']) : null;
$newPassword1 = isset($_POST['newPassword1']) ? mysqli_real_escape_string($db, $_POST['newPassword1']) : null;
$newPassword2 = isset($_POST['newPassword2']) ? mysqli_real_escape_string($db, $_POST['newPassword2']) : null;

$checks = array(
    'wantModifyFirstname' =>false,
    'wantModifyLastname' => false,
    'wantModifyEmail' => false,
    'wantModifyPassword' => false,
    'firstnameIsModified' => false,
    'lastnameIsModified' => false,
    'emailIsModified' => false,
    'currentPasswordIsMatched' => false,
    'newPasswordIsMatched' => false,
    'passwordIsModified' => false
);

try{
    checkUserInfo($firstname,$lastname,$email,$db);

    if(isset($_SESSION['user_id'])){

        //change password
        if(
            isset($currentPassword) &&
            $currentPassword!='' && 
            isset($newPassword1) &&
            $newPassword1!='' &&
            isset($newPassword2) &&
            $newPassword2 != ''
        ){

            $checks['wantModifyPassword'] = true;

            if($newPassword1==$newPassword2){

                $checks['newPasswordIsMatched'] = true;
        
                $stmt = $db->prepare("select password from users where user_id=?");
                $stmt->bind_param("i", $_SESSION['user_id']);

                if($stmt->execute()){

                    $result =  $stmt->get_result();
                    $num_rows = $result->num_rows;
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $stmt->close();

                    //check if current password is correct
                    if(password_verify($currentPassword, $row['password'])){

                        $checks['currentPasswordIsMatched'] = true;

                        $password_hash = password_hash($newPassword1, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE users set password=? where user_id=?");
                        $stmt->bind_param("si",$password_hash, $_SESSION['user_id']);

                        if($stmt->execute()){

                            $checks['passwordIsModified'] = true;
                        }

                        $stmt->close();
                    }
                }
                else
                    $stmt->close();

            }
            
        }

        //change firstname
        if(isset($firstname) && $firstname!='' && $firstname!=$_SESSION['firstname']){

            $checks['wantModifyFirstname'] = true;

            $stmt = $db->prepare("UPDATE users set firstname=? where user_id=?");
            $stmt->bind_param("si",$firstname, $_SESSION['user_id']);
            
            if($stmt->execute()){

                $_SESSION['firstname'] = $firstname;
                $checks['firstnameIsModified'] = true;
            }

            $stmt->close();
        }

        //change lastname
        if(isset($lastname) && $lastname!='' && $lastname!=$_SESSION['lastname']){

            $checks['wantModifyLastname'] = true;

            $stmt = $db->prepare("UPDATE users set lastname=? where user_id=?");
            $stmt->bind_param("si", $lastname, $_SESSION['user_id']);
            
            if($stmt->execute()){

                $_SESSION['lastname'] = $lastname;
                $checks['lastnameIsModified'] = true;
            }

            $stmt->close();
        }

        //change email
        if(isset($email) && $email!='' && $email!=$_SESSION['email']){

            $checks['wantModifyEmail'] = true;

            $stmt = $db->prepare("UPDATE users set email=? where user_id=?");
            $stmt->bind_param("si", $email, $_SESSION['user_id']);
            
            if($stmt->execute()){

                $_SESSION['email'] = $email;
                $checks['emailIsModified'] = true;
            }

            $stmt->close();  
        }
    }

    echo json_encode($checks);

    $db->close();
}
catch(Exception $e){
    $db->close();
    die('Caught exception: '.  $e->getMessage());
}


?>