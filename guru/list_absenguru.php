<?php
include_once '../db.php';

// Handle edit/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $jam = $_POST['edit_jam'] ?? '';
    $tanggal = $_POST['edit_tanggal'] ?? '';
    $keterangan = $_POST['edit_keterangan'] ?? '';

    // Validasi
    $error = '';
    if (!$id || !$jam || !$tanggal) {
        $error = 'Data tidak lengkap!';
    } elseif (!preg_match('/^\d{2}:\d{2}$/', $jam)) {
        $error = 'Format jam salah!';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error = 'Format tanggal salah!';
    }
    if ($error) {
        echo "<script>alert('Gagal update data: $error');window.location='list_absenguru.php';</script>"; exit;
    }

    $up = $conn->prepare("UPDATE absen_guru SET tanggal=?, jam=?, keterangan=? WHERE id=?");
    if (!$up) {
        $err = htmlspecialchars($conn->error, ENT_QUOTES);
        echo "<script>alert('Gagal prepare SQL: $err');window.location='list_absenguru.php';</script>"; exit;
    }
    $up->bind_param("sssi", $tanggal, $jam, $keterangan, $id);
    if ($up->execute()) {
        echo "<script>alert('Data absensi berhasil diupdate');window.location='list_absenguru.php';</script>"; exit;
    } else {
        $err = htmlspecialchars($up->error, ENT_QUOTES);
        echo "<script>alert('Gagal update data: $err');window.location='list_absenguru.php';</script>"; exit;
    }
}

// Query data absensi guru
$q = $conn->query("SELECT ag.*, g.nama as nama_guru, g.mapel, g.no_hp, g.alamat
    FROM absen_guru ag
    LEFT JOIN guru g ON ag.guru_id = g.id
    ORDER BY ag.tanggal DESC, ag.jam DESC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Absensi Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Daftar Absensi Guru</h3>
    <a href="absen_guru.php" class="btn btn-primary mb-3">Input Absensi Guru</a>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guru ID</th>
                    <th>Nama Guru</th>
                    <th>Mapel</th>
                    <th>No HP</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $q->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['guru_id']) ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                    <td><?= htmlspecialchars($row['mapel']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['jam']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="<?= $row['id'] ?>"
                            data-nama_guru="<?= htmlspecialchars($row['nama_guru']) ?>"
                            data-tanggal="<?= htmlspecialchars($row['tanggal']) ?>"
                            data-jam="<?= htmlspecialchars($row['jam']) ?>"
                            data-keterangan="<?= htmlspecialchars($row['keterangan']) ?>"
                        >Edit</button>
                   
                        <form method="post" action="hapus_absenguru.php" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data absensi guru ini?');">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Edit Absen Guru -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Absensi Guru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="mb-2">
                <label for="edit_nama_guru" class="form-label">Nama Guru</label>
                <input type="text" class="form-control" name="edit_nama_guru" id="edit_nama_guru" readonly>
            </div>
            <div class="mb-2">
                <label for="edit_tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" name="edit_tanggal" id="edit_tanggal" required>
            </div>
            <div class="mb-2">
                <label for="edit_jam" class="form-label">Jam</label>
                <input type="time" class="form-control" name="edit_jam" id="edit_jam" required>
            </div>
            <div class="mb-2">
                <label for="edit_keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" name="edit_keterangan" id="edit_keterangan">
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
document.addEventListener('DOMContentLoaded', function(){
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('edit_id').value = button.getAttribute('data-id');
        document.getElementById('edit_nama_guru').value = button.getAttribute('data-nama_guru');
        document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal');
        document.getElementById('edit_jam').value = button.getAttribute('data-jam');
        document.getElementById('edit_keterangan').value = button.getAttribute('data-keterangan');
    });
});
</script>
</body>
</html>