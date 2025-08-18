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
<html>
<head>
    <title>Input Pengeluaran SPP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px;}
        .form-group { margin-bottom: 15px;}
        label { display: block; margin-bottom: 6px;}
        input[type="text"], input[type="number"], input[type="date"], textarea { width: 100%; padding: 5px; }
        input[type="submit"] { padding: 8px 20px;}
    </style>
</head>
<body>
    <h2>Input Pengeluaran SPP (Kegiatan/Jenguk/Lainnya)</h2>
    <?php echo $msg; ?>
    <form method="POST">
        <div class="form-group">
            <label>Tanggal:</label>
            <input type="date" name="tanggal" required>
        </div>
        <div class="form-group">
            <label>Jumlah (Rp):</label>
            <input type="number" name="jumlah" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Keterangan / Kegiatan:</label>
            <textarea name="keterangan" rows="3"></textarea>
        </div>
        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>