<?php
include_once '../db.php';

// ================== QUERY PEMASUKAN (contoh: status = lunas) ==================
$q_pemasukan = $conn->query("SELECT spp.*, siswa.nama as nama_siswa
    FROM spp LEFT JOIN siswa ON spp.siswa_nik=siswa.NIK
    WHERE spp.status='pemasukan'
    ORDER BY spp.date DESC");

// ================== QUERY PENGELUARAN (contoh: status = keluar) ==================
$q_pengeluaran = $conn->query("SELECT spp.*, siswa.nama as nama_siswa
    FROM spp LEFT JOIN siswa ON spp.siswa_nik=siswa.NIK
    WHERE spp.status='pengeluaran'
    ORDER BY spp.date DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pemasukan & Pengeluaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

  <!-- ===================== PEMASUKAN ===================== -->
  <h3 class="text-success">💰 Daftar Pemasukan</h3>
  <table class="table table-bordered table-striped table-sm mt-3">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Tahun Ajaran</th>
        <th>Tanggal</th>
        <th>Nominal</th>
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
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= number_format($row['biaya_spp']) ?></td>
          <td><span class="badge bg-success">pemasukan</span></td>
         
          <td>
            <a href="edit_spp.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="hapus_spp.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- ===================== PENGELUARAN ===================== -->
  <h3 class="text-danger mt-5">📤 Daftar Pengeluaran</h3>
  <table class="table table-bordered table-striped table-sm mt-3">
    <thead class="table-danger">
      <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Nominal</th>
        <th>Status</th>
        <th>Keterangan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $q_pengeluaran->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
       
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= number_format($row['biaya_spp']) ?></td>
          <td><span class="badge bg-danger">pengeluaran</span></td>
          <td><?= htmlspecialchars($row['ket']) ?></td>
          <td>
            <a href="edit_spp.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="hapus_spp.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</div>
</body>
</html>
