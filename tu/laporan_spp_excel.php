<?php
include_once '../db.php';

// Ambil bulan & tahun dari GET (default bulan & tahun sekarang)
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('n');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Nama bulan (biar tidak angka saja)
$nama_bulan = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

// ===================== PEMASUKAN =====================
$sql_pemasukan = "SELECT spp.id, spp.siswa_nik, siswa.nama as nama_siswa, spp.tahun_ajaran, spp.biaya_spp, spp.date, spp.ket
        FROM spp 
        LEFT JOIN siswa ON spp.siswa_nik=siswa.NIK
        WHERE spp.date <> '0000-00-00' 
          AND MONTH(spp.date)=? AND YEAR(spp.date)=? 
          AND spp.status='pemasukan'
        ORDER BY spp.date ASC";

$stmt1 = $conn->prepare($sql_pemasukan);
$stmt1->bind_param("ii", $bulan, $tahun);
$stmt1->execute();
$res_pemasukan = $stmt1->get_result();

// ===================== PENGELUARAN =====================
$sql_pengeluaran = "SELECT id, biaya_spp, date, ket
        FROM spp
        WHERE date <> '0000-00-00' 
          AND MONTH(date)=? AND YEAR(date)=? 
          AND status='pengeluaran'
        ORDER BY date ASC";

$stmt2 = $conn->prepare($sql_pengeluaran);
$stmt2->bind_param("ii", $bulan, $tahun);
$stmt2->execute();
$res_pengeluaran = $stmt2->get_result();

// ===================== EXPORT EXCEL =====================
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Laporan_Keuangan_{$bulan}_{$tahun}.xls\"");

echo "<table border='0' width='100%'>
<tr><td colspan='7' align='center'><h3>TAMAN PENDIDIKAN QUR’AN (TPQ) AL-MUQOYYIM</h3></td></tr>
<tr><td colspan='7' align='center'>TEGALGUBUG LOR</td></tr>
<tr><td colspan='7' align='center'><b>Laporan Keuangan Bulan {$nama_bulan[$bulan]} Tahun {$tahun}</b></td></tr>
</table><br>";

// ===================== TABEL PEMASUKAN =====================
echo "<h4>Pemasukan</h4>";
echo "<table border='1' cellpadding='5' cellspacing='0'>
<tr style='background:#f0f0f0; font-weight:bold'>
    <th>No</th>
    <th>NIK</th>
    <th>Nama Siswa</th>
    <th>Tahun Ajaran</th>
    <th>Tanggal</th>
    <th>Nominal</th>
    <th>Keterangan</th>
</tr>";

$no=1;
$total_pemasukan=0;
while ($row=$res_pemasukan->fetch_assoc()){
    $nominal = (int)$row['biaya_spp'];
    echo "<tr>";
    echo "<td>".$no."</td>";
    echo "<td>".htmlspecialchars($row['siswa_nik'])."</td>";
    echo "<td>".htmlspecialchars($row['nama_siswa'])."</td>";
    echo "<td>".htmlspecialchars($row['tahun_ajaran'])."</td>";
    echo "<td>".date('d/m/Y', strtotime($row['date']))."</td>";
    echo "<td>".number_format($nominal)."</td>";
    echo "<td>".htmlspecialchars($row['ket'])."</td>";
    echo "</tr>";
    $total_pemasukan += $nominal;
    $no++;
}
echo "<tr style='font-weight:bold; background:#e0ffe0'>
        <td colspan='5'>Total Pemasukan</td>
        <td colspan='2'>".number_format($total_pemasukan)."</td>
      </tr>";
echo "</table><br>";

// ===================== TABEL PENGELUARAN =====================
echo "<h4>Pengeluaran</h4>";
echo "<table border='1' cellpadding='5' cellspacing='0'>
<tr style='background:#f0f0f0; font-weight:bold'>
    <th>No</th>
    <th>Tanggal</th>
    <th>Keterangan</th>
    <th>Nominal</th>
</tr>";

$no=1;
$total_pengeluaran=0;
while ($row=$res_pengeluaran->fetch_assoc()){
    $nominal = (int)$row['biaya_spp'];
    echo "<tr>";
    echo "<td>".$no."</td>";
    echo "<td>".date('d/m/Y', strtotime($row['date']))."</td>";
    echo "<td>".htmlspecialchars($row['ket'])."</td>";
    echo "<td>".number_format($nominal)."</td>";
    echo "</tr>";
    $total_pengeluaran += $nominal;
    $no++;
}
echo "<tr style='font-weight:bold; background:#ffe0e0'>
        <td colspan='3'>Total Pengeluaran</td>
        <td>".number_format($total_pengeluaran)."</td>
      </tr>";
echo "</table><br>";

// ===================== RINGKASAN =====================
$saldo = $total_pemasukan - $total_pengeluaran;
echo "<h4>Ringkasan</h4>";
echo "<table border='1' cellpadding='5' cellspacing='0'>
<tr><td>Total Pemasukan</td><td>".number_format($total_pemasukan)."</td></tr>
<tr><td>Total Pengeluaran</td><td>".number_format($total_pengeluaran)."</td></tr>
<tr style='font-weight:bold; background:#d0ffd0'><td>SALDO AKHIR</td><td>".number_format($saldo)."</td></tr>
</table>";
?>
