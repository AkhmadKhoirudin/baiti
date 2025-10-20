<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $del = $conn->prepare("DELETE FROM absen_guru WHERE id=?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        echo "<script>alert('Absen guru berhasil dihapus');window.location='list_absenguru.php';</script>"; exit;
    } else {
        $err = htmlspecialchars($del->error, ENT_QUOTES);
        echo "<script>alert('Gagal hapus absen: $err');window.location='list_absenguru.php';</script>"; exit;
    }
} else {
    header("Location: list_absenguru.php");
    exit;
}
?>