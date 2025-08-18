<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kwitansi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #e91e63; /* warna pinggir */
      padding: 20px;
    }
    .kwitansi {
      background: #fff;
      border: 2px solid #000;
      padding: 30px;
      width: 700px;
      margin: auto;
      position: relative;
    }
    .kwitansi h2 {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      position: absolute;
      left: 0;
      top: 30px;
      font-weight: bold;
      letter-spacing: 2px;
    }
    .kwitansi .nominal {
      font-size: 22px;
      font-weight: bold;
    }
    .kwitansi .ttd {
      text-align: right;
      margin-top: 40px;
    }
    .strip {
      border-bottom: 1px solid #000;
      display: inline-block;
      min-width: 200px;
    }
  </style>
</head>
<body>
  <div class="kwitansi">
    <h2>KWITANSI</h2>
    <div class="mb-2">
      <strong>No. :</strong> 003/KWT/IV/2020
    </div>
    <div class="mb-2">
      <strong>Telah diterima dari</strong> : CUSTOMER C
    </div>
    <div class="mb-2">
      <strong>Uang sejumlah</strong> : <em>TIGA JUTA RUPIAH</em>
    </div>
    <div class="mb-2">
      <strong>Untuk pembayaran</strong> : PEMBELIAN GENTENG
    </div>

    <div class="nominal mt-4">
      Rp. 3.000.000
    </div>

    <div class="ttd">
      Cirebon, 06 April 2020 <br><br>
      ( ADE KARYADI )
    </div>
  </div>
</body>
</html>
