<!DOCTYPE html>
<?php  
    include('session.php');
    user_check();
    ?> 
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/account.css">
        <link rel="stylesheet" type="text/css" href="../css/home.css">
        <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
        <link rel="stylesheet" type="text/css" href="../css/notifications.css">
        <link rel="stylesheet" type="text/css" href="../css/footer.css">
        <link rel="stylesheet" type="text/css" href="../css/footer-mq.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset=utf-8 />
        <script src="../js/fontawesome.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <title> hiCookie</title>
    </head>
    <body>
        <nav class="nav-bar between">
            <div class="title">
                <a href="../index.html">hiCookie</a>
                <i class="fal fa-cookie-bite"></i>
            </div>
            <div class="block-box selected-box invisible">
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
            <div class="selected-box">
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

        <div class='flexible-box'>
            <div id="posts-box" class="posts-box animate__animated animate__slideInDown"></div>
        </div>

        <!--FOOTER-->
        <footer>
            <a href="../about.html">About</a>
            <a href="../contact_us.html">Contact us</a>
            <cite>© Copyright 2021. All rights reserved.</cite>
        </footer>

        <script src="../js/home.js"></script>
        <script src="../js/update_time.js"></script>
        <script src="../js/createPost.js"></script>
        <script src="../js/comment.js"></script>
        <script src="../js/like.js"></script>
        <script src="../js/favorite.js"></script>
        <script src="../js/share.js"></script>
        <script src="../js/notifications.js"></script>
    </body>
</html>