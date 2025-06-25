<?php
require('./session.php');

$answer = [
    "code" => 404,
    "loggedIn" => false,
    "username" => ''
];

// Session zurücksetzen
$_SESSION['loggedIn'] = false;
$_SESSION['user'] = '';

$answer["code"] = 200;

header('Content-Type: application/json');
echo json_encode($answer);
