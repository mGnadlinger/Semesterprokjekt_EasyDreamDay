<?php
require('./session.php');
include './mysql.php';

// INIT VARIABLES
$errors = [];
$username = $password = "";

$answer = [
    "data" => [],
    "loggedIn" => false,
    "errors" => []
];

// CHECK POST DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Username prüfen
    if (empty($_POST["login-username"])) {
        $errors[] = "Gib einen Benutzernamen an";
    } else {
        $username = htmlspecialchars($_POST["login-username"]);
        $errors[] = "";
    }

    // Password prüfen
    if (empty($_POST["login-password"])) {
        $errors[] = "Gib ein Passwort an";
    } else {
        $password = $_POST["login-password"];
        $errors[] = "";
    }

    $answer["data"] = [$username];
    $answer["errors"] = $errors;

    if (!array_filter($errors)) { // keine Fehler

        try {
            $stmt = $conn->prepare("SELECT * FROM User WHERE Username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                if (password_verify($password, $user['PasswordHash'])) {
                    $answer['loggedIn'] = true;
                    $answer['data'] = $user['Role'];

                    $_SESSION["loggedIn"] = true;
                    $_SESSION["user"] = $user['Username'];
                } else {
                    $errors[1] = "Falsches Passwort.";
                    $answer['errors'] = $errors;
                }

                $answer["success"] = true;
            } else {
                $errors[0] = "Kein Benutzer mit diesem Benutzernamen gefunden. Bitte versuche es erneut.";
                $answer["errors"] = $errors;
            }

        } catch (mysqli_sql_exception $e) {
            $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
        }
    }
}

header('Content-Type: application/json');
echo json_encode($answer);
