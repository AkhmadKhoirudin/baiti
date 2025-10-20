<?php
include_once '../db.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe = $_POST['tipe'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $nominal = $_POST['nominal'] ?? '';
    $ket = $_POST['ket'] ?? '';
    // Untuk pemasukan
    $siswa_nik = $_POST['siswa_nik'] ?? '';
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? '';
    if (empty($tanggal) || empty($nominal) || empty($tipe)) {
        $msg = "<div class='alert alert-danger'>Tipe, tanggal, dan nominal wajib diisi.</div>";
    } else {
        if ($tipe === "pemasukan") {
            if (empty($siswa_nik) || empty($tahun_ajaran)) {
                $msg = "<div class='alert alert-danger'>NIK dan Tahun Ajaran wajib diisi untuk pemasukan.</div>";
            } else {
                $ket_p = $ket ?: "Daftar baru tahun $tahun_ajaran";
                $insert = $conn->prepare("INSERT INTO du (siswa_nik, tahun_ajaran, tanggal, nominal, ket) VALUES (?, ?, ?, ?, ?)");
                $insert->bind_param("sssis", $siswa_nik, $tahun_ajaran, $tanggal, $nominal, $ket_p);
                if ($insert->execute()) {
                    $msg = "<div class='alert alert-success'>Data pemasukan Daftar baru berhasil disimpan.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Gagal menyimpan data pemasukan Daftar baru.</div>";
                }
                $insert->close();
            }
        } else {
            // Pengeluaran
            $status = 'pengeluaran';
            $insert = $conn->prepare("INSERT INTO du (tanggal, nominal, ket, status) VALUES (?, ?, ?, ?)");
            $insert->bind_param("siss", $tanggal, $nominal, $ket, $status);
            if ($insert->execute()) {
                $msg = "<div class='alert alert-success'>Data pengeluaran Daftar baru berhasil disimpan.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Gagal menyimpan data pengeluaran Daftar baru.</div>";
            }
            $insert->close();
        }
    }
}

// Ambil daftar siswa sebagai opsi pada select
$data_siswa = [];
$q = $conn->query("SELECT NIK, nama FROM siswa");
while($row = $q->fetch_assoc()) $data_siswa[] = $row;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Data Daftar baru (Pemasukan/Pengeluaran)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3> Data Daftar baru (Pemasukan/Pengeluaran)</h3>
    <?php echo $msg; ?>
    <form method="post">
        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe</label>
            <select class="form-select" id="tipe" name="tipe" required>
                <option value="">Pilih Tipe</option>
                <option value="pemasukan">Pemasukan Daftar baru</option>
                <option value="pengeluaran">Pengeluaran Daftar baru</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" class="form-control" name="nominal" id="nominal" required>
        </div>
        <div class="mb-3">
            <label for="ket" class="form-label">Keterangan</label>
            <textarea class="form-control" name="ket" id="ket"></textarea>
        </div>
        <div id="pemasukan_fields" style="display:none;">
            <div class="mb-3">
                <label for="siswa_nik" class="form-label">NIK Siswa</label>
                <select class="form-select" id="siswa_nik" name="siswa_nik">
                    <option value="">Pilih Siswa</option>
                    <?php foreach($data_siswa as $sis): ?>
                        <option value="<?= $sis['NIK'] ?>"><?= htmlspecialchars($sis['NIK']) ?> - <?= htmlspecialchars($sis['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="<?= date('Y') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="reset" class="btn btn-secondary">Batal</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('tipe').addEventListener('change', function() {
    var val = this.value;
    document.getElementById('pemasukan_fields').style.display = (val == 'pemasukan') ? '' : 'none';
});
</script>

<script>
    document.getElementById("tanggal").value = new Date().toISOString().split('T')[0];
</script>
</body>
</html>