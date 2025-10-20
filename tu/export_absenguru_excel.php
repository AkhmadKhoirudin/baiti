<?php
include_once '../db.php';

// Filter tanggal (gunakan bulan tertentu, misalnya Agustus 2024)
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');


$nama_bulan = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

// Ambil daftar guru
$sql_guru = "SELECT id, nama, gaji FROM guru";
$q_guru = $conn->query($sql_guru);
$guru_list = [];
while ($g = $q_guru->fetch_assoc()) {
    $guru_list[$g['id']] = $g;
}

// Ambil absensi guru
$sql_absen = "SELECT guru_id, DAY(tanggal) as tgl FROM absen_guru 
              WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?";
$stmt = $conn->prepare($sql_absen);
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$res = $stmt->get_result();

$absensi = [];
while ($row = $res->fetch_assoc()) {
    $absensi[$row['guru_id']][] = $row['tgl'];
}

// Export Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Report_Absensi_{$bulan}_{$tahun}.xls\"");



echo "<table border='0' width='100%'>
<tr><td colspan='35' align='center'><h3>TAMAN PENDIDIKAN QUR’AN (TPQ) AL-MUQOYYIM</h3></td></tr>
<tr><td colspan='35' align='center'>TEGALGUBUG LOR</td></tr>
<tr><td colspan='35' align='center'><b>Laporan Keuangan Bulan $bulan Tahun $tahun</b></td></tr>
</table><br>";

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama</th>";
for ($i=1; $i<=31; $i++) {
    echo "<th>$i</th>";
}
echo "<th>Total Hadir</th>
      <th>Jumlah Gaji</th>
    </tr>";

$no = 1;
foreach ($guru_list as $gid => $guru) {
    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>".htmlspecialchars($guru['nama'])."</td>";

    $total_hadir = 0;
    for ($i=1; $i<=31; $i++) {
        if (!empty($absensi[$gid]) && in_array($i, $absensi[$gid])) {
            echo "<td>✓</td>";
            $total_hadir++;
        } else {
            echo "<td></td>";
        }
    }

    $total_gaji = $total_hadir * $guru['gaji'];
    echo "<td>$total_hadir</td>";
    echo "<td>Rp ".number_format($total_gaji,0,',','.')."</td>";
    echo "</tr>";

    $no++;
}
echo "</table>";
?>
