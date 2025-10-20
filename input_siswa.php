<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = $_POST['nik'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $kelas_id = $_POST['kelas_id'] ?? '';
    $status = $_POST['status'] ?? '';

    $errors = [];

    if (empty($nik)) {
        $errors[] = "NIK tidak boleh kosong";
    }

    if (empty($nama)) {
        $errors[] = "Nama tidak boleh kosong";
    }

    if (empty($kelas_id)) {
        $errors[] = "Kelas tidak boleh kosong";
    }

    if (empty($status)) {
        $errors[] = "Status tidak boleh kosong";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO siswa (NIK, nama, kelas, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $nik, $nama, $kelas_id, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Siswa berhasil ditambahkan";
            header("Location: list_siswa.php?kelas_id=" . urlencode($kelas_id));
            exit;
        } else {
            $errors[] = "Gagal menambahkan siswa: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all kelas for dropdown
$kelas_query = "SELECT * FROM kelas";
$kelas_result = $conn->query($kelas_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Data Siswa</h3>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik" required>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select class="form-select" id="kelas_id" name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php while($kelas = $kelas_result->fetch_assoc()): ?>
                        <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dahboard.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>