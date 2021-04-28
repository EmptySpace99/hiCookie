<!DOCTYPE html>
<?php 
    include('session.php');
    login_check();
    ?>
<html lang="en">
    <head>
        <title>Login</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/login.css">
        <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
        <link rel="stylesheet" type="text/css" href="../css/notifications.css">
        <link rel="stylesheet" type="text/css" href="../css/footer.css">
        <script src="../js/fontawesome.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <!--NAVBAR-->
        <nav class="nav-bar" style="justify-content: space-between;">
            <div class="title space">
                <a href="../index.html">hiCookie</a>
                <i class="fal fa-cookie-bite"></i>
            </div>
            <form action="search.php" method="get" class="search-bar space invisible">
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
            <form action="search.php" method="get" class="search-bar">
                <input type="text" name="search_content" placeholder="Search friends or recipes..">
                <button class="fa fa-search"></button>
            </form>
        </div>

        <div class=flexible-box>
            <div >
                <!--LOGIN BOX-->
                <div class="login-box">
                    <!--FRONT-->
                    <div class="front rotation0">
                        <form id="signIn" method=post class=form-box>
                            <h1>Sign in <i class="fal fa-cookie-bite"></i> </h1>
                            <div class="inline-box">
                                <i class="fas fa-user"></i>
                                <input type="text" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="inline-box">
                                <i class="fas fa-key"></i>
                                <input type="password" name="pass" id="password" placeholder="Password" autocomplete='off'>
                            </div>
                            <button>Sign in</button>
                            <div class=change-card id=goToSignUp>Sign up <i class="far fa-arrow-alt-right"></i> </div>
                        </form>
                    </div>
                    <!--BACK-->
                    <div class="back rotation180">
                        <form id="signUp" method=post class=form-box>
                            <h1>Sign up <i class="fal fa-cookie-bite"></i> </h1>
                            <div class="inline-box">
                                <i class="fas fa-user"></i>
                                <input type="text" name="firstname" id="firstname" placeholder="Firstname">
                            </div>
                            <div class="inline-box">
                                <i class="fas fa-user"></i>
                                <input type="text" name="lastname" id="lastname" placeholder="Lastname">
                            </div>
                            <div class="inline-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" id="your-email" placeholder="Email">
                            </div>
                            <div class="inline-box">
                                <i class="fas fa-key"></i>
                                <input type="password" name="pass" id="password1" placeholder="Password" autocomplete='off'>
                            </div>
                            <div class="inline-box">
                                <i class="fas fa-key"></i>
                                <input type="password" name="confirm" id="password2" placeholder="Confirm password" autocomplete='off'>
                            </div>
                            <button>Sign up</button>
                            <div  class=change-card id=goToSignIn> <i class="far fa-arrow-alt-left"></i> Sign in</div>
                        </form>
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

        <script src="../js/login.js"></script>
        <script src="../js/notifications.js"></script>
    </body>
</html>