<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $NIP = $_POST['NIP'];
    $nama = $_POST['nama'];
    $gaji = $_POST['gaji'];

    $stmt = $conn->prepare("INSERT INTO guru (NIP, nama, gaji) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $NIP, $nama, $gaji);
    $stmt->execute();

    header("Location: list_guru.php?status=sukses");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Guru</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="NIP" class="form-label">NIP</label>
                <input type="text" name="NIP" id="NIP" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gaji" class="form-label">Gaji</label>
                <input type="number" step="0.01" name="gaji" id="gaji" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>