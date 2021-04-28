<?php
    include("config.php");
    include('getFollowersAndFollowing.php');
    include('getPosts.php');
    include('error404.php');
    session_start();

    $user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($db, $_GET['user_id']) : null;
    $user_info=[];
    
    try{
        if(!isset($user_id) && isset($_SESSION['user_id'])){ //to pass the test
            $user_id = $_SESSION['user_id'];

            //update current profile
            $_SESSION['profile_user_id'] = $_SESSION['user_id'];
        }

        if(isset($user_id)){

            $stmt = $db->prepare("select * from users where user_id=?");
            $stmt->bind_param("s",$user_id);
            
            if($stmt->execute()){

                //update current profile
                $_SESSION['profile_user_id'] = $user_id;

                $result =  $stmt->get_result();
                $num_rows = $result->num_rows;
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $stmt->close();

                if($num_rows == 1) {

                    $user_id = $row['user_id'];

                    $user_info = array(
                        'firstname'=> $row['firstname'],
                        'lastname'=>$row['lastname'],
                        'email'=>$row['email'],
                        'user_image'=>$row['user_image'],
                        'cover_image'=>$row['cover_image'],
                        'biography'=>stripslashes(str_replace("\\n","<br>",htmlentities($row['biography']))),
                        'isOwner'=>false,
                        'unloggedUser'=>false,
                        'user_id'=> $user_id,
                        'isFollowing'=>false,
                        'current_username'=>''
                    );

                    if(isset($_SESSION["user_id"]) && $_SESSION["user_id"]==$row['user_id'])
                        $user_info['isOwner'] = true;

                    if(!isset($_SESSION["user_id"]))
                        $user_info['unloggedUser'] = true;

                    if(isset($_SESSION['username']))
                        $user_info['current_username'] = $_SESSION['username'];
                }
                else{
                    error404($db);
                }
            }
            else
                $stmt->close();

            if(isset($_SESSION['user_id'])){

                //check if is follower or following (to set button follow/unfollow)
                $stmt = $db->prepare("
                select case when following_id then true else false end as isFollowing 
                from followers where following_id=? and follower_id=?");
                $stmt->bind_param("ss",$user_id, $_SESSION['user_id']);

                if($stmt->execute()){

                    $result =  $stmt->get_result();
                    $row = $result->fetch_array(MYSQLI_ASSOC);

                    if(isset($row['isFollowing'])){
                        $user_info['isFollowing'] = $row['isFollowing'];
                    }
                }
                $stmt->close();
            }
            
            $followers = getFollowersAndFollowing($user_id, $db);
            $posts = getPosts($user_id, $db);
        }
        else{
            header("Location: login_form.php");
            $db->close();
            exit();
        }

        $db->close();
        
    }
    catch(Exception $e){
        $db->close();
        die('Caught exception: '.$e->getMessage());
    }
    
?>

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
    <meta charset=utf-8 />
    <title> hiCookie</title>
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
        <div class="block-box selected-box invisible">
          <a  href="<?php

            if(isset($_SESSION['user_id'])) 
                echo "show_profile.php?user_id=".$_SESSION['user_id'];
            else
                echo "login_form.php";

            ?>"> <i class="fas fa-user"></i> 
            </a>
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
        <div class="selected-box">
          <a  href="<?php

            if(isset($_SESSION['user_id'])) 
                echo "show_profile.php?user_id=".$_SESSION['user_id'];
            else
                echo "login_form.php";

            ?>"> <i class="fas fa-user"></i> </a>
        </div>
    </nav>
      
  
      <div class="flexible-box">
        <?php 

            if(isset($user_info['isOwner']) && $user_info['isOwner']){

                echo "<label id=\"upload-cover-label\" class=cover-photo for=\"upload-cover\">";
                
                if(isset($user_info['cover_image']))
                    echo "<img src=".$user_info['cover_image']." alt=\"cover-image\">";
                
                echo "</label><input class=\"display-none\" type='file' id=\"upload-cover\">";
            }
            else{

                echo "<div class=cover-photo>";

                if(isset($user_info['cover_image']))
                    echo "<img src=".$user_info['cover_image']." alt='cover-image'>";

                echo "</div>";
            }


        ?>
        <div class="profile-box">
            <div class="flexible-image-profile">
                <div id="image-profile-box" class="image-profile-box animate__animated animate__bounceInDown animation_delay_6ms">
                <?php 

                    if(isset($user_info['isOwner']) && $user_info['isOwner']){

                        echo "<label id=\"upload-profile-image-label\" for=\"upload-profile-image\">";
                        
                        if(isset($user_info['user_image']))
                            echo "<img src=".$user_info['user_image']." alt=\"user-image\">";
                        
                        echo "</label><input type='file' id=\"upload-profile-image\">";
                    }
                    else{

                        echo "<div>";
                        
                        if(isset($user_info['user_image']))
                            echo "<img src=".$user_info['user_image']." alt='user-image'>";
                        
                        echo "</div>";
                    }


                ?>
                </div>
            </div>
            <div class="username-box animate__animated animate__flipInX animation_delay_4ms">
                <strong id="username"><?php 
                if(isset($user_info['firstname']) && isset($user_info['lastname']))
                    echo $user_info['firstname'].' '.$user_info['lastname']
                ?></strong>
            </div>
            <div class='center column-box' id=follow-box>
                <div id=bioBox class=biography-box>
                    <div id=add-bio-button class=underline><div class=biography>Add Bio</div></div>
                </div>
            </div>
            <div class="icons-box">
                <div id=posts-icon class="block-box selected-box">
                    <i class="fas fa-tablet-alt"></i>
                    <p>Posts</p>
                </div>
                <div id=favorites-icon class="block-box">
                    <i class="fas fa-star"></i>
                    <p>Favorites</p>
                </div>
                <div id=followers-icon class="block-box">
                    <i class="fas fa-users"></i>
                    <p>Followers & Following</p>
                </div>
                <div id=settings-icon class="block-box">
                    <i class="fas fa-user-cog"></i>
                    <p>Settings</p>
                </div>
            </div>
            <div class=center> <div id=new-post-button class=new-post-button><span>Create new post</span><i class="fas fa-plus"></i></div></div>
            </div>
            <div id="posts-box" class="posts-box animate__animated animate__fadeInDown"></div>
            <?php 
                if(isset($user_info['isOwner']) && $user_info['isOwner'])
                    echo "<div id=\"favorites-box\" style='display:none;' class=\"posts-box animate__animated animate__fadeInDown\"></div>"
            ?>
            <div id="followers-box" style='display:none;' class="posts-box animate__animated animate__fadeInDown">
                <div id=followers class=posts-box style='margin-bottom:0;'><div class=center><strong class=follow-title>Followers: 0</strong></div></div>
                <div id=following class=posts-box  style='margin-bottom:0;'><div class=center><strong class=follow-title>Following: 0</strong></div></div>
            </div>
            <?php 
                if(isset($user_info['isOwner']) && $user_info['isOwner'])
                    echo "
                    <div id=\"settings-box\" style='display: none;' class=\"posts-box animate__animated animate__fadeInDown\">
                        <div class=settings-row><span>Firstname</span> <strong>".(isset($user_info['firstname']) ? $user_info['firstname'] : '')."</strong> <button id='firstname' value='Edit'>Edit</button></div>
                        <div class=settings-row><span>Lastname</span> <strong>".(isset($user_info['lastname']) ? $user_info['lastname'] : '')."</strong> <button  id='lastname' value='Edit'>Edit</button></div>
                        <div class=settings-row><span>Email</span> <strong>".(isset($user_info['email']) ? $user_info['email'] : '')."</strong> <button id='email' value='Edit'>Edit</button></div>
                        <div class=settings-row><span>Current password</span> <input type=\"password\" id=\"current-password\" placeholder=\"Current password\"></div>
                        <div class=settings-row><span>New password</span> <input type=\"password\" id=\"new-password1\" placeholder=\"New password\"></div>
                        <div class=settings-row><span>Retype new password</span> <input type=\"password\" id=\"new-password2\" placeholder=\"Retype new password\"></div>
                        <div class='center'> <button id='saveAllChanges'>Save all changes</button> <button id='deleteProfile'>Delete account</button></div>
                    </div>";
            ?>
            
            
        </div>

        
     <!--FOOTER-->
    <footer>
        <a href="../about.html">About</a>
        <a href="../contact_us.html">Contact us</a>
        <cite>© Copyright 2021. All rights reserved.</cite>
    </footer>
        
        
        <script src="../js/profile.js"></script>
        <script src="../js/getPosts.js"></script>
        <script src="../js/createPost.js"></script>
        <script src="../js/readMedia.js"></script>
        <script src="../js/biography.js"></script>
        <script src="../js/comment.js"></script>
        <script src="../js/like.js"></script>
        <script src="../js/update_time.js"></script>
        <script src="../js/favorite.js"></script>
        <script src="../js/follower.js"></script>
        <script src="../js/share.js"></script>
        <script src="../js/notifications.js"></script>
        <script>
            setUserInfo(
            <?php 

                echo json_encode(json_encode(array(
                    'biography'=> (isset($user_info['biography']) ? $user_info['biography'] : null),
                    'isOwner'=> (isset($user_info['isOwner']) ? $user_info['isOwner'] : null),
                    'unloggedUser'=> (isset($user_info['unloggedUser']) ? $user_info['unloggedUser'] : null),
                    'isFollowing'=> (isset($user_info['isFollowing']) ? $user_info['isFollowing'] : null)
                )));

            ?>);
            
            getFollowersAndFollowing(<?php 
            
                echo json_encode(json_encode(
                    (isset($followers) ? $followers : [])
                ));
            ?>);

            getPosts(<?php 
            
                echo json_encode(json_encode(
                    (isset($posts) ? $posts : [])
                ));
            ?>);

        </script>

    </body>
</html>
