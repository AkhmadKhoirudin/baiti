<?php
include_once '../db.php';

$delete_msg = "";
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM guru WHERE id=?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        $delete_msg = "<div class='alert alert-success'>Data guru berhasil dihapus.</div>";
    } else {
        $delete_msg = "<div class='alert alert-danger'>Gagal menghapus data guru.</div>";
    }
}
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
        <?php if($delete_msg) echo $delete_msg; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Mapel</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($guru = $guru_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($guru['id']) ?></td>
                        <td><?= htmlspecialchars($guru['nama']) ?></td>
                        <td><?= htmlspecialchars($guru['mapel']) ?></td>
                        <td><?= htmlspecialchars($guru['no_hp']) ?></td>
                        <td><?= htmlspecialchars($guru['alamat']) ?></td>
                        <td>
                            <a href="edit_guru.php?id=<?= urlencode($guru['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="list_guru.php?delete=<?= urlencode($guru['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data guru?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>