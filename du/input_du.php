<?php
include_once '../db.php';

$msg = "";
/* Endpoint AJAX cek DU lunas */
if (isset($_GET['action']) && $_GET['action'] == 'cek_du_lunas') {
    $siswa_nik = $_GET['siswa_nik'] ?? '';
    $tahun_ajaran = $_GET['tahun_ajaran'] ?? '';
    $status_lunas = 0;
    if ($siswa_nik && $tahun_ajaran) {
        $cek_stmt = $conn->prepare("SELECT COUNT(*) AS jml FROM du WHERE siswa_nik=? AND tahun_ajaran=?");
        $cek_stmt->bind_param("ss", $siswa_nik, $tahun_ajaran);
        $cek_stmt->execute();
        $res = $cek_stmt->get_result();
        $status_lunas = $res->fetch_assoc()['jml'] ?? 0;
        $cek_stmt->close();
    }
    header('Content-Type: application/json');
    echo json_encode(['lunas' => $status_lunas > 0]);
    exit;
}
// Handle input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_nik = $_POST['siswa_nik'] ?? '';
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $nominal = $_POST['nominal'] ?? '';

    if (empty($siswa_nik) || empty($tahun_ajaran) || empty($tanggal)) {
        $msg = "<div class='alert alert-danger'>Siswa, Tahun Ajaran, dan Tanggal wajib diisi.</div>";
    } else {
        // Validasi: apakah sudah bayar DU di tahun yg sama?
        $cek_stmt = $conn->prepare("SELECT COUNT(*) AS jml FROM du WHERE siswa_nik=? AND tahun_ajaran=?");
        $cek_stmt->bind_param("ss", $siswa_nik, $tahun_ajaran);
        $cek_stmt->execute();
        $res = $cek_stmt->get_result();
        $sudah_bayar = $res->fetch_assoc()['jml'] ?? 0;
        $cek_stmt->close();

        if ($sudah_bayar > 0) {
            $msg = "<div class='alert alert-warning'>Siswa sudah membayar DU untuk tahun ajaran tersebut.</div>";
        } else {
            // Simpan DU ke tabel du
            $ket = "DU tahun $tahun_ajaran";
            $insert = $conn->prepare("INSERT INTO du (siswa_nik, tahun_ajaran, tanggal, nominal, ket) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssis", $siswa_nik, $tahun_ajaran, $tanggal, $nominal, $ket);
            if ($insert->execute()) {
                $msg = "<div class='alert alert-success'>Pendaftaran ulang/DU berhasil disimpan.</div>
                <script>
                    setTimeout(function(){
                        document.querySelector('form').reset();
                        $('#siswa_nik').val('').trigger('change');
                        $('#nominal').val('');
                    }, 100);
                </script>";
            } else {
                $msg = "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
            }
            $insert->close();
        }
    }
}

// Ambil daftar siswa
$data_siswa = [];
$q = $conn->query("SELECT s.NIK, s.nama, k.nama_kelas, k.id, k.du FROM siswa s LEFT JOIN kelas k ON s.kelas=k.id");
while($row = $q->fetch_assoc()) $data_siswa[] = $row;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input DU (Pendaftaran Ulang)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h3>Input Pendaftaran Ulang (DU)</h3>
    <?php echo $msg; ?>
    <form method="post">
        <div class="mb-3">
            <label for="siswa_nik" class="form-label">Siswa</label>
            <select class="form-select" id="siswa_nik" name="siswa_nik" required>
                <option value="">Pilih Siswa</option>
                <?php foreach ($data_siswa as $siswa): ?>
                    <option value="<?= $siswa['NIK'] ?>"
                        data-nama_kelas="<?= $siswa['nama_kelas'] ?>"
                        data-du="<?= $siswa['du'] ?>">
                        <?= htmlspecialchars($siswa['nama']) ?> - <?= htmlspecialchars($siswa['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="<?php echo date('Y'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Bayar</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal DU</label>
            <input type="number" class="form-control" id="nominal" name="nominal"  required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="reset" class="btn btn-secondary" id="btn-reset">Batal</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // RESET FORM saat tombol Batal diklik
    $('#btn-reset').on('click', function() {
        $('form')[0].reset();
        $('#siswa_nik').val('').trigger('change');
        $('#nominal').val('');
        $('#tanggal').val('');
        $('#tahun_ajaran').val('<?php echo date('Y'); ?>');
        $('#alert_du_lunas').remove();
        enableForm();
    });
    $('#siswa_nik').select2();

    function cek_du_lunas() {
        var siswa_nik = $('#siswa_nik').val();
        var tahun_ajaran = $('#tahun_ajaran').val();
        if (!siswa_nik || !tahun_ajaran) {
            enableForm();
            $('#alert_du_lunas').remove();
            return;
        }
        $.get('input_du.php', {
            action: 'cek_du_lunas',
            siswa_nik: siswa_nik,
            tahun_ajaran: tahun_ajaran
        }, function(res) {
            if (res.lunas) {
                disableForm();
                if ($('#alert_du_lunas').length == 0) {
                    $('form').prepend(
                        "<div id='alert_du_lunas' class='alert alert-warning mb-2'>Siswa sudah lunas DU untuk tahun ajaran ini. Input tidak diperbolehkan.</div>"
                    );
                }
            } else {
                enableForm();
                $('#alert_du_lunas').remove();
            }
        });
    }
    function disableForm() {
        $('#tanggal').prop('disabled', true);
        $('#nominal').prop('disabled', true);
        $('#siswa_nik').prop('disabled', true);
        $('#tahun_ajaran').prop('disabled', true);
        $('button[type=submit]').prop('disabled', true);
    }
    function enableForm() {
        $('#tanggal').prop('disabled', false);
        $('#nominal').prop('disabled', true); // tetap readonly
        $('#siswa_nik').prop('disabled', false);
        $('#tahun_ajaran').prop('disabled', false);
        $('button[type=submit]').prop('disabled', false);
    }

    $('#siswa_nik, #tahun_ajaran').on('change', function() {
        var du = $('option:selected', '#siswa_nik').data('du');
        var nominal = du ? du : 0;
        $('#nominal').val(nominal);
        cek_du_lunas();
    });
    $('#tahun_ajaran').on('keyup change', function() {
        cek_du_lunas();
    });
});
</script>
</body>
</html>