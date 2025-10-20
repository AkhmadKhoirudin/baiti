<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama'] ?? '';
    $mapel  = $_POST['mapel'] ?? '';
    $no_hp  = $_POST['no_hp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $gaji = $_POST['gaji'] ?? '';

    $stmt = $conn->prepare("INSERT INTO guru (nama, mapel, no_hp, alamat, gaji) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nama, $mapel, $no_hp, $alamat, $gaji);
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
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mapel" class="form-label">Mapel</label>
                <input type="text" name="mapel" id="mapel" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">No HP</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" name="alamat" id="alamat" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gaji" class="form-label">Gaji</label>
                <input type="number" name="gaji" id="gaji" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>