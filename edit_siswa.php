<?php
include_once 'db.php';

if (!isset($_GET['nik']) || !is_string($_GET['nik'])) {
    die('NIK tidak valid.');
}

$nik = $_GET['nik'];

// Check if student exists
$stmt = $conn->prepare("SELECT * FROM siswa WHERE NIK = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Siswa tidak ditemukan.');
}

$student = $result->fetch_assoc();

// Fetch classes for dropdown
$classes = [];
$class_stmt = $conn->query("SELECT id, nama_kelas FROM kelas");
while ($class = $class_stmt->fetch_assoc()) {
    $classes[] = $class;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $nik = $_POST['nik'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $kelas_id = $_POST['kelas_id'] ?? null;
    $status = $_POST['status'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $alamat = $_POST['alamat'] ?? null;
    $no_hp = $_POST['no_hp'] ?? null;
    $nama_ayah = $_POST['nama_ayah'] ?? null;
    $nama_ibu = $_POST['nama_ibu'] ?? null;
    $pekerjaan_ayah = $_POST['pekerjaan_ayah'] ?? null;
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'] ?? null;
    $tahun_masuk = $_POST['tahun_masuk'] ?? null;

    // Basic validation
    if (empty($nama)) {
        $errors[] = "Nama tidak boleh kosong";
    }

    if (empty($kelas_id)) {
        $errors[] = "Kelas tidak boleh kosong";
    }

    if (empty($status)) {
        $errors[] = "Status tidak boleh kosong";
    }

    if (empty($jenis_kelamin)) {
        $errors[] = "Jenis kelamin tidak boleh kosong";
    }

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    } else {
        // Prepare update statement
        $update_stmt = $conn->prepare("UPDATE siswa SET nama = ?, kelas = ?, status = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, nama_ayah = ?, nama_ibu = ?, pekerjaan_ayah = ?, pekerjaan_ibu = ?, tahun_masuk = ? WHERE NIK = ?");
        $update_stmt->bind_param("ssssssssssss", $nama, $kelas_id, $status, $jenis_kelamin, $alamat, $no_hp, $nama_ayah, $nama_ibu, $pekerjaan_ayah, $pekerjaan_ibu, $tahun_masuk, $student['NIK']);

        if ($update_stmt->execute()) {
            header("Location: list_siswa.php");
            exit;
        } else {
            echo "Terjadi kesalahan saat memperbarui data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Edit Data Siswa</h3>
    <form method="post">
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" value="<?= htmlspecialchars($student['NIK']) ?>" >
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($student['nama']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="kelas_id" class="form-label">Kelas</label>
            <select class="form-select" id="kelas_id" name="kelas_id">
                <option value="">Pilih Kelas</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['id']) ?>" <?= $student['kelas'] == $class['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="Aktif" <?= $student['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Non-Aktif" <?= $student['status'] === 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                <option value="L" <?= $student['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $student['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat"><?= htmlspecialchars($student['alamat']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">No. HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($student['no_hp']) ?>">
        </div>
        <div class="mb-3">
            <label for="nama_ayah" class="form-label">Nama Ayah</label>
            <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="<?= htmlspecialchars($student['nama_ayah']) ?>">
        </div>
        <div class="mb-3">
            <label for="nama_ibu" class="form-label">Nama Ibu</label>
            <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="<?= htmlspecialchars($student['nama_ibu']) ?>">
        </div>
        <div class="mb-3">
            <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
            <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" value="<?= htmlspecialchars($student['pekerjaan_ayah']) ?>">
        </div>
        <div class="mb-3">
            <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
            <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" value="<?= htmlspecialchars($student['pekerjaan_ibu']) ?>">
        </div>
        <div class="mb-3">
            <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
            <input type="number" class="form-control" id="tahun_masuk" name="tahun_masuk" value="<?= htmlspecialchars($student['tahun_masuk']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="list_siswa.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>