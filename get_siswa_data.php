<?php
include_once 'db.php';

if (isset($_GET['nik'])) {
    $nik = $_GET['nik'];
    $stmt = $conn->prepare("SELECT s.NIK, s.nama, k.nama_kelas, s.tahun_masuk
                             FROM siswa s
                             LEFT JOIN kelas k ON s.kelas = k.id
                             WHERE s.NIK = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data siswa tidak ditemukan.']);
    }
} else {
    echo json_encode(['error' => 'NIK siswa tidak diberikan.']);
}
?>