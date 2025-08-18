<?php
include_once 'db.php';

// Ambil daftar guru
$guru_result = $conn->query("SELECT NIK, nama FROM guru ORDER BY nama");

// Proses form absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $absensi_data = $_POST['absensi'];

    $stmt = $conn->prepare("INSERT INTO absensi_guru (guru_nik, tanggal, status, keterangan) VALUES (?, ?, ?, ?)");
    foreach ($absensi_data as $nik => $data) {
        $status = $data['status'];
        $keterangan = $data['keterangan'] ?? '';
        $stmt->bind_param("ssss", $nik, $tanggal, $status, $keterangan);
        $stmt->execute();
    }

    header("Location: list_absensi_guru.php?tanggal=" . urlencode($tanggal) . "&status=sukses");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF8">
    <meta name="viewport" content="width=device-width, initialscale=1.0">
    <title>Input Absensi Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt5">
        <h3>Input Absensi Guru</h3>

        <form method="POST">
            <div class="mb3">
                <label for="tanggal" class="formlabel">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="formcontrol" value="<?= date('Y-m-d') ?>" required>
            </div>

            <table class="table tablebordered">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>Status Kehadiran</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($guru = $guru_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($guru['nama']) ?></td>
                            <td>
                                <select name="absensi[<?= $guru['NIK'] ?>][status]" class="formselect">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="absensi[<?= $guru['NIK'] ?>][keterangan]" class="formcontrol" placeholder="Keterangan (jika perlu)">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btnprimary">Simpan Absensi</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>