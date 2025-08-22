<?php
include_once '../db.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM spp WHERE id=?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus');window.location='list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data');window.location='list.php';</script>";
        exit;
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $tanggal = $_POST['edit_tanggal'] ?? '';
    $nominal = $_POST['edit_nominal'] ?? '';
    $tahun_ajaran = $_POST['edit_tahun_ajaran'] ?? '';
    $siswa_nik = $_POST['edit_siswa_nik'] ?? '';
    $ket = $_POST['edit_ket'] ?? '';
    $status = $_POST['edit_status'] ?? '';
    $update_stmt = $conn->prepare("UPDATE spp SET siswa_nik=?, tahun_ajaran=?, biaya_spp=?, date=?, status=?, ket=? WHERE id=?");
    $update_stmt->bind_param("ssisssi", $siswa_nik, $tahun_ajaran, $nominal, $tanggal, $status, $ket, $id);
    if ($update_stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate');window.location='list.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update data');window.location='list.php';</script>";
        exit;
    }
}

// Query SPP gabung data siswa
$q = $conn->query("SELECT spp.*, siswa.nama as nama_siswa
    FROM spp LEFT JOIN siswa ON spp.siswa_nik=siswa.NIK
    ORDER BY spp.date DESC");

// Untuk dropdown edit
$data_siswa = [];
$sq = $conn->query("SELECT NIK, nama FROM siswa");
while($row = $sq->fetch_assoc()) $data_siswa[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Daftar Pembayaran SPP</h3>
    <table class="table table-bordered table-striped table-sm mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Tahun Ajaran</th>
                <th>Tanggal</th>
                <th>Nominal Bayar</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $q->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['siswa_nik']) ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_ajaran']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= number_format($row['biaya_spp']) ?></td>
                    <td>
                        <?php
                        if ($row['status'] == 'lunas') {
                            echo '<span class="badge bg-success">Lunas</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark">Belum Lunas</span>';
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['ket']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="<?= $row['id'] ?>"
                            data-siswa_nik="<?= htmlspecialchars($row['siswa_nik']) ?>"
                            data-nama="<?= htmlspecialchars($row['nama_siswa']) ?>"
                            data-tahun_ajaran="<?= htmlspecialchars($row['tahun_ajaran']) ?>"
                            data-tanggal="<?= htmlspecialchars($row['date']) ?>"
                            data-nominal="<?= $row['biaya_spp'] ?>"
                            data-ket="<?= htmlspecialchars($row['ket']) ?>"
                            data-status="<?= htmlspecialchars($row['status']) ?>"
                        >Edit</button>
                        <a href="list.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data?')" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Data SPP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="mb-2">
            <label for="edit_siswa_nik" class="form-label">NIK Siswa</label>
            <select class="form-select" name="edit_siswa_nik" id="edit_siswa_nik" required>
                <option value="">Pilih Siswa</option>
                <?php foreach($data_siswa as $sis): ?>
                    <option value="<?= $sis['NIK'] ?>"><?= htmlspecialchars($sis['NIK']) ?> - <?= htmlspecialchars($sis['nama']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label for="edit_tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" name="edit_tahun_ajaran" id="edit_tahun_ajaran" required>
          </div>
          <div class="mb-2">
            <label for="edit_tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" name="edit_tanggal" id="edit_tanggal" required>
          </div>
          <div class="mb-2">
            <label for="edit_nominal" class="form-label">Nominal Bayar</label>
            <input type="number" class="form-control" name="edit_nominal" id="edit_nominal" required>
          </div>
          <div class="mb-2">
            <label for="edit_status" class="form-label">Status</label>
            <select class="form-select" name="edit_status" id="edit_status" required>
                <option value="lunas">Lunas</option>
                <option value="du">Belum Lunas</option>
            </select>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('edit_id').value = button.getAttribute('data-id');
      document.getElementById('edit_siswa_nik').value = button.getAttribute('data-siswa_nik');
      document.getElementById('edit_tahun_ajaran').value = button.getAttribute('data-tahun_ajaran');
      document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal');
      document.getElementById('edit_nominal').value = button.getAttribute('data-nominal');
      document.getElementById('edit_ket').value = button.getAttribute('data-ket');
      document.getElementById('edit_status').value = button.getAttribute('data-status');
    });
});
</script>
</body>
</html>