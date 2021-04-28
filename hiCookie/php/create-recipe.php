<!DOCTYPE html>
<?php  
    include('session.php');
    user_check();
    ?>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/create-recipe.css">
        <link rel="stylesheet" type="text/css" href="../css/navbar-logged.css">
        <link rel="stylesheet" type="text/css" href="../css/notifications.css">
        <link rel="stylesheet" type="text/css" href="../css/footer.css">
        <link rel="stylesheet" type="text/css" href="../css/footer-mq.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset=utf-8>
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
            <div class="block-box selected-box invisible">
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
            <div class="selected-box">
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

        <form id=recipe-form class="box-container" method="post">
            <div class="recipe-image-container" id="recipe-image-container">
                <label class="far fa-plus-circle" id="upload-photo-icon" for="upload-photo"></label>
                <p>Upload image</p>
                <input type='file' id="upload-photo">
            </div>
            <div class="recipe-title">
                <input type="text" name="recipe-title" id="recipe-title" placeholder="Title">
                <hr>
                <i>Choose your recipe title</i>
            </div>
            <div class="info-box">
                <p>Preparation</p>
                <div class=options-box id='preparation-time'>
                    <input type="number" value="1" min="0" max="1000">
                    <select>
                        <option value="min/s">min/s</option>
                        <option value="hour/s">hour/s</option>
                        <option value="day/s">day/s</option>
                    </select>
                    <i class="fal fa-clock"></i>
                </div>
            </div>
            <div class="info-box">
                <p>Cooking</p>
                <div class=options-box id='cooking-time'>
                    <input type="number" value="1" min="0" max="1000">
                    <select>
                        <option value="min/s">min/s</option>
                        <option value="hour/s">hour/s</option>
                        <option value="day/s">day/s</option>
                    </select>
                    <i class="fal fa-clock"></i>
                </div>
            </div>
            <div class="info-box">
                <p>Difficulty</p>
                <div class="options-box">
                    <select id='difficulty'>
                        <option value="easy">easy</option>
                        <option value="medium">medium</option>
                        <option value="difficult">difficult</option>
                    </select>
                    <i class="fal fa-hat-chef"></i>
                </div>
            </div>
            <div class="info-box">
                <p>Cost</p>
                <div class="options-box">
                    <select id='cost'>
                        <option value="cheap">cheap</option>
                        <option value="medium">medium</option>
                        <option value="expensive">expensive</option>
                    </select>
                    <i class="fal fa-sack-dollar"></i>
                </div>
            </div>
            <div class="info-box">
                <p>Ingredients</p>
                <div class="options-box">
                    <p>for</p>
                    <input type="number" id="ingredients-for-people" value="1" min="0" max="1000">
                    <p>people</p>
                    <i class="fal fa-users"></i>
                </div>
            </div>
            <div id="ingredients-container">
                <div data-name=ingredient-line id=ingredient-line1 class="ingredient-line">
                    <div class="ingredient-name">
                        <input type="text" name="ingredient-name" placeholder="Ingredient name">
                        <hr>
                    </div>
                    <div class=options-box>
                        <input type="number" name="ingredient-quantity" step="0.01" value="1" min="0" max="999">
                        <select id="units" name="ingredient-type">
                            <option value="none">none</option>
                            <optgroup label="Solids">
                                <option value="g">g</option>
                                <option value="kg">kg</option>
                            </optgroup>
                            <optgroup label="Liquids">
                                <option value="ml">ml</option>
                                <option value="l">l</option>
                            </optgroup>
                        </select>
                        <div class="delete-ingredient-button" id="ingredient-trash-box1"><i class="far fa-trash-alt"></i></div>
                    </div>
                </div>
            </div>
            <div id="add-ingredient" class="add-box">
                <i class="fas fa-plus-circle"></i>
                <p>Add ingredient</p>
            </div>
            <div class="text-box textarea-info">
                <p>Curiosity</p>
                <textarea rows="10" id='curiosity' maxlength="1000" placeholder="Tell your friends more about your recipe.."></textarea>
            </div>
            <div id=preparation-box class=text-box>
                <p>Preparation step</p>
                <div data-name="step-box-container" id="step-box-container1">
                    <div class="step-box">
                        <div class="column-box">
                            <div data-name="circle-box" class="circle-box"> 1</div>
                            <div data-name="delete-button" id="delete-button1"><i class="far fa-trash-alt"></i></div>
                            <label class="far fa-plus-circle step-image-button-label" for="step-image-button1"></label>
                            <input type="file" name="step-image-button" id="step-image-button1">
                        </div>
                        <textarea name="step-description" rows="4" maxlength="1000" id="step-description1" placeholder="Describe preparation step"></textarea>
                    </div>
                    <div id="step-box-images1" class="step-box-images"></div>
                </div>
            </div>
            <div id='add-step' class="add-box">
                <i class="fas fa-plus-circle"></i>
                <p>Add step</p>
            </div>
            <div class="share-box">
                <p>Are you ready?</p>
                <p>Share your recipe with all your friends.</p>
                <button>share</button>
            </div>
        </form>

        <!--FOOTER-->
        <footer>
            <a href="../about.html">About</a>
            <a href="../contact_us.html">Contact us</a>
            <cite>© Copyright 2021. All rights reserved.</cite>
        </footer>

        <div class="top-left-image-box">
            <img src="../images/cookie-svgrepo-com.svg" alt="cookie-img">
        </div>
        <div class="top-right-image-box">
            <img src="../images/cookie-svgrepo-com.svg" alt="cookie-img">
        </div>

        <script src="../js/recipe.js"></script>
        <script src="../js/readMedia.js"></script>
        <script src="../js/update_time.js"></script>
        <script src="../js/notifications.js"></script>
    </body>
</html>