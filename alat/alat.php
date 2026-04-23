<?php
require '../middleware/auth_staff.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AO Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Google Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

    <div class="app">
        <div class="header">
            <h3 style="font-size: 16px;">Damara Tracking Mobile</h3>
        </div>

        <div class="content">
            <!--HeroSection-->
            <div class="welcome">
                <h2 id="username">Alat Bantuan</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Alat</li>
                </ol>
            </nav>
            <!--Menu Pengaturan-->
            <!--Catatan-->
            <a href="../staff/catatan.php" style="text-decoration: none;">
                <div class="mt-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Nama -->
                                <div>
                                    <h6 class="mb-1" style="font-size: 14px;">👉 Catatan Saya</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!--Pengajuan Kendala-->
            <a href="../staff/tambah-lokasi.php" style="text-decoration: none;">
                <div class="mt-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Nama -->
                                <div>
                                    <h6 class="mb-1" style="font-size: 14px;">👉 Tambah Lokasi</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!--Pengajuan Kendala-->
            <a href="../alat/simulasi-kredit.php" style="text-decoration: none;">
                <div class="mt-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Nama -->
                                <div>
                                    <h6 class="mb-1" style="font-size: 14px;">👉 Simulasi Kredit</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!--Pengajuan Kendala-->
            <a href="#" style="text-decoration: none;">
                <div class="mt-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Nama -->
                                <div>
                                    <h6 class="mb-1" style="font-size: 14px;">👉 Form Pengajuan Kredit</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!--Pengajuan Kendala-->
            <a href="#" style="text-decoration: none;">
                <div class="mt-3">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Nama -->
                                <div>
                                    <h6 class="mb-1" style="font-size: 14px;">👉 Analisa Kredit (AI)</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="main.js"></script>

</body>

</html>