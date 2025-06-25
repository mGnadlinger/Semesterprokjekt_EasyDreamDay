<?php
require('./session.php');
include './mysql.php';

$guestId = $_GET['guestId'] ?? null;

if ($guestId === null) {
    echo json_encode(["code" => 400, "message" => "Keine ID"]);
    exit;
}

try {
    // Aktuellen RSVP-Status abrufen
    $stmt = $conn->prepare("SELECT RSVP FROM Guest WHERE GuestId = ?");
    $stmt->bind_param("i", $guestId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $current = $result['RSVP'];

    // RSVP Status umschalten
    if ($current === 'Yes') {
        $newResponse = 'Pending';
    } else {
        $newResponse = 'Yes';
    }

    // Status aktualisieren
    $stmt = $conn->prepare("UPDATE Guest SET RSVP = ? WHERE GuestId = ?");
    $stmt->bind_param("si", $newResponse, $guestId);
    $stmt->execute();

    echo json_encode(["code" => 200, "response" => $newResponse]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(["code" => 500, "message" => $e->getMessage()]);
}
