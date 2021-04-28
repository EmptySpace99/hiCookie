<?php

include("checkValidCharacters.php");

function checkUserInfo($firstname, $lastname, $email, $db){

    if(isset($firstname)){

        if(strlen($firstname)>255){

            $db->close();
            die("Error: Firstname is longer than 255 characters");
        }

        
        $bad_character = checkValidCharacters($firstname);

        if(isset($bad_character)){

            $db->close();
            die("Error: Found bad character in firstname: ".$bad_character);
        }
    }

    if(isset($lastname)){
        
        if(strlen($lastname)>255){

            $db->close();
            die("Error: Lastname is longer than 255 characters");
        }

        
        $bad_character = checkValidCharacters($lastname);

        if(isset($bad_character)){

            $db->close();
            die("Error: Found bad character in lastname: ".$bad_character);
        }
        
    }

    if(isset($email)){

        if(strlen($email)>255){

            $db->close();
            die("Error: Email is longer than 255 characters");
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

            $db->close();
            die("Error: Invalid email format");
        }
    }
}


?>