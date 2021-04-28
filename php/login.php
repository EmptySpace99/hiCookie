<?php
    include("config.php");
    session_start();

    $email = isset($_POST['email']) ? mysqli_real_escape_string($db, $_POST['email']) : null;
    $password = isset($_POST['pass']) ? mysqli_real_escape_string($db, $_POST['pass']) : null;

    try{ 
        if(isset($email)){
            $stmt = $db->prepare("SELECT * FROM users WHERE email=?;");
            $stmt->bind_param("s",$email); 
            
            if($stmt->execute()){
                $result =  $stmt->get_result();
                $num_rows = $result->num_rows;
                $row = $result->fetch_array(MYSQLI_ASSOC);
    
                if($num_rows == 1) {
                    if(password_verify($password, $row['password'])){
                        //check if the user is activated
                        if($row['active']){
                            //check if the user is banned
                            if(!$row['banned']){
                                
                                $_SESSION['user_id'] = $row['user_id'];
                                $_SESSION['firstname'] = $row['firstname'];
                                $_SESSION['lastname'] = $row['lastname'];
                                $_SESSION['email'] = $row['email'];
                                $_SESSION['user_image']=$row['user_image'];
                                
                                echo('success='.$_SESSION['user_id']);
                            }
                            else
                                echo('Error: you are banned!');
                        }
                        else
                            echo('Error: inactive profile, please check your email.');
                    }
                    else
                        echo('Error: wrong password, please try another one.');   
                }
                else
                    echo('Error: no profile with this email, please try another one.');
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