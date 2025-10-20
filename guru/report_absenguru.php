<?php
// Koneksi
include_once '../db.php';

// Prevent warning (hapus sisa variabel filter tanggal)
$tgl_dari = '';
$tgl_sampai = '';

// Filter bulan & tahun
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$hari_count = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// List guru
$q_guru = $conn->query("SELECT id, nama, mapel, gaji FROM guru ORDER BY nama");
$list_guru = [];
while ($g = $q_guru->fetch_assoc()) {
    $list_guru[] = $g;
}

// Ambil semua absensi bulan-tahun terpilih
$stmt = $conn->prepare("SELECT guru_id, tanggal FROM absen_guru WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$res_absen = $stmt->get_result();

$absen = []; // guru_id => [tanggal=>ada]
while ($r = $res_absen->fetch_assoc()) {
    $gid = $r['guru_id'];
    $tgl = ltrim(date('j', strtotime($r['tanggal'])));
    $absen[$gid][$tgl] = true;
}

// Tidak ada lagi query absensi per tanggal, hanya menggunakan $bulan & $tahun di grid utama.

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Laporan Absensi Guru Bulan <?= $bulan ?>/<?= $tahun ?></h3>
    <form method="get" class="row mb-4">
        <div class="col-auto">
            <label for="bulan" class="form-label">Bulan:</label>
            <select name="bulan" id="bulan" class="form-select">
                <?php
                  for ($b=1; $b<=12; $b++) {
                    $selected = ($b==$bulan) ? "selected" : "";
                    echo "<option value='$b' $selected>" . date('F', mktime(0,0,0,$b,1)) . "</option>";
                  }
                ?>
            </select>
        </div>
        <div class="col-auto">
            <label for="tahun" class="form-label">Tahun:</label>
            <input type="number" name="tahun" id="tahun" class="form-control" value="<?= $tahun ?>">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="report_absenguru.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    <div class="mb-3">
       <?php
         $params = [];
         if ($bulan) $params[] = "bulan=" . urlencode($bulan);
         if ($tahun) $params[] = "tahun=" . urlencode($tahun);
         $link_excel = "export_absenguru_excel.php";
         if ($params) {
           $link_excel .= "?" . implode("&", $params);
         }
       ?>
       <a href="<?= $link_excel ?>" class="btn btn-success">Export Gaji Excel</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm" style="font-size:12px">
            <thead>
                <tr style="background:#ffd3a1;">
                    <th>No</th>
                    <th>Nama</th>
                    <?php for ($d=1; $d<=$hari_count; $d++): ?>
                        <th><?= $d ?></th>
                    <?php endfor; ?>
                    <th>Total Hadir</th>
                    <th>Jumlah Gaji</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $grand_hadir = 0; $grand_gaji = 0; $no=1;
            foreach ($list_guru as $g):
                $hadir=0;
                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>".htmlspecialchars($g['nama'])."</td>";
                for ($d=1; $d<=$hari_count; $d++) {
                    if (!empty($absen[$g['id']][$d])) {
                        echo "<td style='text-align:center'>&#10003;</td>";
                        $hadir++;
                    } else {
                        echo "<td></td>";
                    }
                }
                $jml_gaji = $hadir * $g['gaji'];
                echo "<td style='text-align:center'>$hadir</td>";
                echo "<td>Rp " . number_format($jml_gaji,0,',','.') . "</td>";
                echo "</tr>";
                $grand_hadir += $hadir;
                $grand_gaji += $jml_gaji;
                $no++;
            endforeach;
            ?>
            <tr style="background:#eee;">
                <td colspan="<?= 2+$hari_count ?>" style="text-align:right"><b>Total</b></td>
                <td style='text-align:center'><b><?= $grand_hadir ?></b></td>
                <td><b>Rp <?= number_format($grand_gaji,0,',','.') ?></b></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>