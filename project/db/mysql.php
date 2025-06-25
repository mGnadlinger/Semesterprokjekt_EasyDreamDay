<?php
$servername = "db_server";
$port = 3306;
$username = "easyDreamDay";
$password = "passwort";
$dbname = "easyDreamDay";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
