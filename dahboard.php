<?php 
include_once 'db.php';
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
        
        body { 
            background-color: #f5f7fa; 
            overflow-x: hidden;
        }
        
        /* Sidebar styling */
        .sidebar {
            background-color: #fff;
            height: 100vh;
            padding: 20px;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            position: fixed;
            width: var(--sidebar-width);
            z-index: 1000;
            transition: transform 0.3s ease;
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
        
        .submenu a {
            font-size: 0.9rem;
            padding-left: 2rem;
        }
        
        /* Profile section */
        .profile-section {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 35px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px;
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
            width: 100%;
            height: calc(100vh - 150px);
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
            margin-top: 20px;
            font-size: 0.85rem;
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
        <img src="https://media.canva.com/v2/image-resize/format:JPG/height:800/quality:92/uri:ifs%3A%2F%2FM%2Fec183fca-6320-486a-b040-5112c98da034/watermark:F/width:738?csig=AAAAAAAAAAAAAAAAAAAAAHToxb60bACl3EDuqIXaDsHkQmOI4CP-2r90dRPGwvoy&exp=1755206252&osig=AAAAAAAAAAAAAAAAAAAAAPbNvuMzvOjqIYNE6fdzTBJX_oXL0kxqoOoazoeXlYMv&signer=media-rpc&x-canva-quality=screen" alt="Profil" class="profile-img">
        <p class="fw-bold mt-2">Profil</p>
    </div>

    <!-- Dashboard -->
    <div class="mb-2">
        <a class="btn w-100 text-start btn-primary" href="home.php" target="main_frame">
            <i class="bi bi-house-door me-2"></i> Dashboard
        </a>
    </div>

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
                            echo '<a class="nav-link" href="list_siswa.php?kelas=' . urlencode($row['id']) . '" target="main_frame">
                                <i class="bi bi-arrow-right-circle me-2"></i>' . htmlspecialchars($row['nama_kelas']) .
                            '</a>';
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
                    <a class="nav-link" href="list_sekben1.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Lihat List Sekben I</a>
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
                    <a class="nav-link" href="input_sekben2.php" target="main_frame"><i class="bi bi-cash-coin me-2"></i> Pemasukan DU/B</a>
                    <a class="nav-link" href="pengeluaran_sekben2.php" target="main_frame"><i class="bi bi-cart-dash me-2"></i> Pengeluaran Event</a>
                    <a class="nav-link" href="list_sekben2.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Lihat List Sekben II</a>
                </div>
            </div>
        </div>

        <!-- TU -->
        <div class="accordion-item">
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
        </div>
        
        <!-- Data Guru -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuru">
                    <i class="bi bi-person-fill"></i> Data Guru
                </button>
            </h2>
            <div id="collapseGuru" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="input_guru.php" target="main_frame"><i class="bi bi-plus-circle me-2"></i> Input Guru</a>
                    <a class="nav-link" href="list_guru.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> List Guru</a>
                </div>
            </div>
        </div>
        
        <!-- Menambahkan menu absensi siswa di bagian TU -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAbsensi">
                    <i class="bi bi-calendar-check"></i> Absensi Siswa
                </button>
            </h2>
            <div id="collapseAbsensi" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                <div class="accordion-body submenu">
                    <a class="nav-link" href="absen.php" target="main_frame"><i class="bi bi-plus-circle me-2"></i> Input Absensi</a>
                    <a class="nav-link" href="list_absensi.php" target="main_frame"><i class="bi bi-list-ul me-2"></i> Laporan Absensi</a>
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
                    <a class="nav-link" href="laporan_harian.php" target="main_frame"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h3 class="fw-bold mb-4">Dashboard</h3>
    
   
   
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