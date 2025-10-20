<?php
include_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $siswa_nik = $_POST['siswa_nik'] ?? '';
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? '';
    $biaya_spp = $_POST['biaya_spp'] ?? '';
    $date = $_POST['DATE'] ?? '';

    if (empty($siswa_nik)) {
        $errors[] = "NIK Siswa tidak boleh kosong";
    }

    if (empty($tahun_ajaran)) {
        $errors[] = "Tahun Ajaran tidak boleh kosong";
    }

    if ($biaya_spp === '' || !is_numeric($biaya_spp)) {
        $errors[] = "Biaya SPP tidak boleh kosong dan harus berupa angka.";
    }

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    } else {
        // pastikan biaya_spp integer
        $biaya_spp = (int)$biaya_spp;

        // Ambil kelas siswa
        $kelas_stmt = $conn->prepare("SELECT k.id, k.spp FROM siswa s LEFT JOIN kelas k ON s.kelas=k.id WHERE s.NIK=?");
        $kelas_stmt->bind_param("s", $siswa_nik);
        $kelas_stmt->execute();
        $kelas_res = $kelas_stmt->get_result();
        $kelas_data = $kelas_res->fetch_assoc();
        $kelas_stmt->close();

        $kelas_id = $kelas_data['id'] ?? null;
        $spp_kelas = $kelas_data['spp'] ?? 0;

        // Extract bulan dan tahun dari tanggal
        $dt = explode("-", $date);
        $bulan = $dt[1] ?? date('m');
        $tahun = $dt[0] ?? date('Y');

        // Cari total pembayaran siswa pada bulan & tahun ini
        $query = "SELECT SUM(biaya_spp) AS total_bayar FROM spp
                  WHERE siswa_nik=? AND YEAR(DATE)=? AND MONTH(DATE)=?";
        $stmtCek = $conn->prepare($query);
        $stmtCek->bind_param("sss", $siswa_nik, $tahun, $bulan);
        $stmtCek->execute();
        $cekRes = $stmtCek->get_result();
        $bayarBulanIni = $cekRes->fetch_assoc()['total_bayar'] ?? 0;
        $stmtCek->close();

        if($bayarBulanIni >= $spp_kelas) {
            echo "<div class='alert alert-danger'>SPP bulan ini sudah lunas dan tidak dapat diinput lagi sampai bulan depan.</div>";
        } else {
            $sisa_cicilan = $spp_kelas - $bayarBulanIni;
            // Jika pembayaran lebih dari sisa cicilan, alokasikan ke bulan berikutnya/mengecek bulan sebelumnya jika belum lunas
            $bayar = $biaya_spp;
            $bulan_input = $bulan;
            $tahun_input = $tahun;

            while($bayar > 0) {
                // Cek pembayaran bulan ini
                $query_bulan = "SELECT SUM(biaya_spp) AS total_bayar FROM spp
                                WHERE siswa_nik=? AND YEAR(DATE)=? AND MONTH(DATE)=?";
                $stmtBulan = $conn->prepare($query_bulan);
                $stmtBulan->bind_param("sss", $siswa_nik, $tahun_input, $bulan_input);
                $stmtBulan->execute();
                $res_bulan = $stmtBulan->get_result();
                $bayar_bulan = $res_bulan->fetch_assoc()['total_bayar'] ?? 0;
                $stmtBulan->close();

                $sisa_bulan = $spp_kelas - $bayar_bulan;

                if($sisa_bulan <= 0) {
                    // sudah lunas bulan ini, lanjut ke bulan berikutnya
                    $bulan_input++; if($bulan_input > 12) { $bulan_input = 1; $tahun_input++; }
                    continue;
                }

                // pembayaran sesuai sisa atau lebih
                $bayar_bulan_ini = min($bayar, $sisa_bulan);

                // insert ke bulan yang sesuai (tanggal: gunakan tanggal input, tapi ubah ke bulan target)
                $tanggal_baru = sprintf('%04d-%02d-%02d', $tahun_input, $bulan_input, $dt[2] ?? '01');
                $insert_stmt = $conn->prepare("INSERT INTO spp (siswa_nik, tahun_ajaran, biaya_spp, DATE) VALUES (?, ?, ?, ?)");
                $insert_stmt->bind_param("ssis", $siswa_nik, $tahun_ajaran, $bayar_bulan_ini, $tanggal_baru);
                if($insert_stmt->execute()) {
                    // Berhasil input ke bulan ini
                }
                $insert_stmt->close();

                $bayar -= $bayar_bulan_ini;
                $bulan_input++; if($bulan_input > 12) { $bulan_input = 1; $tahun_input++; }
            }
            
            echo "<div class='alert alert-success'>Pembayaran SPP berhasil diinput sesuai logika cicilan/kelebihan.</div>
            <script>
                setTimeout(function(){
                    document.querySelector('form').reset();
                    $('#siswa_nik').val('').trigger('change');
                    $('#biaya_spp_display').val('');
                }, 100);
            </script>";
        }
    }
} else {
    $siswa_nik = '';
    $tahun_ajaran = '';
    $biaya_spp = '';
    $date = '';
}

