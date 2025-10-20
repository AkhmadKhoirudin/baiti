<?php
include_once '../db.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Hapus data
$delete_query = "DELETE FROM du WHERE id = $id";

if ($conn->query($delete_query)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location.href='list.php';</script>";
} else {
    echo "<script>alert('Error: " . $conn->error . "'); window.location.href='list.php';</script>";
}
?>