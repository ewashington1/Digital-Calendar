<?php
    header("Content-Type: application/json");

    ini_set("session.cookie_httponly", 1);
    session_start();
    if (isset($_SESSION['username'])) {
        echo json_encode(array(
            "success" => true,
            "message" => htmlentities($_SESSION['username'])
        ));       
        exit;
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Not Logged In"
        ));
        // how to not use json in output echo ("Not Logged In");
        exit;
    }
?>