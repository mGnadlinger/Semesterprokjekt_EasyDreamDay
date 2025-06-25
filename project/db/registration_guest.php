<?php
require('./session.php');
include './mysql.php';

// INIT VARIABLES
$errors = [];
$weddingCode = $guestCode = $username = $password = $password2 = "";

$answer = [
    "data" => [],
    "success" => false,
    "errors" => []
];

// CHECK POST DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Wedding Code
    if (empty($_POST["guest-wedding-code"])) {
        $errors[] = "Gib den Hochzeitscode an";
    } else {
        $weddingCode = htmlspecialchars($_POST["guest-wedding-code"]);
    }

    // Guest Code
    if (empty($_POST["guest-code"])) {
        $errors[] = "Gib deinen Gästecode an";
    } else {
        $guestCode = htmlspecialchars($_POST["guest-code"]);
    }

    // Username
    if (empty($_POST["guest-username"])) {
        $errors[] = "Gib einen Benutzernamen an";
    } else {
        $username = htmlspecialchars($_POST["guest-username"]);
    }

    // Password
    if (empty($_POST["guest-password"])) {
        $errors[] = "Gib ein Passwort an";
    } else {
        $password = $_POST["guest-password"];
    }

    // Repeat Password
    if (empty($_POST["guest-password2"])) {
        $errors[] = "Wiederhole das Passwort";
    } else {
        $password2 = $_POST["guest-password2"];
        if ($password !== $password2) {
            $errors[] = "Die Passwörter stimmen nicht überein";
        }
    }

    $answer["data"] = [$weddingCode, $guestCode, $username];
    $answer["errors"] = $errors;

    if (!array_filter($errors)) {

        try {
            $stmt = $conn->prepare("SELECT * FROM Guest WHERE WeddingId = ? AND GuestId = ?");
            $stmt->bind_param("ii", $weddingCode, $guestCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO User (Phone, Email, Role, PasswordHash, Username) VALUES (?, ?, 'Guest', ?, ?)");
                $stmt->bind_param("ssss", $user['Phone'], $user['Email'], $passwordHash, $username);
                $stmt->execute();
                $userId = $conn->insert_id;

                $stmt = $conn->prepare("UPDATE Guest SET UserId = ? WHERE WeddingId = ? AND GuestId = ?");
                $stmt->bind_param("iii", $userId, $weddingCode, $guestCode);
                $stmt->execute();

                $_SESSION["loggedIn"] = true;
                $_SESSION["user"] = $username;

                $answer["success"] = true;
            } else {
                $errors[0] = "Ungültige Hochzeits- oder Gästedaten";
                $answer["errors"] = $errors;
            }
        } catch (mysqli_sql_exception $e) {
            $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
        }
    }
}

header('Content-Type: application/json');
echo json_encode($answer);
?>
