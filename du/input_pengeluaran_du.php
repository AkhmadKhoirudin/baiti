<?php
include_once '../db.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $nominal = $_POST['nominal'] ?? '';
    $ket = $_POST['ket'] ?? '';
    $status=$_POST['status'] ?? 'pengeluaran';

    if (empty($tanggal) || empty($nominal)) {
        $msg = "<div class='alert alert-danger'>Tanggal dan nominal wajib diisi.</div>";
    } else {
        $insert = $conn->prepare("INSERT INTO du (tanggal, nominal, ket, status) VALUES (?, ?, ?, ?)");
        $insert->bind_param("siss", $tanggal, $nominal, $ket, $status);
        $status = 'pengeluaran';
        if ($insert->execute()) {
            $msg = "<div class='alert alert-success'>Pengeluaran DU berhasil disimpan.</div>
            <script>
                setTimeout(function(){
                    document.querySelector('form').reset();
                }, 100);
            </script>";
        } else {
            $msg = "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
        }
        $insert->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengeluaran DU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Input Pengeluaran DU</h3>
    <?php echo $msg; ?>
    <form method="post">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" class="form-control" id="nominal" name="nominal" required>
        </div>
        <div class="mb-3">
            <label for="ket" class="form-label">Keterangan</label>
            <textarea class="form-control" id="ket" name="ket" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="#" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>