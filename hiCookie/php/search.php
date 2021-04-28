<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/account.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
    <link rel="stylesheet" type="text/css" href="../css/notifications.css">
    <link rel="stylesheet" type="text/css" href="../css/footer.css">
    <link rel="stylesheet" type="text/css" href="../css/footer-mq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset=utf-8>
    <title>hiCookie</title>
  </head>
  <body>

    <nav class="nav-bar between">
        <div class="title">
            <a href="../index.html">hiCookie</a>
            <i class="fal fa-cookie-bite"></i>
        </div>
        <div class="block-box invisible">
            <a href="home.php"><i class="fas fa-home-lg-alt"></i></a>
            <p>Home</p>
        </div>
        <div class="block-box invisible">
            <a href="create-recipe.php"> <i class="fas fa-upload"></i> </a>
            <p>Upload</p>
        </div>
        <div class="block-box invisible">
            <a href="chat.php"> <i class="fas fa-comments"></i> </a>
            <p>Chat</p>
        </div>
        <div class="block-box invisible">
            <a  href="<?php
                if(isset($_SESSION['user_id'])) 
                    echo "show_profile.php?user_id=".$_SESSION['user_id'];
                else
                    echo "login_form.php";
                
                ?>"> <i class="fas fa-user"></i> </a>
            <p>Account</p>
        </div>
        <div class="block-box invisible">
            <a href="logout.php"> <i class="fas fa-sign-out-alt"></i></a>
            <p>Logout</p>
        </div>
        <form action="search.php" method="get" class="search-bar invisible">
            <input type="text" name="search_content" placeholder="Search friends or recipes..">
            <button class="fa fa-search"></button>
        </form>
        <div class="hidden-menu-icon" onclick="document.body.classList.toggle('active')">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
  
  
    <div class="hidden-menu">
        <div>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </div>
        <form action="search.php" method="get" class="search-bar">
            <input type="text" name="search_content" placeholder="Search friends or recipes..">
            <button class="fa fa-search"></button>
        </form>
    </div>
  
  
    <nav class="nav-bar mobile-menu">
        <div>
          <a href="home.php"><i class="fas fa-home-lg-alt"></i></a>
        </div>
        <div>
            <a href="create-recipe.php"> <i class="fas fa-upload"></i> </a>
        </div>
        <div>
          <a href="chat.php"> <i class="fas fa-comments"></i> </a>
        </div>
        <div>
          <a  href="<?php

            if(isset($_SESSION['user_id'])) 
                echo "show_profile.php?user_id=".$_SESSION['user_id'];
            else
                echo "login_form.php";

            ?>"> <i class="fas fa-user"></i> </a>
        </div>
    </nav>

      <div class="flexible-box">
        <div class=posts-box>

        <?php
            include("config.php");
        
            $search_content= isset($_GET['search_content']) ? strip_tags(trim(mysqli_real_escape_string($db, $_GET['search_content']))) : null;
            $users=[];
            $recipes=[];

            try{
                if(strlen($search_content)!=0){
                    $stmt = $db->prepare("
                    select firstname, lastname, user_id,user_image,null as recipe_image, null as recipe_title, 
                        null as recipe_id from users 
                        where concat(users.firstname,' ', users.lastname) like CONCAT( '%',?,'%')
                    union all select null as firstname, null as lastname, 
                        null as user_id, null as user_image,recipe_image, recipe_title, recipe_id from recipes 
                        where recipe_title like CONCAT( '%',?,'%')
                    ");
                    $stmt->bind_param(
                        "ss",
                        $search_content,
                        $search_content
                    );
                    if($stmt->execute()){
                        $result =  $stmt->get_result();
                        if($result->num_rows==0){
                            echo "
                                <div class=center>
                                    <strong style='font-size:30px;'>No match found</strong>
                                </div>
                            ";
                        }
                        else{
                            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                                //create users
                                if(
                                    isset($row['firstname']) && 
                                    isset($row['lastname']) && 
                                    isset($row['user_id']) &&
                                    isset($row['user_image'])
                                    ){
    
                                    echo "
                                    <div class=follower-box>
                                        <a href=\"show_profile.php?user_id=".$row['user_id']."\">
                                            <img src=".$row['user_image']." alt='user_image'>
                                            <strong>".$row['firstname']." ".$row['lastname']."</strong>
                                        </a>
                                    </div>";
    
                                }
                                //create recipes
                                else if(isset($row['recipe_title']) && isset($row['recipe_id'])){

                                    $total_lenght = strlen($row['recipe_title']);
                                    
                                    if($total_lenght>20)
                                        $row['recipe_title'] = substr($row['recipe_title'],0,20).'..';
                                   
                                    echo "
                                    <div class=follower-box>
                                        <a href=\"show_recipe.php?recipe=".$row['recipe_id']."\">
                                            <img src=".$row['recipe_image']." alt='recipe_image'>
                                            <strong>".$row['recipe_title']."</strong>
                                        </a>
                                    </div>";
                                }
                                
                            }
                        }
                       
                    }
                    $stmt->close();
                }
                else{
                    echo "
                        <div class=center>
                            <strong style='font-size:30px;'>No match found</strong>
                        </div>
                    ";
                }
                $db->close();
            }
            catch(Exception $e){
                $db->close();
                die('Caught exception: '.$e->getMessage());
            }
            
        ?>
        </div>
    </div>

    
    <!--FOOTER-->
    <footer>
        <a href="../about.html">About</a>
        <a href="../contact_us.html">Contact us</a>
        <cite>© Copyright 2021. All rights reserved.</cite>
    </footer>

    <script src="../js/update_time.js"></script>
    <script>
        setInterval(()=>{
            updateActiveTime();
        }, 55000);
    </script>
    </body>
</html>

