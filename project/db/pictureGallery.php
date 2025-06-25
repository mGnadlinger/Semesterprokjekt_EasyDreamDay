<?php
require('./session.php');
include './mysql.php';

$answer = [
    "data" => [],
    "success" => false,
    "errors" => []
];

try {
    $stmt = $conn->prepare("
        SELECT * 
        FROM Photo;
    ");
    $stmt->execute();
    $photos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $answer["data"] = $photos;
    $answer["success"] = true;

} catch (mysqli_sql_exception $e) {
    $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($answer);
?>
