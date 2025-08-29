<?php
include_once 'db.php';

$kelas_id = $_GET['kelas'] ?? null;
$kelas = null; // Inisialisasi variabel $kelas

// Validasi kelas
if ($kelas_id) {
    $check_kelas = $conn->prepare("SELECT * FROM kelas WHERE id = ?");
    $check_kelas->bind_param("i", $kelas_id);
    $check_kelas->execute();
    $check_result = $check_kelas->get_result();
    
    if ($check_result->num_rows === 0) {
        $kelas_id = null;
    } else {
        $kelas = $check_result->fetch_assoc(); // Ambil data kelas
    }
}

// Query data siswa
if ($kelas_id) {
    $siswa_query = $conn->prepare("SELECT s.NIK, s.nama, s.status, k.nama_kelas
                                    FROM siswa s
                                    LEFT JOIN kelas k ON s.kelas = k.id
                                    WHERE s.kelas = ?");
    $siswa_query->bind_param("i", $kelas_id);
    $siswa_query->execute();
    $siswa_result = $siswa_query->get_result();
} else {
    $siswa_result = $conn->query("SELECT s.NIK, s.nama, s.status, k.nama_kelas
                                    FROM siswa s
                                    LEFT JOIN kelas k ON s.kelas = k.id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3><?= $kelas_id && $kelas ? 'Data Siswa Kelas ' . htmlspecialchars($kelas['nama_kelas']) : 'Data Siswa' ?></h3>
        <a href="input_siswa.php?menu=data_siswa" class="btn btn-primary mb-3">Tambah Siswa</a>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($siswa = $siswa_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($siswa['NIK']) ?></td>
                            <td><?= htmlspecialchars($siswa['nama']) ?></td>
                            <td><?= htmlspecialchars($siswa['nama_kelas'] ?? 'Tanpa Kelas') ?></td>
                            <td>
                             <span class="badge <?= strtolower($siswa['status']) === 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                 <?= htmlspecialchars($siswa['status']) ?>
                            </span>

                       
                        </td>
                        <td>
                            <a href="edit_siswa.php?nik=<?= urlencode($siswa['NIK']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus_siswa.php?nik=<?= urlencode($siswa['NIK']) ?>" class="btn btn-sm btn-danger">Hapus</a>
                        </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>