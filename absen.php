<?php
include_once 'db.php';

// Ambil daftar kelas
$kelas_result = $conn->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");

// Proses form absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas_id = $_POST['kelas_id'];
    $tanggal = $_POST['tanggal'];
    $absensi_data = $_POST['absensi'];

    $stmt = $conn->prepare("INSERT INTO absensi (siswa_nik, kelas_id, tanggal, status, keterangan) VALUES (?, ?, ?, ?, ?)");

    foreach ($absensi_data as $nik => $data) {
        $status = $data['status'];
        $keterangan = $data['keterangan'] ?? '';
        $stmt->bind_param("sisss", $nik, $kelas_id, $tanggal, $status, $keterangan);
        $stmt->execute();
    }

    header("Location: list_absensi.php?kelas=" . urlencode($kelas_id) . "&tanggal=" . urlencode($tanggal) . "&status=sukses");
    exit;
}

// Ambil siswa berdasarkan kelas yang dipilih
$selected_kelas_id = $_GET['kelas'] ?? null;
$siswa_result = null;
if ($selected_kelas_id) {
    $stmt = $conn->prepare("SELECT NIK, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama");
    $stmt->bind_param("i", $selected_kelas_id);
    $stmt->execute();
    $siswa_result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Absensi Siswa</h3>

        <form method="GET" action="absen.php" class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="kelas" class="form-label">Pilih Kelas</label>
                <select name="kelas" id="kelas" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php while ($kelas = $kelas_result->fetch_assoc()): ?>
                        <option value="<?= $kelas['id'] ?>" <?= ($selected_kelas_id == $kelas['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <?php if ($selected_kelas_id && $siswa_result && $siswa_result->num_rows > 0): ?>
            <form method="POST" action="absen.php">
                <input type="hidden" name="kelas_id" value="<?= $selected_kelas_id ?>">
                
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($siswa = $siswa_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($siswa['nama']) ?></td>
                                <td>
                                    <input type="hidden" name="absensi[<?= $siswa['NIK'] ?>]">
                                    <select name="absensi[<?= $siswa['NIK'] ?>][status]" class="form-select">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Alpa">Alpa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="absensi[<?= $siswa['NIK'] ?>][keterangan]" class="form-control" placeholder="Keterangan (jika perlu)">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
            </form>
        <?php elseif ($selected_kelas_id): ?>
            <div class="alert alert-warning">Tidak ada siswa aktif di kelas ini.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>