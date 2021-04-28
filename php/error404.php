<?php 

function error404($db){
    header("HTTP/1.1 404 Not Found");
    $db->close();
    exit();
}

?>