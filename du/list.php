<?php
include_once '../db.php';

// ================== QUERY TOTAL PERBULAN ==================
$q_total_bulan = $conn->query("
    SELECT
        DATE_FORMAT(tanggal, '%Y-%m') as bulan,
        SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) as total_pemasukan,
        SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END) as total_pengeluaran,
        (SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) -
         SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END)) as saldo
    FROM du
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC
");

// ================== QUERY TOTAL KESELURUHAN ==================
$q_total_keseluruhan = $conn->query("
    SELECT
        SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) as total_pemasukan_all,
        SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END) as total_pengeluaran_all,
        (SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) -
         SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END)) as saldo_all
    FROM du
");

$total_all = $q_total_keseluruhan->fetch_assoc();

// ================== QUERY BULAN INI ==================
$bulan_ini = date('Y-m');
$q_bulan_ini = $conn->query("
    SELECT
        SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) as pemasukan_bulan_ini,
        SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END) as pengeluaran_bulan_ini,
        (SUM(CASE WHEN status = 'pemasukan' THEN nominal ELSE 0 END) -
         SUM(CASE WHEN status = 'pengeluaran' THEN nominal ELSE 0 END)) as saldo_bulan_ini
    FROM du
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'
");

$bulan_ini_data = $q_bulan_ini->fetch_assoc();

