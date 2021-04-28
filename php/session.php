<?php

    function login_check(){

        session_start();
        
        if(isset($_SESSION["user_id"])){
            header("location: home.php");
        }
    }

    function user_check(){

        session_start();

        if(!isset($_SESSION["user_id"])){
            header("location: login_form.php");
        }
    }
?>