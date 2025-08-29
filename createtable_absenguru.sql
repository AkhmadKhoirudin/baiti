CREATE TABLE absen_guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id VARCHAR(50),
    nama_guru VARCHAR(100),
    tanggal DATE,
    jam_masuk TIME,
    jam_keluar TIME,
    keterangan VARCHAR(255)
);