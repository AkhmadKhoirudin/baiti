<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $spp = $_POST['spp'] ?? '';

    if (empty($nama_kelas) || empty($spp)) {
        $error = "Nama kelas dan SPP tidak boleh kosong";
    } elseif (!is_numeric($spp)) {
        $error = "SPP harus berupa angka";
    } else {
        $stmt = $conn->prepare("INSERT INTO kelas (nama_kelas, spp) VALUES (?, ?)");
        $stmt->bind_param("si", $nama_kelas, $spp);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Kelas berhasil ditambahkan";
            header("Location: dahboard.php");
            exit;
        } else {
            $error = "Gagal menambahkan kelas: " . $conn->error;
        }
        $stmt->close();
    }
}
 
// Fetch classes for display
$classes = [];
$class_stmt = $conn->query("SELECT id, nama_kelas, spp, wali_kelas FROM kelas");
while ($class = $class_stmt->fetch_assoc()) {
    $classes[] = $class;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Data Kelas</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nama_kelas" class="form-label">Nama Kelas</label>
                <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" required>
            </div>
            <div class="mb-3">
                <label for="spp" class="form-label">SPP</label>
                <input type="number" class="form-control" id="spp" name="spp" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dahboard.php" class="btn btn-secondary">Batal</a>
        </form>
     
        <h3 class="mt-5">Daftar Kelas</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>SPP</th>
                    <th>Wali Kelas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $index => $class): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($class['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($class['spp']) ?></td>
                        <td><?= htmlspecialchars($class['wali_kelas']) ?></td>
                        <td>
                            <a href="edit_kelas.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="hapus_kelas.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </body>
    </html>