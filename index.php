<?php
require 'middleware/auth_staff.php';
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
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="app">
        <div class="header">
            AO Tracking
        </div>

        <div class="content">

            <!-- Modal Loading -->
            <!-- Modal Loading -->
            <div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-body text-center py-5">

                            <div class="d-flex justify-content-center mb-4">
                                <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
                                </div>
                            </div>

                            <h5 class="fw-semibold">Mengambil Lokasi...</h5>
                            <p class="text-muted small mb-0">
                                Sistem sedang menentukan titik koordinat terbaik
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <div class="welcome">
                <h2>Halo Selamat Datang  </h2>
                <h2 id="username"><?= $_SESSION['nama']; ?></h2>
                <p>Jangan Lupa Tetap Semangat dan Bersyukur ya</p>
            </div>

            <!-- MAP -->
            <div class="shadow-lg" id="map"></div>

            <!-- MENU -->
            <div class="menu-grid">

                <a href="task.html" style="text-decoration: none;">
                    <div class="menu-item" style="text-decoration: none;">
                        <p style="font-size: 30px; ">📝</p>
                        <p>Tugas Saya</p>
                    </div>
                </a>

                <a href="history.html" style="text-decoration: none;">
                    <div class="menu-item">
                        <p style="font-size: 30px;">📍</p>
                        <p>History</p>
                    </div>
                </a>

                <a href="catatan.html" style="text-decoration: none;">
                    <div class="menu-item">
                        <p style="font-size: 30px;">📌</p>
                        <p>Catatan</p>
                    </div>
                </a>

                <a href="pengaturan.php" style="text-decoration: none;">
                    <div class="menu-item">
                        <p style="font-size: 30px;">⚙</p>
                        <p>Pengaturan</p>
                    </div>
                </a>

            </div>
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