// Fetch students for dropdown
$students = [];
$student_stmt = $conn->query("SELECT s.NIK, s.nama, s.tahun_masuk, k.nama_kelas 
                              FROM siswa s 
                              LEFT JOIN kelas k ON s.kelas = k.id");

while ($student = $student_stmt->fetch_assoc()) {
    $students[] = $student;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Biaya SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h3>Input Biaya SPP</h3>
    <form method="post">
        <div class="mb-3">
            <label for="siswa_nik" class="form-label">Siswa</label>
            <select class="form-select" id="siswa_nik" name="siswa_nik">
    <option value="">Pilih Siswa</option>
    <?php foreach ($students as $student): ?>
        <option value="<?= htmlspecialchars($student['NIK']) ?>" 
            data-kelas="<?= htmlspecialchars($student['nama_kelas']) ?>" 
            data-tahun="<?= htmlspecialchars($student['tahun_masuk']) ?>"
            <?= ($siswa_nik == $student['NIK']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($student['nama']) ?>
        </option>
    <?php endforeach; ?>
</select>

        </div>
        <div class="mb-3">
            <label for="nama_kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" readonly>
        </div>
        <div class="mb-3">
            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" readonly>
        </div>
        <div class="mb-3">
            <label for="DATE">Tanggal</label>
            <input type="date" class="form-control" id="DATE" name="DATE" required>
        </div>
        <div class="mb-3">
            <label for="biaya_spp_display" class="form-label">Biaya SPP</label>
            <input type="text" class="form-control" id="biaya_spp_display" placeholder="Rp. 0">
            <!-- hidden untuk angka murni -->
            <input type="hidden" id="biaya_spp" name="biaya_spp">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="#" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.getElementById("DATE").value = new Date().toISOString().split('T')[0];
</script>
<script>
    $(document).ready(function() {
        $('#siswa_nik').select2();

        $('#siswa_nik').on('change', function() {
            let option = $(this).find(':selected');
            if (option.val()) {
                $('#nama_kelas').val(option.data('kelas'));
                $('#tahun_ajaran').val(option.data('tahun'));
            } else {
                $('#nama_kelas').val('');
                $('#tahun_ajaran').val('');
                $('#biaya_spp_display').val('');
                $('#biaya_spp').val('');
            }
        });

        // format input biaya
        $('#biaya_spp_display').on('input', function() {
            let angka = $(this).val().replace(/[^0-9]/g, '');
            $('#biaya_spp').val(angka);
            $(this).val(formatRupiah(angka));
        });

        function formatRupiah(angka) {
            if (!angka) return '';
            var number_string = angka.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return 'Rp. ' + rupiah;
        }
    });
</script>
</body>
</html>
