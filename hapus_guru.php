<?php
include_once 'db.php';

if (isset($_GET['NIP'])) {
    $NIP = $_GET['NIP'];
    $stmt = $conn->prepare("DELETE FROM guru WHERE NIP = ?");
    $stmt->bind_param("s", $NIP);
    $stmt->execute();

    header("Location: list_guru.php?status=sukses");
    exit;
}
?>