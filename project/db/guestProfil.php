<?php
require('./session.php');
include './mysql.php';

// INIT
$errors = [];
$username = $_SESSION["user"];

$answer = [
    "data" => [],
    "success" => false,
    "errors" => []
];

try {
    // Hochzeit des eingeloggten Users ermitteln
    $stmt = $conn->prepare("
        SELECT * 
        FROM Wedding 
        JOIN Guest ON Wedding.WeddingId = Guest.WeddingId 
        JOIN User ON Guest.UserId = User.UserId 
        WHERE User.Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $wedding = $result->fetch_assoc();

    // Zeitplan abrufen
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(Schedule.Time, '%H:%i') AS Time, 
            Schedule.EventName, 
            Schedule.MeetingPoint 
        FROM Schedule 
        JOIN Wedding ON Schedule.WeddingId = Wedding.WeddingId 
        JOIN Guest ON Wedding.WeddingId = Guest.WeddingId 
        JOIN User ON Guest.UserId = User.UserId 
        WHERE User.Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_all(MYSQLI_ASSOC);

    // Geschenke und Reservierungen abrufen
    $stmt = $conn->prepare("
        SELECT 
            Gift.GiftId,
            Gift.Name,
            COUNT(GiftReservation.GuestId) AS ReservationCount,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM GiftReservation gr
                    JOIN Guest g ON gr.GuestId = g.GuestId
                    JOIN User u ON g.UserId = u.UserId
                    WHERE gr.GiftId = Gift.GiftId AND u.Username = ?
                )
                THEN 1 ELSE 0
            END AS IsReservedByUser
        FROM Gift 
        JOIN Wedding ON Gift.WeddingId = Wedding.WeddingId 
        JOIN Guest ON Wedding.WeddingId = Guest.WeddingId 
        JOIN User ON Guest.UserId = User.UserId 
        LEFT JOIN GiftReservation ON Gift.GiftId = GiftReservation.GiftId
        WHERE User.Username = ?
        GROUP BY Gift.GiftId
    ");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $gift = $result->fetch_all(MYSQLI_ASSOC);

    // Partnernamen abrufen
    $stmt = $conn->prepare("
        SELECT 
            g1.FirstName AS Partner1FirstName,
            g2.FirstName AS Partner2FirstName
        FROM User u
        JOIN Guest g ON u.UserId = g.UserId
        JOIN Wedding w ON g.WeddingId = w.WeddingId
        JOIN Guest g1 ON w.Partner1Id = g1.GuestId
        JOIN Guest g2 ON w.Partner2Id = g2.GuestId
        WHERE u.Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $partner = $result->fetch_assoc();

    // Daten zusammenbauen
    if ($wedding) {
        $answer["data"] = [
            "partner1" => $partner['Partner1FirstName'],
            "partner2" => $partner['Partner2FirstName'],
            "weddingDate" => date("d.m.Y", strtotime($wedding["Date"])),
            "weddingTime" => date("H:i", strtotime($wedding["Time"])) . " Uhr",
            "weddingStyle" => $wedding["CeremonyType"],
            "weddingDresscode" => $wedding["Dresscode"],
            "weddingLocation" => $wedding["Location1"],
            "weddingSchedule" => $schedule,
            "weddingGift" => $gift
        ];
        $answer["success"] = true;
    } else {
        $answer["errors"][] = "Fehler beim Daten finden. Bitte versuche es erneut.";
    }

} catch (mysqli_sql_exception $e) {
    $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
}

// JSON-Ausgabe
header('Content-Type: application/json');
echo json_encode($answer);
?>
