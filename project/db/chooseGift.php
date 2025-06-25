<?php
require('./session.php');
require('./mysql.php');

$answer = [
    "code" => 400,
    "errors" => [],
    "message" => null
];

$username = $_SESSION["user"];

// Prüfe GET-Daten
if (isset($_GET["giftId"]) && isset($_GET["action"])) {
    $giftId = intval($_GET["giftId"]);
    $action = $_GET["action"];

    try {
        // Hole GuestId des eingeloggten Benutzers
        $stmt = $conn->prepare("
            SELECT g.GuestId
            FROM Guest g
            JOIN User u ON g.UserId = u.UserId
            WHERE u.Username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$guest = $result->fetch_assoc()) {
            $answer["code"] = 404;
            $answer["errors"][] = "Kein Gast für diesen Benutzer gefunden.";
            echo json_encode($answer);
            exit;
        }

        $guestId = $guest["GuestId"];

        // Führe Aktion aus
        if ($action === 'add') {
            // Prüfe, ob Geschenk bereits reserviert wurde
            $checkStmt = $conn->prepare("SELECT * FROM GiftReservation WHERE GiftId = ?");
            $checkStmt->bind_param("i", $giftId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $answer["code"] = 409;
                $answer["errors"][] = "Geschenk wurde bereits reserviert.";
            } else {
                // Reservierung einfügen
                $insertStmt = $conn->prepare("INSERT INTO GiftReservation (GiftId, GuestId) VALUES (?, ?)");
                $insertStmt->bind_param("ii", $giftId, $guestId);
                $insertStmt->execute();

                $answer["code"] = 200;
                $answer["message"] = "added";
            }

        } elseif ($action === 'remove') {
            // Reservierung löschen
            $deleteStmt = $conn->prepare("DELETE FROM GiftReservation WHERE GiftId = ? AND GuestId = ?");
            $deleteStmt->bind_param("ii", $giftId, $guestId);
            $deleteStmt->execute();

            $answer["code"] = 200;
            $answer["message"] = "removed";
        }

    } catch (mysqli_sql_exception $e) {
        $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($answer);
