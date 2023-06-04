<?php
require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

ini_set("session.cookie_httponly", 1);
session_start();
$username = $_SESSION['username'];
$date = substr($json_obj['date'], 0, 10);

$stmt = $mysqli->prepare("select event, event_description, time, event_id from events where username=? and date=? order by time");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
$stmt->bind_param('ss', $username, $date);
$success = $stmt->execute();
$stmt->bind_result($event, $event_description, $time, $event_id);  
$eventArray = array();  
$eventDesArray = array();
$timeArray = array();
$idArray = array();
while ($stmt->fetch()) {
    $eventArray[] = htmlentities($event);
    $eventDesArray[] = htmlentities($event_description);
    $timeArray[] = htmlentities($time);
    $idArray[] = htmlentities($event_id);
}
$stmt->close();




if ($success) {
    // events were retrieved
    echo json_encode(array(
        "success" => true,
        "events" => $eventArray,
        "descriptions" => $eventDesArray,
        "times" => $timeArray,
        "ids" => $idArray
 	));
    exit;
} else {
    echo json_encode(array(
	    "success" => false,
        "message" => "Failed Getting Events"
	));
	exit;
}
?>