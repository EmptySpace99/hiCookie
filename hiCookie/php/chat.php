<!DOCTYPE html>
<?php  
    include('session.php');
    user_check();
    ?>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/chat.css">
        <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
        <link rel="stylesheet" type="text/css" href="../css/notifications.css">
        <link rel="stylesheet" type="text/css" href="../css/footer.css">
        <link rel="stylesheet" type="text/css" href="../css/footer-mq.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset=utf-8 />
        <script src="../js/fontawesome.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            <div class="block-box selected-box invisible">
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
            <div class="selected-box">
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
            <div class="chat-box" id="chat-box">
                <div class="chat-list-box">
                    <div class='user-box inline-box'>
                        <img id="current-user-img" src=<?php 
                            if(isset($_SESSION['user_image']))
                                echo $_SESSION['user_image']; 
                                
                            ?> alt="profile-image">
                        <a href="show_profile.php?user_id=<?php
                            if(isset($_SESSION['user_id']))
                                echo $_SESSION['user_id']; 
                            
                            ?>">
                        <strong id="current-user-name"><?php 
                            if(isset($_SESSION['firstname']) && isset($_SESSION['lastname']))
                                echo $_SESSION['firstname']." ".$_SESSION['lastname'] ;
                            
                            ?></strong> 
                        </a>
                    </div>
                    <div id=chatroom-box class=chatroom-box></div>
                </div>
                <div id="presentation-page" class=message-box>
                    <div class=image-box-relative>
                        <div class="image-box" id=image-box>
                            <img src="../images/cookie-svgrepo-com.svg" alt="cookie">
                        </div>
                    </div>
                    <div class='presentation-text'>
                        <strong>Chat with your <br> friends</strong>
                    </div>
                </div>
            </div>
        </div>

        <!--FOOTER-->
        <footer>
            <a href="../about.html">About</a>
            <a href="../contact_us.html">Contact us</a>
            <cite>© Copyright 2021. All rights reserved.</cite>
        </footer>
        
        <script src="../js/chatroom.js"></script>
        <script src="../js/chat.js"></script>
        <script src="../js/update_time.js"></script>
        <script src="../js/notifications.js"></script>
    </body>
</html>
