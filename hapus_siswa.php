<?php
include_once 'db.php';

if (!isset($_GET['nik']) || !is_string($_GET['nik'])) {
    die('NIK tidak valid.');
}

$nik = $_GET['nik'];

// Prepare delete statement
$delete_stmt = $conn->prepare("DELETE FROM siswa WHERE NIK = ?");
$delete_stmt->bind_param("s", $nik);

if ($delete_stmt->execute()) {
    header("Location: list_siswa.php");
    exit;
} else {
    die("Terjadi kesalahan saat menghapus data.");
}
?>