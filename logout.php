<?php
    header("Content-Type: application/json");

    ini_set("session.cookie_httponly", 1);
    session_start();
    session_destroy();
    ini_set("session.cookie_httponly", 1);
    session_start();
    session_unset();
    if (!isset($_SESSION['username'])) {
        echo json_encode(array(
            "success" => true
        ));       
        exit;
    } else {
        echo json_encode(array(
            "success" => false
        ));
        exit;
    }
?>