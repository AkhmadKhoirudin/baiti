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
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($database);

// Create kelas table
$kelas_table = "CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(50) NOT NULL
)";
if ($conn->query($kelas_table) === TRUE) {
    echo "Kelas table created successfully";
} else {
    echo "Error creating kelas table: " . $conn->error;
}

// Create siswa table
$siswa_table = "CREATE TABLE IF NOT EXISTS siswa (
    NIK VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kelas_id INT,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id)
)";
if ($conn->query($siswa_table) === TRUE) {
    echo "Siswa table created successfully";
} else {
    echo "Error creating siswa table: " . $conn->error;
}

// Create pengguna table
$pengguna_table = "CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('sekben1', 'sekben2', 'admin') NOT NULL
)";
if ($conn->query($pengguna_table) === TRUE) {
    echo "Pengguna table created successfully";
} else {
    echo "Error creating pengguna table: " . $conn->error;
}

// Create pemasukan table
$pemasukan_table = "CREATE TABLE IF NOT EXISTS pemasukan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    nominal DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    siswa_NIK VARCHAR(20),
    sekben ENUM('I', 'II') NOT NULL,
    FOREIGN KEY (siswa_NIK) REFERENCES siswa(NIK)
)";
if ($conn->query($pemasukan_table) === TRUE) {
    echo "Pemasukan table created successfully";
} else {
    echo "Error creating pemasukan table: " . $conn->error;
}

// Create pengeluaran table
$pengeluaran_table = "CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    nominal DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    sekben ENUM('I', 'II') NOT NULL
)";
if ($conn->query($pengeluaran_table) === TRUE) {
    echo "Pengeluaran table created successfully";
} else {
    echo "Error creating pengeluaran table: " . $conn->error;
}

$conn->close();
?>