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

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($database);

// Read the SQL file
$sqlFile = 'database_lengkap.sql';
$sqlContent = file_get_contents($sqlFile);

// Remove comments
$sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
$sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

// Split into individual statements
$statements = preg_split('/;\s*\n/', $sqlContent);

// Execute each statement
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if ($conn->query($statement) === TRUE) {
            echo "Executed successfully: " . substr($statement, 0, 50) . "..." . "<br>";
        } else {
            echo "Error executing: " . $conn->error . "<br>";
        }
    }
}

echo "<br>Database import completed successfully!";
$conn->close();
?>