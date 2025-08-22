<?php
include_once '../db.php';

// Handle delete
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

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $nominal = $_POST['edit_nominal'] ?? '';
    $ket = $_POST['edit_ket'] ?? '';
    $tanggal = $_POST['edit_tanggal'] ?? '';
    $tahun_ajaran = $_POST['edit_tahun_ajaran'] ?? null;
    $siswa_nik = $_POST['edit_siswa_nik'] ?? null;
    if ($siswa_nik && $tahun_ajaran) {
        $update_stmt = $conn->prepare("UPDATE du SET tanggal=?, tahun_ajaran=?, siswa_nik=?, nominal=?, ket=? WHERE id=?");
        $update_stmt->bind_param("sssssi", $tanggal, $tahun_ajaran, $siswa_nik, $nominal, $ket, $id);
    } else {
        $update_stmt = $conn->prepare("UPDATE du SET tanggal=?, nominal=?, ket=? WHERE id=?");
        $update_stmt->bind_param("sisi", $tanggal, $nominal, $ket, $id);
    }
    if ($update_stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate');window.location='list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update data');window.location='list.php';</script>";
        exit;
    }
}

// Handle export Excel (CSV)
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=laporan_du.csv');
    $fp = fopen('php://output', 'w');
    // Header untuk pemasukan
    fputcsv($fp, ['Pemasukan DU']);
    fputcsv($fp, ['Tanggal', 'NIK', 'Tahun Ajaran', 'Nominal', 'Keterangan']);
    $q1 = $conn->query("SELECT * FROM du WHERE siswa_nik IS NOT NULL AND siswa_nik<>'' AND tahun_ajaran IS NOT NULL AND tahun_ajaran<>'' ORDER BY tanggal DESC");
    while ($row = $q1->fetch_assoc()) {
        fputcsv($fp, [$row['tanggal'], $row['siswa_nik'], $row['tahun_ajaran'], $row['nominal'], $row['ket']]);
    }
    fputcsv($fp, []);
    // Header untuk pengeluaran
    fputcsv($fp, ['Pengeluaran DU']);
    fputcsv($fp, ['Tanggal', 'Nominal', 'Keterangan']);
    $q2 = $conn->query("SELECT * FROM du WHERE status='pengeluaran' ORDER BY tanggal DESC");
    while ($row = $q2->fetch_assoc()) {
        fputcsv($fp, [$row['tanggal'], $row['nominal'], $row['ket']]);
    }
    fclose($fp);
    exit;
}

// Query Pemasukan DU
$q1 = $conn->query("SELECT * FROM du WHERE siswa_nik IS NOT NULL AND siswa_nik<>'' AND tahun_ajaran IS NOT NULL AND tahun_ajaran<>'' ORDER BY tanggal DESC");
// Query Pengeluaran DU
$q2 = $conn->query("SELECT * FROM du WHERE status='pengeluaran' ORDER BY tanggal DESC");

// Total pemasukan
$total_pemasukan = 0;
$pemasukan = [];
while ($row = $q1->fetch_assoc()) {
    $pemasukan[] = $row;
    $total_pemasukan += (int)$row['nominal'];
}

// Total pengeluaran
$total_pengeluaran = 0;
$pengeluaran = [];
while ($row = $q2->fetch_assoc()) {
    $pengeluaran[] = $row;
    $total_pengeluaran += (int)$row['nominal'];
}