// ================== QUERY PEMASUKAN ==================
$q_pemasukan = $conn->query("SELECT du.*, siswa.nama as nama_siswa
    FROM du LEFT JOIN siswa ON du.siswa_nik=siswa.NIK
    WHERE du.status='pemasukan'
    ORDER BY du.tanggal DESC");

// ================== QUERY PENGELUARAN ==================
$q_pengeluaran = $conn->query("SELECT du.*, siswa.nama as nama_siswa
    FROM du LEFT JOIN siswa ON du.siswa_nik=siswa.NIK
    WHERE du.status='pengeluaran'
    ORDER BY du.tanggal DESC");

// Hitung jumlah data untuk pagination
$jumlah_pemasukan = $q_pemasukan->num_rows;
$jumlah_pengeluaran = $q_pengeluaran->num_rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan DU</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    .card-summary {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .card-summary:hover {
      transform: translateY(-5px);
    }
    .nav-tabs .nav-link.active {
      font-weight: bold;
    }
    .table-container {
      max-height: 500px;
      overflow-y: auto;
    }
    .summary-card {
      border-left: 4px solid;
    }
    .summary-income {
      border-left-color: #28a745;
    }
    .summary-expense {
      border-left-color: #dc3545;
    }
    .summary-balance {
      border-left-color: #007bff;
    }
    .table th {
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
      z-index: 10;
    }
  </style>
</head>
<body>
<div class="container-fluid py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-primary">
      <i class="bi bi-graph-up"></i> Laporan Keuangan DU
    </h1>
    <div class="btn-group">
      <button type="button" class="btn btn-outline-primary" id="printBtn">
        <i class="bi bi-printer"></i> Cetak
      </button>
      <button type="button" class="btn btn-outline-success" id="exportBtn">
        <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor
      </button>
    </div>
  </div>

  <!-- Ringkasan Keuangan -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card card-summary summary-card summary-income">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="card-title text-success">Total Pemasukan</h5>
              <h3 class="text-success">Rp <?= number_format($total_all['total_pemasukan_all'] ?? 0) ?></h3>
            </div>
            <div class="align-self-center">
              <i class="bi bi-arrow-down-circle text-success" style="font-size: 2rem;"></i>
            </div>
          </div>
          <p class="card-text text-muted">Bulan ini: Rp <?= number_format($bulan_ini_data['pemasukan_bulan_ini'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-summary summary-card summary-expense">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="card-title text-danger">Total Pengeluaran</h5>
              <h3 class="text-danger">Rp <?= number_format($total_all['total_pengeluaran_all'] ?? 0) ?></h3>
            </div>
            <div class="align-self-center">
              <i class="bi bi-arrow-up-circle text-danger" style="font-size: 2rem;"></i>
            </div>
          </div>
          <p class="card-text text-muted">Bulan ini: Rp <?= number_format($bulan_ini_data['pengeluaran_bulan_ini'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-summary summary-card summary-balance">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="card-title text-primary">Saldo</h5>
              <h3 class="text-primary">Rp <?= number_format($total_all['saldo_all'] ?? 0) ?></h3>
            </div>
            <div class="align-self-center">
              <i class="bi bi-wallet2 text-primary" style="font-size: 2rem;"></i>
            </div>
          </div>
          <p class="card-text text-muted">Bersih bulan ini: Rp <?= number_format($bulan_ini_data['saldo_bulan_ini'] ?? 0) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab Navigasi -->
  <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="monthly" aria-selected="true">
        <i class="bi bi-calendar-month"></i> Ringkasan Bulanan
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button" role="tab" aria-controls="income" aria-selected="false">
        <i class="bi bi-arrow-down-circle"></i> Pemasukan
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense" type="button" role="tab" aria-controls="expense" aria-selected="false">
        <i class="bi bi-arrow-up-circle"></i> Pengeluaran
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="myTabContent">
    <!-- Tab Ringkasan Bulanan -->
    <div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">
            <i class="bi bi-calendar-range"></i> Ringkasan Keuangan Per Bulan
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover table-striped mb-0">
              <thead class="table-primary">
                <tr>
                  <th>Bulan</th>
                  <th class="text-end">Total Pemasukan</th>
                  <th class="text-end">Total Pengeluaran</th>
                  <th class="text-end">Saldo</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $q_total_bulan->fetch_assoc()): ?>
                  <tr>
                    <td><?= date('F Y', strtotime($row['bulan'] . '-01')) ?></td>
                    <td class="text-end text-success">Rp <?= number_format($row['total_pemasukan']) ?></td>
                    <td class="text-end text-danger">Rp <?= number_format($row['total_pengeluaran']) ?></td>
                    <td class="text-end <?= $row['saldo'] >= 0 ? 'text-success' : 'text-danger' ?>">
                      Rp <?= number_format($row['saldo']) ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Total <?= $q_total_bulan->num_rows ?> bulan
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Pemasukan -->
    <div class="tab-pane fade" id="income" role="tabpanel" aria-labelledby="income-tab">
      <div class="card">
        <div class="card-header bg-success text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-arrow-down-circle"></i> Daftar Pemasukan
            </h5>
            <div class="d-flex">
              <input type="text" class="form-control form-control-sm me-2" placeholder="Cari..." id="searchIncome">
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover table-striped mb-0">
              <thead class="table-success">
                <tr>
                  <th>ID</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Tahun Ajaran</th>
                  <th>Tanggal</th>
                  <th class="text-end">Nominal</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $q_pemasukan->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['siswa_nik']) ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_ajaran']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td class="text-end">Rp <?= number_format($row['nominal']) ?></td>
                    <td><span class="badge bg-success">pemasukan</span></td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="edit_du.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hapus_du.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                          <i class="bi bi-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Total <?= $jumlah_pemasukan ?> data pemasukan
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Pengeluaran -->
    <div class="tab-pane fade" id="expense" role="tabpanel" aria-labelledby="expense-tab">
      <div class="card">
        <div class="card-header bg-danger text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-arrow-up-circle"></i> Daftar Pengeluaran
            </h5>
            <div class="d-flex">
              <input type="text" class="form-control form-control-sm me-2" placeholder="Cari..." id="searchExpense">
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-container">
            <table class="table table-hover table-striped mb-0">
              <thead class="table-danger">
                <tr>
                  <th>ID</th>
                  <th>Tanggal</th>
                  <th class="text-end">Nominal</th>
                  <th>Status</th>
                  <th>Keterangan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $q_pengeluaran->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td class="text-end">Rp <?= number_format($row['nominal']) ?></td>
                    <td><span class="badge bg-danger">pengeluaran</span></td>
                    <td><?= htmlspecialchars($row['ket']) ?></td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="edit_du.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hapus_du.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                          <i class="bi bi-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Total <?= $jumlah_pengeluaran ?> data pengeluaran
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Fungsi untuk pencarian
  document.getElementById('searchIncome').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#income tbody tr');
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });
  
  document.getElementById('searchExpense').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#expense tbody tr');
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });
  
  // Fungsi untuk tombol cetak
  document.getElementById('printBtn').addEventListener('click', function() {
    const activeTab = document.querySelector('.nav-link.active').getAttribute('data-bs-target');
    const tabContent = document.querySelector(activeTab);
    
    // Simpan konten asli
    const originalContent = document.body.innerHTML;
    
    // Buat konten untuk dicetak
    const printContent = `
      <html>
        <head>
          <title>Laporan Keuangan DU</title>
          <style>
            body { font-family: Arial, sans-serif; }
            .table { width: 100%; border-collapse: collapse; }
            .table th, .table td { border: 1px solid #ddd; padding: 8px; }
            .table th { background-color: #f2f2f2; }
            .text-success { color: green; }
            .text-danger { color: red; }
            .text-end { text-align: right; }
          </style>
        </head>
        <body>
          <h2>Laporan Keuangan DU</h2>
          <p>Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
          ${tabContent.innerHTML}
        </body>
      </html>
    `;
    
    // Ganti konten body dengan konten cetak
    document.body.innerHTML = printContent;
    window.print();
    
    // Kembalikan konten asli
    document.body.innerHTML = originalContent;
    window.location.reload();
  });
  
  // Fungsi untuk tombol ekspor
  document.getElementById('exportBtn').addEventListener('click', function() {
    alert('Fitur ekspor akan segera tersedia!');
  });
</script>
</body>
</html>
