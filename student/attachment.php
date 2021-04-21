<?php
    ob_start();
    session_start();
    if(!array_key_exists('role', $_SESSION)) header('location: ../index.php');
    else {
        if (array_key_exists('attachment_type', $_POST) and array_key_exists('attachment_data', $_POST)) {
            header("Content-type: " . $_POST["attachment_type"]);
            echo base64_decode($_POST["attachment_data"]);
        }
        else {
            header('HTTP/1.0 404 Not Found');
        }
    }
?>