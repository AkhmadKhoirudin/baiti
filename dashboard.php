<?php
include_once 'db.php';
session_start();
require_once './login/google-api-client.php';
require_once './login/config.php';
require_once './login/auth.php';

// Periksa apakah pengguna sudah login
if (!isLoggedIn()) {
    // Redirect ke login jika belum login
    header('Location: login/index.php');
    exit;
}

// Ambil data role dari session
$role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'user';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard TPQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --mobile-breakpoint: 768px;
        }
        
      html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }
        
        /* Sidebar styling */
        .sidebar {
            background-color: #fff;
            height: 100vh;
            padding: 32px 24px 32px 24px; /* lebih lebar dan napas */
            border-right: 1px solid #ddd;
            overflow-y: auto;
            position: fixed;
            width: var(--sidebar-width);
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        /* Spasi antar item menu sidebar */
        .sidebar .accordion-item {
            margin-bottom: 18px; /* lebih rapih antar section accordion */
        }
        .accordion-body{

            margin: 2px 0 0 0;
        }

        /* Spasi antar link di submenu */
        .submenu .nav-link {
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            /* gap: 10px; */
            margin-bottom: 10px;
        }

        /* Tombol dashboard dan lain di sidebar */
        .sidebar .btn,
        .sidebar .accordion-button {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .btn {
            padding: 10px 12px 10px 16px; /* agar icon-teks menengah */
            font-size: 1rem;
        }

        /* Accordion header lebih branded */
        .accordion-header {
            margin-bottom: 0;
        }

        /* Tambahan pemisah section via border halus */
        .sidebar .accordion-item:not(:last-child) {
            border-bottom: 1px solid #f1f1f1;
        }

        /* Icon styling lebih stabil */
        .bi {
            font-size: 1.2em;
            vertical-align: middle;
        }

        /* Responsive submenu gap */
        @media (max-width: 991px) {
            .sidebar .accordion-item {
                margin-bottom: 2px;
            }
            .submenu .nav-link {
                margin-bottom: 8px;
                font-size: 0.98rem;
            }
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            display: none;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 10px;
        }
        
        /* Main content area */
        .main-content {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* isi minimal penuh layar */
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }
        
        /* Accordion styling */
        .accordion-button {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .accordion-button::after {
            transition: transform 0.3s ease;
        }
        
        
        .accordion-button:not(.collapsed)::after {
            transform: rotate(180deg);
        }
        
        /* .submenu a {
            font-size: 0.9rem;
            padding-left: 2rem;
        } */
        
        /* Profile section */
        .profile-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 40px;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-img {
            width: 100px;
            height: 110px;
            /* border-radius: 50%; */
            object-fit: cover;
            /* margin: 0 auto 10px; */
        }
        a.nav-link{
            color: #333;
            text-decoration: none;
            /* padding: 10px 15px; */
            border-bottom: 1px solid #eee;
            display: block;
        }

        /* Dashboard cards */
        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #4361ee;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Iframe styling */
        .content-iframe {
             flex: 1; /* dorong footer ke bawah */
    width: 100%;
    border: none;
    border-radius: 8px;
    background: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
        
        @media (max-width: 575px) {
            .stat-number {
                font-size: 1.5rem;
            }
            
            .profile-img {
                width: 60px;
                height: 60px;
            }
            
            .dashboard-card {
                padding: 15px;
            }
        }
        
        /* Footer styling */
        .app-footer {
            background: white;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #eee;
            margin-bottom: 0;
            font-size: 0.85rem;
        }
        .accordion-header{
            padding: 0 !important;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center; /* teks & ikon jadi di tengah */
            gap: 8px; /* jarak antara ikon dan teks */
            width: 100%;
            padding: 10px 14px;
            margin-top: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            }

        .logout-btn:hover {
            background-color: #e63946;
            color: #fff;
            border-color: #e63946;
        }

    </style>
</head>
<body>
<!-- Mobile Menu Toggle Button -->
<button class="mobile-menu-toggle">
    <i class="bi bi-list"></i> Menu
</button>

<!-- Sidebar -->
<div class="sidebar">
    <div class="profile-section">
        <img src="width_738.jpg" alt="Profil" class="profile-img">
        <p class="fw-bold mt-2">AL-MUQOYYIM</p>
    </div>

    <!-- Dashboard -->
    <!-- <div class="mb-2">
        <a class="btn w-100 text-start btn-primary" href="login/home.php" target="main_frame">
            <i class="bi bi-house-door me-2"></i> Dashboard
        </a>
    </div> -->

    <!-- Accordion Menu -->
    <div class="accordion" id="menuAccordion">
        <!-- Data Siswa -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDataSiswa">
                    <i class="bi bi-people-fill"></i> Data Siswa
                </button>
            </h2>
            <div id="collapseDataSiswa" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <?php
                    $sql = "SELECT * FROM kelas";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<a class="nav-link d-flex align-items-center mb-2" href="list_siswa.php?kelas=' . urlencode($row['id']) . '" target="main_frame">
                                <i class="bi bi-arrow-right-circle me-2"></i><span>' . htmlspecialchars($row['nama_kelas']) . '</span>
                            </a>';
                        }
                    } else {
                        echo '<span class="nav-link text-muted">Tidak ada data</span>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Sekben I -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSekben1">
                    <i class="bi bi-wallet2"></i> Sekben I
                </button>
            </h2>
            <div id="collapseSekben1" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="spp/input_sekben1.php" target="main_frame"><i class="bi bi-cash-stack me-2"></i> Pemasukan SPP</a>
                    <a class="nav-link" href="spp/input_pengeluaran_spp.php" target="main_frame"><i class="bi bi-credit-card-2-back me-2"></i> Input Pengeluaran SPP</a>
                    <a class="nav-link" href="spp/list.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Lihat List Sekben I</a>
                </div>
            </div>
        </div>

        <!-- Sekben II -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSekben2">
                    <i class="bi bi-piggy-bank-fill"></i> Sekben II
                </button>
            </h2>
            <div id="collapseSekben2" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="./du/input_du1.php" target="main_frame"><i class="bi bi-cash-coin me-2"></i>daftar ulang</a>
                     <a class="nav-link" href="./db/input_db.php" target="main_frame"><i class="bi bi-cash-coin me-2"></i>daftar baru</a>
                    <a class="nav-link" href="db/list.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Lihat List Sekben DB</a>
                    <a class="nav-link" href="du/list.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Lihat List Sekben DU</a>

                </div>
            </div>
        </div>

        <!-- TU -->
        <!-- <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTU">
                    <i class="bi bi-building-fill"></i> TU
                </button>
            </h2>
            <div id="collapseTU" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="Rekapan_Harian.php" target="main_frame"><i class="bi bi-calendar-day me-2"></i> Rekapan Harian</a>
                    <a class="nav-link" href="Rekapan_Bulanan.php" target="main_frame"><i class="bi bi-calendar-month me-2"></i> Rekapan Bulanan</a>
                    <a class="nav-link" href="laporan_keuangan.php" target="main_frame"><i class="bi bi-graph-up me-2"></i> Laporan Keuangan</a>
                    <a class="nav-link" href="absen_guru.php" target="main_frame"><i class="bi bi-clipboard-check me-2"></i> Absen Guru</a>
                </div>
            </div>
        </div> -->
        
        <!-- Data Guru -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuru">
                    <i class="bi bi-person-fill"></i> Data Guru
                </button>
            </h2>
            <div id="collapseGuru" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="guru/input_guru.php" target="main_frame"><i class="bi bi-plus-circle me-2"></i> Input Guru</a>
                    <a class="nav-link" href="guru/list_guru.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> List Guru</a>
                </div>
            </div>
        </div>
        
        <!-- Menambahkan menu absensi siswa di bagian TU -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAbsensi">
                    <i class="bi bi-calendar-check"></i> Absensi guru
                </button>
            </h2>
            <div id="collapseAbsensi" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="guru/absen_guru.php" target="main_frame"><i class="bi bi-plus-circle me-2"></i> Input Absensi</a>
                    <a class="nav-link" href="guru/report_absenguru.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Laporan Absensi</a>
                </div>
            </div>
        </div>
        
        <!-- Laporan -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLaporan">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                </button>
            </h2>
            <div id="collapseLaporan" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="tu/export_absenguru_excel.php" target="main_frame"><i class="bi bi-calendar-day me-2"></i> Laporan absen dan gaji</a>
                </div>
            </div>
            <div id="collapseLaporan" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="tu/laporan_spp_excel.php" target="main_frame"><i class="bi bi-calendar-day me-2"></i> Laporan SPP Harian</a>
                </div>
            </div>
        </div>

        <?php if ($role === 'admin') : ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUser">
                        <i class="bi bi-people me-2"></i> Manajemen User
                    </button>
                </h2>
                <div id="collapseUser" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                    <div class="accordion-body submenu">
                        <a class="nav-link" href="./login/users.php" target="main_frame">
                            <i class="bi bi-person-badge me-2"></i> Kelola User
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>




        <form action="./login/logout.php" method="post">
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>


    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- <h3 class="fw-bold mb-4">Dashboard</h3>
     -->
   
   
  <!-- Content Area -->
    <iframe name="main_frame" class="content-iframe" src="home.php"></iframe>

    
    <!-- Footer -->
    <div class="app-footer">
        <p class="mb-0">Sistem Informasi TPQ © <?php echo date('Y'); ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = menuToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });
        
        // Close sidebar when a menu item is clicked on mobile
        const menuItems = document.querySelectorAll('.sidebar .nav-link');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>