<?php
require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

ini_set("session.cookie_httponly", 1);
session_start();

if(!hash_equals($_SESSION['token'], $json_obj['token'])){
	die("Request forgery detected");
}

//Variables can be accessed as such:
$eventId = $json_obj['eventId'];

//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

// sending information to database
$stmt = $mysqli->prepare("delete from events where event_id=?");
    if (!$stmt) {
        $error_message = $mysqli->error;
        echo json_encode(array(
            "success" => false,
            "message" => "Failed Preparing"
        ));
        exit;
    }
$stmt->bind_param('s',$eventId);
$success = $stmt->execute();
$stmt->close();


if ($success) {
    // event was added
    echo json_encode(array(
        "success" => true
 	));
    exit;
} else {
    $error_message = $mysqli->error;
    echo json_encode(array(
	    "success" => false,
        "message" => "Failed Executing"
	));
	exit;
}
?>