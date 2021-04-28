<?php
   session_start();
   
   if(session_unset() && session_destroy()) { // remove all session variables and destroy session
      header("Location: ../index.html");
   }
?>