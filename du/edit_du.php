<?php
include_once '../db.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];

// Ambil data berdasarkan ID
$query = $conn->query("SELECT du.*, siswa.nama as nama_siswa 
                       FROM du LEFT JOIN siswa ON du.siswa_nik=siswa.NIK 
                       WHERE du.id = $id");

if ($query->num_rows === 0) {
    header("Location: list.php");
    exit();
}

$data = $query->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $nominal = $_POST['nominal'];
    $ket = $_POST['ket'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $siswa_nik = $_POST['siswa_nik'];
    
    // Update data
    if ($siswa_nik && $tahun_ajaran) {
        $update_query = "UPDATE du SET 
                        tanggal = '$tanggal',
                        tahun_ajaran = '$tahun_ajaran',
                        siswa_nik = '$siswa_nik',
                        nominal = '$nominal',
                        ket = '$ket'
                        WHERE id = $id";
    } else {
        $update_query = "UPDATE du SET 
                        tanggal = '$tanggal',
                        nominal = '$nominal',
                        ket = '$ket'
                        WHERE id = $id";
    }
    
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
    <title>Edit Data DU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data DU</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" 
                                   value="<?= htmlspecialchars($data['tanggal']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <input type="number" class="form-control" name="nominal" 
                                   value="<?= htmlspecialchars($data['nominal']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="ket" rows="3" required><?= htmlspecialchars($data['ket']) ?></textarea>
                        </div>
                        
                        <?php if ($data['siswa_nik'] && $data['tahun_ajaran']): ?>
                        <div class="mb-3">
                            <label class="form-label">NIK Siswa</label>
                            <select class="form-select" name="siswa_nik">
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
                                   value="<?= htmlspecialchars($data['tahun_ajaran']) ?>">
                        </div>
                        <?php endif; ?>
                        
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