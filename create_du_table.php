<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "tqp_management";

// Create database connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the database
$conn->select_db($database);

// Read the SQL file
$sqlFile = 'create_du_table.sql';
$sqlContent = file_get_contents($sqlFile);

// Split SQL statements by semicolon
$sqlStatements = explode(';', $sqlContent);

// Execute each SQL statement
foreach ($sqlStatements as $sql) {
    $sql = trim($sql);
    if (!empty($sql)) {
        if ($conn->query($sql) === TRUE) {
            echo "Query berhasil dieksekusi: $sql<br>";
        } else {
            echo "Error executing query: $sql<br>" . $conn->error . "<br>";
        }
    }
}

echo "Tabel 'du' berhasil dibuat atau sudah ada.<br>";

$conn->close();
?>