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
$days = $json_obj['days'];

// get event count for the entire month
$eventCounts = array();
for ($day = 0; $day < $days; $day++) {
    $date = $year . "-" . ($month + 1) . "-" . ($day + 1);
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
    $eventCounts[] = $count;
    $stmt->close();
}
if ($success) {
    // events were retrieved
    echo json_encode(
        array(
            "success" => true,
            "counts" => $eventCounts,
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