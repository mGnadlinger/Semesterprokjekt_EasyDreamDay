<?php
require('./session.php');
include './mysql.php';

$answer = [
    "data" => [],
    "success" => false,
    "errors" => []
];

$username = $_SESSION["user"];

try {
    // Hochzeit holen
    $stmt = $conn->prepare("
        SELECT * 
        FROM Wedding W
        JOIN User U ON W.PlannerId = U.UserId
        WHERE U.Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $wedding = $stmt->get_result()->fetch_assoc();

    // Gäste holen
    $stmt = $conn->prepare("
        SELECT G.GuestId, G.FirstName, G.LastName, COALESCE(G.RSVP, 'Pending') AS RSVP, G.AdditionalText
        FROM Guest G
        LEFT JOIN User U ON G.UserId = U.UserId
        WHERE G.WeddingId = ?
    ");
    $stmt->bind_param("i", $wedding["WeddingId"]);
    $stmt->execute();
    $guests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Zeitplan holen
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(Time, '%H:%i') as Time,
            EventName, 
            MeetingPoint
        FROM Schedule 
        WHERE WeddingId = ?
        ORDER BY Time
    ");
    $stmt->bind_param("i", $wedding["WeddingId"]);
    $stmt->execute();
    $schedule = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // ToDos holen (nur kommende)
    $stmt = $conn->prepare("
        SELECT 
            ToDoId, 
            Name, 
            DATE_FORMAT(Date, '%d.%m.%Y') as Date,
            DATE_FORMAT(Time, '%H:%i') as Time,
            Done
        FROM ToDo 
        WHERE WeddingId = ? AND Date > SYSDATE()
        ORDER BY Date, Time
    ");
    $stmt->bind_param("i", $wedding["WeddingId"]);
    $stmt->execute();
    $todos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Antwort befüllen
    $answer["data"] = [
        "weddingDate" => date("d.m.Y", strtotime($wedding["Date"])),
        "weddingTime" => date("H:i", strtotime($wedding["Time"])) . " Uhr",
        "weddingStyle" => $wedding["CeremonyType"],
        "weddingGuestNumber" => count($guests),
        "weddingLocation" => $wedding["Location1"],
        "weddingGuests" => $guests,
        "weddingSchedule" => $schedule,
        "todos" => $todos
    ];

    $answer["success"] = true;

} catch (mysqli_sql_exception $e) {
    $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($answer);
?>
