<?php

    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', '*****'); //modify with your db username
    define('DB_PASSWORD', '**********'); //modify with your db password
    define('DB_DATABASE', '*******'); //modify with your db database

    // Start connection
    $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
    
    // Check database connection
    if($db->connect_error)
        die("Connection failed: " . $db->connect_error);

?>