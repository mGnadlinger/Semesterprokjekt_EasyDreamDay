<?php
require('./session.php');
include './mysql.php';

$answer = [
    "success" => false,
    "errors" => [],
    "message" => ""
];

if (!isset($_SESSION["user"])) {
    $answer["errors"][] = "Nicht eingeloggt.";
    echo json_encode($answer);
    exit;
}

$username = $_SESSION["user"];

try {
    // Hochzeit des Benutzers ermitteln
    $stmt = $conn->prepare("
        SELECT *
        FROM User U
        JOIN Guest G ON U.UserId = G.UserId
        JOIN Wedding W ON G.WeddingId = W.WeddingId
        WHERE U.Username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $wedding = $result->fetch_assoc();

    if (!$wedding) {
        $answer["errors"][] = "Keine Hochzeit für diesen Benutzer gefunden.";
        echo json_encode($answer);
        exit;
    }

    $weddingId = $wedding["WeddingId"];

    // Upload-Logik
    $uploadDir = "../uploads/";
    $uploadedFiles = $_FILES['fileToUpload'];
    $successCount = 0;

    foreach ($uploadedFiles['tmp_name'] as $index => $tmpName) {
        if ($uploadedFiles['error'][$index] === UPLOAD_ERR_OK) {
            $fileName = basename($uploadedFiles['name'][$index]);
            $targetFilePath = $uploadDir . uniqid() . "_" . $fileName;

            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $stmt = $conn->prepare("
                    INSERT INTO Photo (WeddingId, Link)
                    VALUES (?, ?)
                ");
                $relativePath = substr($targetFilePath, 3); // ../ entfernen
                $stmt->bind_param("is", $weddingId, $relativePath);
                $stmt->execute();
                $successCount++;
            } else {
                $answer["errors"][] = "Fehler beim Verschieben der Datei: $fileName";
            }
        } else {
            $answer["errors"][] = "Upload-Fehler bei Datei: " . $uploadedFiles['name'][$index];
        }
    }

    if ($successCount > 0) {
        $answer["success"] = true;
        $answer["message"] = "$successCount Datei(en) erfolgreich hochgeladen.";
    }

} catch (mysqli_sql_exception $e) {
    $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($answer);
