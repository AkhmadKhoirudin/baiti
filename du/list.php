<?php
include_once '../db.php';

// Ambil filter kelas & pencarian (jika ada)
$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$cari  = isset($_GET['cari']) ? $_GET['cari'] : '';

// Query untuk mengambil data kelas dengan id dan nama
$q_kelas = $conn->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_options = [];
while ($row = $q_kelas->fetch_assoc()) {
    $kelas_options[$row['id']] = $row['nama_kelas'];
}


// ================== HANDLE DELETE ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM du WHERE id=?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus');window.location='list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data');window.location='list.php';</script>";
        exit;
    }
}

// ================== HANDLE UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id     = intval($_POST['edit_id']);
    $tanggal = $_POST['edit_tanggal'] ?? '';
    $nominal = $_POST['edit_nominal'] ?? '';
    $ket     = $_POST['edit_ket'] ?? '';
    $tahun_ajaran = $_POST['edit_tahun_ajaran'] ?? null;
    $siswa_nik    = $_POST['edit_siswa_nik'] ?? null;
    $status       = $_POST['edit_status'] ?? '';

    $update_stmt = $conn->prepare("UPDATE du SET tanggal=?, tahun_ajaran=?, siswa_nik=?, nominal=?, ket=?, status=? WHERE id=?");
    $update_stmt->bind_param("sssissi", $tanggal, $tahun_ajaran, $siswa_nik, $nominal, $ket, $status, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate');window.location='list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update data');window.location='list.php';</script>";
        exit;
    }
}

// ================== QUERY PEMASUKAN ==================
// Tambahkan filter pencarian
$search_condition = '';
if (!empty($cari)) {
    $search_condition .= " AND (siswa.nama LIKE '%$cari%' OR du.siswa_nik LIKE '%$cari%' OR du.ket LIKE '%$cari%')";
}
if (!empty($kelas)) {
    // Ubah kondisi filtering untuk menggunakan id kelas
    $search_condition .= " AND siswa.kelas_id = '$kelas'";
}

$q_pemasukan = $conn->query("SELECT du.*, siswa.nama as nama_siswa
    FROM du
    LEFT JOIN siswa ON du.siswa_nik=siswa.NIK
    WHERE du.status='pemasukan' $search_condition
    ORDER BY du.tanggal DESC");

// ================== QUERY PENGELUARAN ==================
// Tambahkan filter pencarian untuk pengeluaran (jika diperlukan)
$q_pengeluaran = $conn->query("SELECT du.*, siswa.nama as nama_siswa
    FROM du
    LEFT JOIN siswa ON du.siswa_nik=siswa.NIK
    WHERE du.status='pengeluaran' $search_condition
    ORDER BY du.tanggal DESC");

// Hitung total pemasukan
$total_pemasukan = 0;
$data_pemasukan = [];
while ($row = $q_pemasukan->fetch_assoc()) {
    $data_pemasukan[] = $row;
    $total_pemasukan += (int)$row['nominal'];
}

// Hitung total pengeluaran
$total_pengeluaran = 0;
$data_pengeluaran = [];
while ($row = $q_pengeluaran->fetch_assoc()) {
    $data_pengeluaran[] = $row;
    $total_pengeluaran += (int)$row['nominal'];
}

// Hitung saldo akhir
$saldo = $total_pemasukan - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pemasukan & Pengeluaran DU</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- // Tambahkan form pencarian di atas container -->
<div class="container mt-5">
  <form method="GET" class="mb-4 row g-2">
    <div class="col-auto">
      <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" class="form-control" placeholder="Cari nama/NIS...">
    </div>
    <!-- <div class="col-auto">
      <select name="kelas" class="form-select">
        <option value="">Pilih Kelas</option>
        <?php
        foreach ($kelas_options as $id => $nama_kelas) {
            $selected = ($kelas == $nama_kelas) ? 'selected' : '';
            echo "<option value='$id' $selected>$nama_kelas</option>";
        }
        ?>
      </select>
    </div> -->
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Tampilkan</button>
    </div>
  </form>

  <!-- ===================== PEMASUKAN DU ===================== -->
  <h3 class="text-success">💰 Daftar Pemasukan DU</h3>
  <table class="table table-bordered table-striped table-sm mt-3">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Tahun Ajaran</th>
        <th>Tanggal</th>
        <th>Nominal</th>
        <th>Keterangan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($data_pemasukan) > 0): ?>
        <?php foreach ($data_pemasukan as $row): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['siswa_nik']) ?></td>
            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
            <td><?= htmlspecialchars($row['tahun_ajaran']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= number_format($row['nominal']) ?></td>
            <td><?= htmlspecialchars($row['ket']) ?></td>
            <td>
              <a href="edit_du.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="list.php?delete=<?= $row['id'] ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr class="table-success fw-bold">
          <td colspan="5" class="text-end">Total Pemasukan</td>
          <td><?= number_format($total_pemasukan) ?></td>
          <td colspan="2"></td>
        </tr>
      <?php else: ?>
        <tr><td colspan="8" class="text-center">Belum ada data pemasukan</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ===================== PENGELUARAN DU ===================== -->
  <h3 class="text-danger mt-5">📤 Daftar Pengeluaran DU</h3>
  <table class="table table-bordered table-striped table-sm mt-3">
    <thead class="table-danger">
      <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Nominal</th>
        <th>Keterangan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($data_pengeluaran) > 0): ?>
        <?php foreach ($data_pengeluaran as $row): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= number_format($row['nominal']) ?></td>
            <td><?= htmlspecialchars($row['ket']) ?></td>
            <td>
              <a href="edit_du.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="list.php?delete=<?= $row['id'] ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr class="table-danger fw-bold">
          <td colspan="2" class="text-end">Total Pengeluaran</td>
          <td><?= number_format($total_pengeluaran) ?></td>
          <td colspan="2"></td>
        </tr>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">Belum ada data pengeluaran</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ===================== SALDO ===================== -->
  <div class="alert alert-info mt-4">
    <h5>💵 Saldo DU Saat Ini: <strong><?= number_format($saldo) ?></strong></h5>
  </div>

</div>
</body>
</html>
