<?php
include_once '../db.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Ambil data berdasarkan ID
$query = $conn->query("SELECT spp.*, siswa.nama as nama_siswa 
                       FROM spp LEFT JOIN siswa ON spp.siswa_nik=siswa.NIK 
                       WHERE spp.id = $id");

if ($query->num_rows === 0) {
    header("Location: list.php");
    exit();
}

$data = $query->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_nik = $_POST['siswa_nik'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $biaya_spp = $_POST['biaya_spp'];
    $ket = $_POST['ket'];
    
    $update_query = "UPDATE spp SET 
                    siswa_nik = '$siswa_nik',
                    tahun_ajaran = '$tahun_ajaran',
                    biaya_spp = '$biaya_spp',
                    ket = '$ket'
                    WHERE id = $id";
    
    if ($conn->query($update_query)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='list.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Ambil daftar siswa untuk dropdown
$siswa_query = $conn->query("SELECT NIK, nama FROM siswa ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data SPP</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIK Siswa</label>
                            <select class="form-select" name="siswa_nik" required>
                                <option value="">Pilih Siswa</option>
                                <?php while($siswa = $siswa_query->fetch_assoc()): ?>
                                    <option value="<?= $siswa['NIK'] ?>" 
                                            <?= $siswa['NIK'] == $data['siswa_nik'] ? 'selected' : '' ?>>
                                        <?= $siswa['nama'] ?> (<?= $siswa['NIK'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="tahun_ajaran" 
                                   value="<?= htmlspecialchars($data['tahun_ajaran']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Biaya SPP</label>
                            <input type="number" class="form-control" name="biaya_spp" 
                                   value="<?= htmlspecialchars($data['biaya_spp']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="ket" rows="3" required><?= htmlspecialchars($data['ket']) ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Data</button>
                            <a href="list.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>