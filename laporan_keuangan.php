<?php
include_once 'db.php';

// Set default bulan dan tahun
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Hitung total pemasukan dan pengeluaran
$total_pemasukan_stmt = $conn->prepare(
    "SELECT SUM(nominal) as total 
     FROM pemasukan 
     WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?"
);
$total_pemasukan_stmt->bind_param("ss", $bulan, $tahun);
$total_pemasukan_stmt->execute();
$total_pemasukan_result = $total_pemasukan_stmt->get_result();
$total_pemasukan = $total_pemasukan_result->fetch_assoc()['total'] ?? 0;

$total_pengeluaran_stmt = $conn->prepare(
    "SELECT SUM(nominal) as total 
     FROM pengeluaran 
     WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?"
);
$total_pengeluaran_stmt->bind_param("ss", $bulan, $tahun);
$total_pengeluaran_stmt->execute();
$total_pengeluaran_result = $total_pengeluaran_stmt->get_result();
$total_pengeluaran = $total_pengeluaran_result->fetch_assoc()['total'] ?? 0;

$saldo = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Laporan Keuangan Bulanan - <?= htmlspecialchars(date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun))) ?></h3>

        <!-- Filter Bulan dan Tahun -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="bulan" class="form-label">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="form-control">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <input type="number" name="tahun" id="tahun" class="form-control" value="<?= htmlspecialchars($tahun) ?>" min="2020" max="<?= date('Y') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>

        <!-- Ringkasan Keuangan -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <p class="card-text display-6">Rp. <?= number_format($total_pemasukan, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <p class="card-text display-6">Rp. <?= number_format($total_pengeluaran, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Saldo</h5>
                        <p class="card-text display-6 <?= $saldo < 0 ? 'text-danger' : '' ?>">Rp. <?= number_format($saldo, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>