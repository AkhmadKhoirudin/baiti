<?php
include_once 'db.php';

// Ambil daftar kelas
$kelas_result_filter = $conn->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");

// Filter berdasarkan kelas dan tanggal
$selected_kelas_id = $_GET['kelas'] ?? null;
$selected_tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$absensi_result = null;
$nama_kelas = '';

if ($selected_kelas_id) {
    // Ambil nama kelas
    $kelas_stmt = $conn->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
    $kelas_stmt->bind_param("i", $selected_kelas_id);
    $kelas_stmt->execute();
    $kelas_data = $kelas_stmt->get_result()->fetch_assoc();
    if ($kelas_data) {
        $nama_kelas = $kelas_data['nama_kelas'];
    }

    // Ambil data absensi
    $stmt = $conn->prepare("
        SELECT s.nama, a.status, a.keterangan, a.tanggal
        FROM absensi a
        JOIN siswa s ON a.siswa_nik = s.NIK
        WHERE a.kelas_id = ? AND a.tanggal = ?
        ORDER BY s.nama
    ");
    $stmt->bind_param("is", $selected_kelas_id, $selected_tanggal);
    $stmt->execute();
    $absensi_result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Laporan Absensi Siswa</h3>
        
        <!-- Tambahkan tombol print -->
        <!-- Form tanggal untuk pencetakan -->
        <form method="GET" action="list_absensi.php" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="<?= htmlspecialchars($_GET['tanggal_awal'] ?? '') ?>">
            </div>
            <div class="col-md-5">
                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= htmlspecialchars($_GET['tanggal_akhir'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Tampilkan Data</button>
            </div>
        </form>
        
        <div class="d-flex justify-content-between mb-4">
            <h4>Absensi Kelas: <?= htmlspecialchars($nama_kelas) ?></h4>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak</button>
        </div>

        <?php
        $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d');
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');
        
        $where_tanggal = "a.tanggal >= ? AND a.tanggal <= ?";
        $params = [$tanggal_awal, $tanggal_akhir];
        $types = "ss";
        if (!$selected_kelas_id) {
            $where_tanggal = "a.tanggal >= ? AND a.tanggal <= ?";
            $params = [$tanggal_awal, $tanggal_akhir];
            $types = "ss";
        } else {
            $where_tanggal = "a.kelas_id = ? AND a.tanggal >= ? AND a.tanggal <= ?";
            $params = [$selected_kelas_id, $tanggal_awal, $tanggal_akhir];
            $types = "iss";
        }
        
        $stmt = $conn->prepare("
            SELECT s.nama, a.status, a.keterangan, a.tanggal
            FROM absensi a
            JOIN siswa s ON a.siswa_nik = s.NIK
            WHERE $where_tanggal
            ORDER BY s.nama
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $absensi_result = $stmt->get_result();
        ?>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'sukses'): ?>
            <div class="alert alert-success">Absensi berhasil disimpan.</div>
        <?php endif; ?>

        <form method="GET" action="list_absensi.php" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="kelas" class="form-label">Pilih Kelas</label>
                <select name="kelas" id="kelas" class="form-select">
                    <option value="">-- Pilih Kelas --</option>
                    <?php while ($kelas = $kelas_result_filter->fetch_assoc()): ?>
                        <option value="<?= $kelas['id'] ?>" <?= ($selected_kelas_id == $kelas['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($selected_tanggal) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>

        <?php if ($selected_kelas_id && $absensi_result): ?>
            <h4>Absensi Kelas: <?= htmlspecialchars($nama_kelas) ?></h4>
            <p>Tanggal: <?= htmlspecialchars(date('d F Y', strtotime($selected_tanggal))) ?></p>

            <?php if ($absensi_result->num_rows > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($absen = $absensi_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($absen['nama']) ?></td>
                                <td>
                                    <?php 
                                        $status = htmlspecialchars($absen['status']);
                                        $badge_class = 'bg-secondary';
                                        if ($status === 'Hadir') $badge_class = 'bg-success';
                                        if ($status === 'Sakit') $badge_class = 'bg-warning text-dark';
                                        if ($status === 'Izin') $badge_class = 'bg-info text-dark';
                                        if ($status === 'Alpa') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                                </td>
                                <td><?= htmlspecialchars($absen['keterangan']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data absensi untuk kelas dan tanggal yang dipilih.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Styling khusus untuk print -->
    <style>
        @media print {
            body {
                background-color: #fff;
            }
            
            .container {
                max-width: 100%;
                padding: 0;
            }
            
            .table {
                border: 1px solid #000;
            }
            
            .table th, .table td {
                border: 1px solid #000;
                color: #000;
            }
            
            .btn, .mobile-menu-toggle {
                display: none !important;
            }
            
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
</body>
</html>