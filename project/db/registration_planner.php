<?php
require('./session.php');
include './mysql.php';

$errors = [];
$answer = [
    "data" => [],
    "success" => false,
    "errors" => []
];

// Hilfsfunktion zum Saubermachen von Eingaben
function clean($field)
{
    return htmlspecialchars(trim($_POST[$field] ?? ""));
}

// Arrays aus POST-Daten
$guestEmail = $_POST['guest-email'] ?? [];
$guestFirstname = $_POST['guest-firstname'] ?? [];
$guestLastname = $_POST['guest-lastname'] ?? [];
$guestNote = $_POST['guest-note'] ?? [];

$guestgroupEmail = $_POST['guestgroup-email'] ?? [];
$guestgroupFirstname = $_POST['guestgroup-firstname'] ?? [];
$guestgroupLastname = $_POST['guestgroup-lastname'] ?? [];
$guestgroupNote = $_POST['guestgroup-note'] ?? [];

$guestgroups = $_POST['guestgroup'] ?? [];

$scheduleTime = $_POST['schedule-time'] ?? [];
$scheduleProgram = $_POST['schedule-program'] ?? [];
$scheduleMeeting = $_POST['schedule-meeting'] ?? [];

$wishes = $_POST['wish'] ?? [];

$todoDate = $_POST['todo-date'] ?? [];
$todoTime = $_POST['todo-time'] ?? [];
$todoText = $_POST['todo-text'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Registrierung - Pflichtfelder validieren
    $regUsername = clean("reg-username");
    $regFirstname = clean("reg-firstname");
    $regLastname = clean("reg-lastname");
    $regEmail = clean("reg-email");
    $regTelefon = clean("reg-telefon");
    $password = $_POST["reg-password"] ?? "";
    $password2 = $_POST["reg-password2"] ?? "";

    if (!$regUsername) $errors[] = "Gib einen Username ein";
    if (!$regFirstname) $errors[] = "Gib einen Vornamen ein";
    if (!$regLastname) $errors[] = "Gib einen Nachnamen ein";
    if (!$regEmail) $errors[] = "Gib eine Email an";
    if (!$regTelefon) $errors[] = "Gib eine Telefonnummer an";
    if (!$password) $errors[] = "Gib ein Passwort an";
    if (!$password2) $errors[] = "Wiederhole das Passwort";
    if ($password !== $password2) $errors[] = "Die Passwörter stimmen nicht überein";

    // Partner 1 validieren
    $bridalType1 = clean("bridal-type1");
    $bridalFirstname1 = clean("bridal-firstname1");
    $bridalLastname1 = clean("bridal-lastname1");
    $bridalEmail1 = clean("bridal-email1");
    $bridalTelefon1 = clean("bridal-telefon1");

    if (!$bridalType1) $errors[] = "Wähle eine Definition für den ersten Partner";
    if (!$bridalFirstname1) $errors[] = "Gib den Vornamen der ersten Person an";
    if (!$bridalLastname1) $errors[] = "Gib den Nachnamen der ersten Person an";
    if (!$bridalEmail1) $errors[] = "Gib die Email der ersten Person an";
    if (!$bridalTelefon1) $errors[] = "Gib die Telefonnummer der ersten Person an";

    // Partner 2 validieren
    $bridalType2 = clean("bridal-type2");
    $bridalFirstname2 = clean("bridal-firstname2");
    $bridalLastname2 = clean("bridal-lastname2");
    $bridalEmail2 = clean("bridal-email2");
    $bridalTelefon2 = clean("bridal-telefon2");

    if (!$bridalType2) $errors[] = "Wähle eine Definition für den zweiten Partner";
    if (!$bridalFirstname2) $errors[] = "Gib den Vornamen der zweiten Person an";
    if (!$bridalLastname2) $errors[] = "Gib den Nachnamen der zweiten Person an";
    if (!$bridalEmail2) $errors[] = "Gib die Email der zweiten Person an";
    if (!$bridalTelefon2) $errors[] = "Gib die Telefonnummer der zweiten Person an";

    // Hochzeitsinfos validieren
    $infoDate = clean("info-date");
    $infoTime = clean("info-time");
    $infoStyle = clean("info-style");
    $infoDresscode = clean("info-dresscode");
    $infoLocation1 = clean("info-location1");
    $infoLocation2 = clean("info-location2");

    if (!$infoDate) $errors[] = "Datum der Hochzeit fehlt";
    if (!$infoTime) $errors[] = "Zeit der Hochzeit fehlt";
    if (!$infoStyle) $errors[] = "Stil der Hochzeit fehlt";
    if (!$infoDresscode) $errors[] = "Dresscode fehlt";
    if (!$infoLocation1) $errors[] = "Ort 1 fehlt";

    if (empty($errors)) {
        try {
            $conn->begin_transaction();

            // Benutzer (Planner) speichern
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO User (Username, Phone, Email, Role, PasswordHash) VALUES (?, ?, ?, 'Planner', ?)");
            $stmt->bind_param("ssss", $regUsername, $regTelefon, $regEmail, $passwordHash);
            $stmt->execute();
            $plannerId = $conn->insert_id;

            // Hochzeit speichern mit PlannerId
            $stmt = $conn->prepare("INSERT INTO Wedding (Date, Time, Location1, Location2, CeremonyType, Dresscode, PlannerId) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $infoDate, $infoTime, $infoLocation1, $infoLocation2, $infoStyle, $infoDresscode, $plannerId);
            $stmt->execute();
            $weddingId = $conn->insert_id;

            // Partner 1 speichern
            $stmt = $conn->prepare("INSERT INTO Guest (WeddingId, FirstName, LastName, Phone, Email, AdditionalText, RSVP) VALUES (?, ?, ?, ?, ?, ?, 'Yes')");
            $stmt->bind_param("isssss", $weddingId, $bridalFirstname1, $bridalLastname1, $bridalTelefon1, $bridalEmail1, $bridalType1);
            $stmt->execute();
            $partner1Id = $conn->insert_id;

            // Partner 2 speichern
            $stmt = $conn->prepare("INSERT INTO Guest (WeddingId, FirstName, LastName, Phone, Email, AdditionalText, RSVP) VALUES (?, ?, ?, ?, ?, ?, 'Yes')");
            $stmt->bind_param("isssss", $weddingId, $bridalFirstname2, $bridalLastname2, $bridalTelefon2, $bridalEmail2, $bridalType2);
            $stmt->execute();
            $partner2Id = $conn->insert_id;

            // Partner IDs der Hochzeit zuweisen
            $stmt = $conn->prepare("UPDATE Wedding SET Partner1Id = ?, Partner2Id = ? WHERE WeddingId = ?");
            $stmt->bind_param("iii", $partner1Id, $partner2Id, $weddingId);
            $stmt->execute();

            // Zeitplan speichern
            foreach ($scheduleTime as $i => $time) {
                if ($time && $scheduleProgram[$i]) {
                    $stmt = $conn->prepare("INSERT INTO Schedule (WeddingId, Time, EventName, MeetingPoint) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $weddingId, $time, $scheduleProgram[$i], $scheduleMeeting[$i]);
                    $stmt->execute();
                }
            }

            // Einzelgäste speichern
            foreach ($guestEmail as $i => $email) {
                $firstname = $guestFirstname[$i] ?? null;
                $lastname = $guestLastname[$i] ?? null;
                $note = $guestNote[$i] ?? null;

                if ($email && $firstname && $lastname) {
                    $stmt = $conn->prepare("INSERT INTO Guest (WeddingId, FirstName, LastName, Email, AdditionalText, RSVP) VALUES (?, ?, ?, ?, ?, 'Pending')");
                    $stmt->bind_param("issss", $weddingId, $firstname, $lastname, $email, $note);
                    $stmt->execute();
                }
            }

            // Gästgruppen speichern
            foreach ($guestgroups as $grp) {
                $name = htmlspecialchars(trim($grp['name'] ?? ''));
                if (!$name) continue;

                // Gruppe anlegen
                $stmt = $conn->prepare("INSERT INTO GuestGroup (WeddingId, Name) VALUES (?, ?)");
                $stmt->bind_param("is", $weddingId, $name);
                $stmt->execute();
                $groupId = $conn->insert_id;

                // Gruppenmitglieder speichern
                foreach ($grp['members'] as $member) {
                    $email = htmlspecialchars(trim($member['email'] ?? ''));
                    $fn = htmlspecialchars(trim($member['firstname'] ?? ''));
                    $ln = htmlspecialchars(trim($member['lastname'] ?? ''));
                    $note = htmlspecialchars(trim($member['note'] ?? ''));

                    if ($email && $fn && $ln) {
                        $stmt = $conn->prepare("
                            INSERT INTO Guest (WeddingId, FirstName, LastName, Email, AdditionalText, RSVP, GuestGroupId)
                            VALUES (?, ?, ?, ?, ?, 'Pending', ?)
                        ");
                        $stmt->bind_param("issssi", $weddingId, $fn, $ln, $email, $note, $groupId);
                        $stmt->execute();
                    }
                }
            }

            // Wünsche speichern
            foreach ($wishes as $wish) {
                if ($wish) {
                    $stmt = $conn->prepare("INSERT INTO Gift (WeddingId, Name) VALUES (?, ?)");
                    $stmt->bind_param("is", $weddingId, $wish);
                    $stmt->execute();
                }
            }

            // ToDos speichern
            foreach ($todoText as $i => $text) {
                if ($text && $todoDate[$i]) {
                    $stmt = $conn->prepare("INSERT INTO ToDo (WeddingId, Name, Date, Time) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $weddingId, $text, $todoDate[$i], $todoTime[$i]);
                    $stmt->execute();
                }
            }

            $conn->commit();
            $answer["success"] = true;
            $_SESSION["loggedIn"] = true;
            $_SESSION["user"] = $regUsername;

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $answer["errors"][] = "Datenbankfehler: " . $e->getMessage();
        }
    } else {
        $answer["errors"] = $errors;
    }
}

header('Content-Type: application/json');
echo json_encode($answer);
?>
