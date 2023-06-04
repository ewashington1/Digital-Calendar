<?php
require 'database.php';

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = $json_obj['username'];
$password = $json_obj['password'];
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

// seeing if username is valid
$stmt = $mysqli->prepare("select count(username) from users where username=?");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($count);    
$stmt->fetch();
$stmt->close();

// verifying password
$stmt = $mysqli->prepare("select password from users where username=?");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($passwordHash);
$stmt->fetch();
$stmt->close();

if ($count == 1 && password_verify($password, $passwordHash)) {
    ini_set("session.cookie_httponly", 1);
	session_start();
	$_SESSION['username'] = $username;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 

	echo json_encode(array(
		"success" => true,
        "token" => $_SESSION['token']
	));
	exit;
}else{
    if ($count < 1) {
        $message = "Username not found";
    } else if (!password_verify($password, $passwordHash)){
        $message = "Incorrect Password";
    }
	echo json_encode(array(
		"success" => false,
		"message" => $message
	));
	exit;
}
?>