<?php
include_once '../db.php';

$msg = "";

// Ambil data guru untuk dropdown
$guru_opt = [];
$q = $conn->query("SELECT id, nama FROM guru ORDER BY nama");
while ($row = $q->fetch_assoc()) $guru_opt[] = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input server side
    $guru_id = isset($_POST['guru_id']) ? intval($_POST['guru_id']) : 0;
    $tanggal = $_POST['tanggal'] ?? '';
    $jam = $_POST['jam'] ?? '';
    $keterangan = $_POST['keterangan'] ?? 'hadir';

    // Ambil nama guru
    $nama_guru = '';
    if ($guru_id > 0) {
        $stmt = $conn->prepare("SELECT nama FROM guru WHERE id=?");
        $stmt->bind_param("i", $guru_id);
        $stmt->execute();
        $r = $stmt->get_result();
        if ($row = $r->fetch_assoc()) $nama_guru = $row['nama'];
        $stmt->close();
    }
    if ($guru_id > 0 && $nama_guru && $tanggal && $jam && $keterangan) {
        // Cek duplikasi absen (jika ada absen untuk guru di tanggal dan jam yang sama)
        $cek = $conn->prepare("SELECT COUNT(*) as cnt FROM absen_guru WHERE guru_id=? AND tanggal=? AND jam=?");
        $cek->bind_param("iss", $guru_id, $tanggal, $jam);
        $cek->execute();
        $res = $cek->get_result();
        $cnt = 0;
        if ($row = $res->fetch_assoc()) $cnt = intval($row['cnt']);
        $cek->close();

        if ($cnt > 0) {
            $msg = "<div class='alert alert-warning'>Absen untuk guru ini di tanggal dan jam yang sama sudah tercatat.</div>";
        } else {
            $ins = $conn->prepare("INSERT INTO absen_guru (guru_id, nama_guru, tanggal, jam, keterangan) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("issss", $guru_id, $nama_guru, $tanggal, $jam, $keterangan);
            if ($ins->execute()) {
                header("Location: list_absenguru.php?status=sukses");
                exit;
            } else {
                $msg = "<div class='alert alert-danger'>Gagal menyimpan absensi guru.</div>";
            }
            $ins->close();
        }
    } else {
        $msg = "<div class='alert alert-danger'>Semua field wajib diisi dan valid.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Input Absensi Guru</h3>
    <?php echo $msg; ?>
    <form method="post">
        <div class="mb-3">
            <label for="guru_id" class="form-label">Guru</label>
            <select class="form-select" name="guru_id" id="guru_id" required>
                <option value="">Pilih Guru</option>
                <?php foreach($guru_opt as $g): ?>
                    <option value="<?= htmlspecialchars($g['id']) ?>"><?= htmlspecialchars($g['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
        </div>

        <div class="mb-3">
            <label for="jam" class="form-label">Jam</label>
            <input type="time" class="form-control" name="jam" id="jam" required>
        </div>
        <!-- <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <select name="keterangan" id="keterangan" class="form-select" required>
                <option value="">Pilih Keterangan</option>
                <option value="Hadir">Hadir</option>
                <option value="alpa">alpa</option>
            </select>
        </div> -->
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="reset" class="btn btn-secondary">Batal</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setDateTimeNow() {
        const now = new Date();

        // format tanggal (YYYY-MM-DD)
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0'); // bulan mulai dari 0
        const day = String(now.getDate()).padStart(2, '0');
        document.getElementById('tanggal').value = `${year}-${month}-${day}`;

        // format jam (HH:MM)
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('jam').value = `${hours}:${minutes}`;
    }

    // jalankan saat halaman dimuat
    window.onload = setDateTimeNow;
</script>
</body>
</html>