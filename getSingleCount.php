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
$year = $json_obj['year'];
$month = $json_obj['month'];
$date = substr($json_obj['date'], 0, 10);
$stmt = $mysqli->prepare("select count(*) from events where username=? and date=?");
if (!$stmt) {
    echo json_encode(
        array(
            "success" => false
        )
    );
    exit;
}
$stmt->bind_param('ss', $username, $date);
$success = $stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();
$day = substr($date, 8, 2);
if ($success) {
    // events were retrieved
    echo json_encode(
        array(
            "success" => true,
            "count" => $count,
            "day" => $day
        )
    );
    exit;
} else {
    echo json_encode(
        array(
            "success" => false
        )
    );
    exit;
}
?>