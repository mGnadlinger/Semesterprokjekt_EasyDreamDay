<?php
require('./session.php');

$answer = [
    "code" => 404,
    "loggedIn" => false,
    "username" => ''
];

if (!empty($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $answer["code"] = 200;
    $answer["loggedIn"] = true;
    $answer["username"] = $_SESSION["user"];
}

header('Content-Type: application/json');
echo json_encode($answer);
