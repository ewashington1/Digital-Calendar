<?php
require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

session_start();
$day = $json_obj['body'];
$username = $_SESSION("username");

// get event count for the entire month
$eventsInWeek = array();
for ($day = 0; $day < 7; $day++) {
    $date = $date.getdate()+1;
    $stmt = $mysqli->prepare("select event from events where username=? and date=?");
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
    $stmt->bind_result($event);
    $stmt->fetch();
    $eventsInWeek[] = $event;
    $stmt->close();
}
if ($success) {
    // events were retrieved
    echo json_encode(
        array(
            "success" => true,
            "events" => $eventsInWeek
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