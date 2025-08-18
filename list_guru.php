<?php
include_once 'db.php';

$stmt = $conn->prepare("SELECT * FROM guru");
$stmt->execute();
$guru_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>List Guru</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Gaji</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($guru = $guru_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($guru['NIP']) ?></td>
                        <td><?= htmlspecialchars($guru['nama']) ?></td>
                        <td>Rp. <?= number_format($guru['gaji'], 2, ',', '.') ?></td>
                        <td>
                            <a href="edit_guru.php?NIP=<?= urlencode($guru['NIP']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="hapus_guru.php?NIP=<?= urlencode($guru['NIP']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>