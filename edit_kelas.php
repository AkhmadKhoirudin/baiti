<?php
include_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID kelas tidak valid.');
}

$id = $_GET['id'];

// Check if class exists
$stmt = $conn->prepare("SELECT * FROM kelas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Kelas tidak ditemukan.');
}

$class = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $spp = $_POST['spp'] ?? '';
    $wali_kelas = $_POST['wali_kelas'] ?? '';

    // Basic validation
    if (empty($nama_kelas)) {
        $errors[] = "Nama kelas tidak boleh kosong";
    }

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    } else {
        // Prepare update statement
        $update_stmt = $conn->prepare("UPDATE kelas SET nama_kelas = ?, spp = ?, wali_kelas = ? WHERE id = ?");
        $update_stmt->bind_param("sssi", $nama_kelas, $spp, $wali_kelas, $id);

        if ($update_stmt->execute()) {
            header("Location: input_kelas.php");
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
    <title>Edit Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Data Kelas</h3>
        <form method="post">
            <div class="mb-3">
                <label for="nama_kelas" class="form-label">Nama Kelas</label>
                <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" value="<?= htmlspecialchars($class['nama_kelas']) ?>">
            </div>
            <div class="mb-3">
                <label for="spp" class="form-label">SPP</label>
                <input type="text" class="form-control" id="spp" name="spp" value="<?= htmlspecialchars($class['spp']) ?>">
            </div>
            <div class="mb-3">
                <label for="wali_kelas" class="form-label">Wali Kelas</label>
                <input type="text" class="form-control" id="wali_kelas" name="wali_kelas" value="<?= htmlspecialchars($class['wali_kelas']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="input_kelas.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>