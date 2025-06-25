<?php
require('./session.php');
include './mysql.php';

$todoId = $_GET['todoId'] ?? null;

if ($todoId === null) {
    echo json_encode(["code" => 400, "message" => "Keine ID"]);
    exit;
}

try {
    // Aktuellen Status abfragen
    $stmt = $conn->prepare("SELECT Done FROM ToDo WHERE ToDoId = ?");
    $stmt->bind_param("i", $todoId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Status invertieren
    $newStatus = $result['Done'] ? 0 : 1;

    // Status updaten
    $stmt = $conn->prepare("UPDATE ToDo SET Done = ? WHERE ToDoId = ?");
    $stmt->bind_param("ii", $newStatus, $todoId);
    $stmt->execute();

    echo json_encode(["code" => 200, "done" => $newStatus]);

} catch (mysqli_sql_exception $e) {
    echo json_encode(["code" => 500, "message" => $e->getMessage()]);
}
?>
