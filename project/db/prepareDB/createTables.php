<?php
include '../mysql.php';

$sqlFile = 'createTables.sql';

if (file_exists($sqlFile)) {
    $sql = file_get_contents($sqlFile);

    // SQL in einzelne Anweisungen aufteilen
    $queries = explode(";", $sql);

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === TRUE) {
                echo "Query executed successfully: " . $query . "<br>";
            } else {
                echo "Error executing query: " . $query . "<br>" . $conn->error . "<br>";
            }
        }
    }
} else {
    echo "SQL file not found.";
}

$conn->close();
?>
