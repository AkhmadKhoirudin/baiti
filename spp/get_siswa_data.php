<?php
include_once '../db.php';

if (isset($_GET['nik'])) {
    $nik = $_GET['nik'];

    // Perbaikan query berdasarkan struktur tabel yang terlihat
    $stmt = $conn->prepare("
        SELECT s.NIK, s.nama, s.tahun_masuk, k.nama_kelas 
        FROM siswa s 
        LEFT JOIN kelas k ON s.kelas_id = k.id 
        WHERE s.NIK = ?
    ");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $siswa = $result->fetch_assoc();
        echo json_encode([
            'nama' => $siswa['nama'],
            'nama_kelas' => $siswa['nama_kelas'],
            'tahun_masuk' => $siswa['tahun_masuk']
        ]);
    } else {
        echo json_encode(['error' => 'Siswa tidak ditemukan']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'NIK tidak diberikan']);
}
$conn->close();
?>