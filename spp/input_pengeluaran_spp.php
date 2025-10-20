<?php
include "../db.php";

$msg = "";
if(isset($_POST['submit'])){
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    // Validasi baru tanpa NIK atau tahunajaran
    if(empty($tanggal) || empty($jumlah)) {
        $msg = "<div style='color:red'>Tanggal dan Jumlah wajib diisi.</div>";
    } else {
        // Simpan ke tabel spp dengan siswa_nik dan tahun_ajaran NULL
        $stmt = $conn->prepare("INSERT INTO spp (siswa_nik, tahun_ajaran, biaya_spp, date, status, ket) VALUES (NULL, NULL, ?, ?, 'pengeluaran', ?)");
        $stmt->bind_param("dss", $jumlah, $tanggal, $keterangan);
        if($stmt->execute()){
            $msg = "<div style='color:green'>Pengeluaran SPP berhasil disimpan.</div>";
        } else {
            $msg = "<div style='color:red'>Gagal simpan: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengeluaran SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Pengeluaran SPP</h3>
        <?php echo $msg; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp):</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan / Kegiatan:</label>
                <textarea class="form-control" name="keterangan" rows="3"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="#" class="btn btn-secondary">Batal</a>
        </form>


</body>

<script>
    document.getElementById("tanggal").value = new Date().toISOString().split('T')[0];
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>