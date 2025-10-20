<?php
include_once 'db.php';

// Set tanggal default ke hari ini jika tidak ada filter
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Ambil data pemasukan Sekben I pada tanggal yang dipilih
$pemasukan1_stmt = $conn->prepare("SELECT SUM(nominal) as total FROM pemasukan WHERE sekben = 'I' AND tanggal = ?");
$pemasukan1_stmt->bind_param("s", $tanggal);
$pemasukan1_stmt->execute();
$total_pemasukan1 = $pemasukan1_stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Ambil data pengeluaran Sekben I pada tanggal yang dipilih
$pengeluaran1_stmt = $conn->prepare("SELECT SUM(nominal) as total FROM pengeluaran WHERE sekben = 'I' AND tanggal = ?");
$pengeluaran1_stmt->bind_param("s", $tanggal);
$pengeluaran1_stmt->execute();
$total_pengeluaran1 = $pengeluaran1_stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Ambil data pemasukan Sekben II pada tanggal yang dipilih
$pemasukan2_stmt = $conn->prepare("SELECT SUM(nominal) as total FROM pemasukan WHERE sekben = 'II' AND tanggal = ?");
$pemasukan2_stmt->bind_param("s", $tanggal);
$pemasukan2_stmt->execute();
$total_pemasukan2 = $pemasukan2_stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Ambil data pengeluaran Sekben II pada tanggal yang dipilih
$pengeluaran2_stmt = $conn->prepare("SELECT SUM(nominal) as total FROM pengeluaran WHERE sekben = 'II' AND tanggal = ?");
$pengeluaran2_stmt->bind_param("s", $tanggal);
$pengeluaran2_stmt->execute();
$total_pengeluaran2 = $pengeluaran2_stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Hitung total
$total_pemasukan = $total_pemasukan1 + $total_pemasukan2;
$total_pengeluaran = $total_pengeluaran1 + $total_pengeluaran2;
$saldo_harian = $total_pemasukan - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header { font-weight: bold; }
        .summary-card { border-left: 5px solid; }
        .border-success { border-left-color: #198754 !important; }
        .border-danger { border-left-color: #dc3545 !important; }
        .border-info { border-left-color: #0dcaf0 !important; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Laporan Harian - <?= htmlspecialchars(date('d F Y', strtotime($tanggal))) ?></h3>

        <!-- Filter Tanggal -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="tanggal" class="form-label">Pilih Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Ringkasan -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card summary-card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <p class="card-text fs-4">Rp. <?= number_format($total_pemasukan, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card border-danger">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <p class="card-text fs-4">Rp. <?= number_format($total_pengeluaran, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card border-info">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Hari Ini</h5>
                        <p class="card-text fs-4">Rp. <?= number_format($saldo_harian, 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">Detail Pemasukan</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pemasukan Sekben I (SPP)
                            <span class="badge bg-primary rounded-pill">Rp. <?= number_format($total_pemasukan1, 2, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pemasukan Sekben II (DU/B)
                            <span class="badge bg-primary rounded-pill">Rp. <?= number_format($total_pemasukan2, 2, ',', '.') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">Detail Pengeluaran</div>
                    <ul class="list-group list-group-flush">
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pengeluaran Sekben I (SPP)
                            <span class="badge bg-warning text-dark rounded-pill">Rp. <?= number_format($total_pengeluaran1, 2, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pengeluaran Sekben II (Event)
                            <span class="badge bg-warning text-dark rounded-pill">Rp. <?= number_format($total_pengeluaran2, 2, ',', '.') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>