// Hitung Saldo
$saldo = $total_pemasukan - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pemasukan & Pengeluaran DU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Daftar Pemasukan & Pengeluaran DU</h3>
    <div class="mb-3">
        <a href="list.php?export=excel" class="btn btn-success btn-sm">Export Excel</a>
        <a href="list.php?export=pdf" class="btn btn-danger btn-sm">Export PDF</a>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h5 class="mt-4">Pemasukan DU</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Tahun Ajaran</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pemasukan as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['siswa_nik']) ?></td>
                            <td><?= htmlspecialchars($row['tahun_ajaran']) ?></td>
                            <td><?= number_format($row['nominal']) ?></td>
                            <td><?= htmlspecialchars($row['ket']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>"
                                    data-nominal="<?= $row['nominal'] ?>"
                                    data-ket="<?= htmlspecialchars($row['ket']) ?>"
                                    data-tahun_ajaran="<?= htmlspecialchars($row['tahun_ajaran']) ?>"
                                    data-siswa_nik="<?= htmlspecialchars($row['siswa_nik']) ?>"
                                    data-type="pemasukan"
                                >Edit</button>
                                <a href="list.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin Hapus?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(count($pemasukan)==0): ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data pemasukan.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Pemasukan</th>
                        <th colspan="3"><?= number_format($total_pemasukan) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-md-6">
            <h5 class="mt-4">Pengeluaran DU</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pengeluaran as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= number_format($row['nominal']) ?></td>
                            <td><?= htmlspecialchars($row['ket']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>"
                                    data-nominal="<?= $row['nominal'] ?>"
                                    data-ket="<?= htmlspecialchars($row['ket']) ?>"
                                    data-type="pengeluaran"
                                >Edit</button>
                                <a href="list.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin Hapus?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(count($pengeluaran)==0): ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data pengeluaran.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total Pengeluaran</th>
                        <th colspan="3"><?= number_format($total_pengeluaran) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="alert alert-info mt-4"><strong>Saldo DU saat ini: <?= number_format($saldo) ?></strong></div>
</div>
<?php
// Handle export PDF (skeleton, butuh library mPDF/FPDF)
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    echo "<div class='alert alert-warning mt-3'>Export PDF membutuhkan library <b>mPDF</b> atau <b>FPDF</b>. Silakan install salah satu library tersebut dan tambahkan kode export di sini.<br>
    Contoh penggunaan mPDF:<br>
    <pre>
    require_once __DIR__.'/vendor/autoload.php';
    \$mpdf = new \\Mpdf\\Mpdf();
    \$html = '...'; // Generate HTML sama dengan tabel di halaman
    \$mpdf->WriteHTML(\$html);
    \$mpdf->Output('laporan_du.pdf','D');
    </pre>
    </div>";
    // exit;
}
?>
<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Data DU</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          <input type="hidden" name="edit_type" id="edit_type">
          <div class="mb-2">
            <label for="edit_tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" name="edit_tanggal" id="edit_tanggal" required>
          </div>
          <div id="edit_fields_pemasukan" style="display:none;">
            <div class="mb-2">
              <label for="edit_siswa_nik" class="form-label">NIK</label>
              <input type="text" class="form-control" name="edit_siswa_nik" id="edit_siswa_nik">
            </div>
            <div class="mb-2">
              <label for="edit_tahun_ajaran" class="form-label">Tahun Ajaran</label>
              <input type="text" class="form-control" name="edit_tahun_ajaran" id="edit_tahun_ajaran">
            </div>
          </div>
          <div class="mb-2">
            <label for="edit_nominal" class="form-label">Nominal</label>
            <input type="number" class="form-control" name="edit_nominal" id="edit_nominal" required>
          </div>
          <div class="mb-2">
            <label for="edit_ket" class="form-label">Keterangan</label>
            <input type="text" class="form-control" name="edit_ket" id="edit_ket">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var id = button.getAttribute('data-id');
      var tanggal = button.getAttribute('data-tanggal');
      var nominal = button.getAttribute('data-nominal');
      var ket = button.getAttribute('data-ket');
      var type = button.getAttribute('data-type');
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_tanggal').value = tanggal;
      document.getElementById('edit_nominal').value = nominal;
      document.getElementById('edit_ket').value = ket;
      document.getElementById('edit_type').value = type;
      // Field tambahan pemasukan
      if(type === 'pemasukan'){
        document.getElementById('edit_fields_pemasukan').style.display = '';
        var tahun_ajaran = button.getAttribute('data-tahun_ajaran');
        var siswa_nik = button.getAttribute('data-siswa_nik');
        document.getElementById('edit_tahun_ajaran').value = tahun_ajaran;
        document.getElementById('edit_siswa_nik').value = siswa_nik;
        document.getElementById('edit_tahun_ajaran').required = true;
        document.getElementById('edit_siswa_nik').required = true;
      }else{
        document.getElementById('edit_fields_pemasukan').style.display = 'none';
        document.getElementById('edit_tahun_ajaran').required = false;
        document.getElementById('edit_siswa_nik').required = false;
      }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>