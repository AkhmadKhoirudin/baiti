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

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_stmt = $conn->prepare("DELETE FROM kelas WHERE id = ?");
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        header("Location: input_kelas.php");
        exit;
    } else {
        echo "Terjadi kesalahan saat menghapus data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Hapus Kelas</h3>
        <p>Apakah Anda yakin ingin menghapus kelas ini?</p>
        <form method="post">
            <button type="submit" class="btn btn-danger">Hapus</button>
            <a href="input_kelas.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>