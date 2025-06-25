<?php
require('./session.php');
require('./mysql.php');
require_once __DIR__ . '/../composer/vendor/autoload.php';

use Mpdf\Mpdf;

$answer = [
    "code" => 400,
    "errors" => [],
    "message" => null
];

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION["user"])) {
    $answer["errors"][] = "Nicht eingeloggt.";
    echo json_encode($answer);
    exit;
}

$username = $_SESSION["user"];

try {
    // Hole die WeddingId des eingeloggten Users
    $stmt = $conn->prepare("
        SELECT w.WeddingId
        FROM Wedding w
        JOIN User u ON w.PlannerId = u.UserId
        WHERE u.Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$wedding = $result->fetch_assoc()) {
        $answer["errors"][] = "Keine Hochzeit für diesen Benutzer gefunden.";
        echo json_encode($answer);
        exit;
    }

    $weddingId = $wedding["WeddingId"];

    // Hole alle Gäste dieser Hochzeit
    $guestStmt = $conn->prepare("
        SELECT FirstName, LastName, Email, GuestId, WeddingId
        FROM Guest
        WHERE WeddingId = ?
        ORDER BY LastName, GuestId
    ");
    $guestStmt->bind_param("i", $weddingId);
    $guestStmt->execute();
    $guestResult = $guestStmt->get_result();

    // PDF mit mPDF erstellen
    $mpdf = new Mpdf();
    $html = '<h1 style="color:#BF5E78; text-align: center;">Gästeliste für die Hochzeit</h1>';
    $html .= '<table style="width: 100%; border-collapse: separate; border-spacing: 10px 10px;">';

    $rowOpen = false;
    $count = 0;

    while ($guest = $guestResult->fetch_assoc()) {
        $fullName = htmlspecialchars($guest['FirstName'] . ' ' . $guest['LastName']);
        $email = htmlspecialchars($guest['Email']);
        $guestId = htmlspecialchars($guest['GuestId']);
        $weddingId = htmlspecialchars($guest['WeddingId']);

        if ($count % 2 === 0) {
            $html .= '<tr>';
            $rowOpen = true;
        }

        $html .= '
            <td style="
                width: 50%;
                background: #D98FA3;
                padding: 15px;
                border-radius: 5px;
                box-sizing: border-box;
                vertical-align: top;
            ">
                <h2 style="margin-top: 0;">' . $fullName . '</h2>
                <p><strong>Email:</strong> ' . $email . '</p>
                <p><strong>GästeID:</strong> ' . $guestId . '</p>
                <p><strong>HochzeitsID:</strong> ' . $weddingId . '</p>
            </td>
        ';

        if ($count % 2 === 1) {
            $html .= '</tr>';
            $rowOpen = false;
        }

        $count++;
    }

    // Falls letzte Zeile nicht geschlossen wurde (ungerade Anzahl Gäste)
    if ($rowOpen) {
        $html .= '<td style="width: 50%;"></td></tr>';
    }

    $html .= '</table>';

    $mpdf->WriteHTML($html);

    // PDF als Download senden
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=gaesteliste.pdf");
    echo $mpdf->Output('', 'S');
    exit;

} catch (mysqli_sql_exception $e) {
    $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($answer);
    exit;
}
