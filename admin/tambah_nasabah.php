<?php
include '../config/koneksi.php';

// TAMBAH DATA
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("INSERT INTO nasabah 
        (nama, alamat, status, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nama'],
        $_POST['alamat'],
        $_POST['status'],
        $_POST['latitude'],
        $_POST['longitude']
    ]);

    header("Location: test.php");
    exit;
}

// HAPUS DATA
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM nasabah WHERE id=?");
    $stmt->execute([$_GET['hapus']]);
    header("Location: test.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!--Link style-->
    <link rel="stylesheet" href="../style-dashboard-admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        #map {
            height: 400px;
        }
    </style>
</head>

<body class="bg-light">

    <!--Navbar-->
   <!-- Navbar Atas -->
    <nav class="navbar navbar-light bg-white shadow-sm px-3">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-bold">
            <p>Selamat datang <?= $_SESSION['nama']; ?></p>
        </span>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link active" href="../admin/dashboard-admin.php">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="../admin/data-ao.php">
                        <i class="bi bi-person-badge me-2"></i> Data AO
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="../admin/data-nasabah.php">
                        <i class="bi bi-people me-2"></i> Data Nasabah
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="../admin/pantau-progress.php">
                        <i class="bi bi-clock-history me-2"></i> Pantau Progres
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="../admin/pantau-progress.php">
                        <i class="bi bi-file-earmark-plus me-2"></i> Permintaan Lokasi
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container mt-4">

        <div class="row">
            <div class="col">
                <!--Form Tambah Nasabah-->
                <h3>Form Input Nasabah</h3>
                <div class="card p-3 mb-4">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Nama Nasabah</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" required></textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Latitude</label>
                                <input type="text" name="latitude" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Longitude</label>
                                <input type="text" name="longitude" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--Form Tambah Nasabah-->
            </div>
            <div class="col">
                <!--Koordinat Lokasi Nasabah-->
                <h3>Peta Lokasi Nasabah</h3>
                <div class="card p-3">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <!--Show Table Nasabah-->
        <h3>Data Nasabah</h3>
        <div class="card p-3 mb-4">
            <table class="table table-bordered">
                <tr class="table-dark">
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $no = 1;
                $data = $pdo->query("SELECT * FROM nasabah");
                foreach ($data as $d) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['alamat'] ?></td>
                        <td><?= $d['status'] ?></td>
                        <td><?= $d['latitude'] ?></td>
                        <td><?= $d['longitude'] ?></td>
                        <td>
                            <button
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalHapus<?= $d['id'] ?>">
                                Hapus
                            </button>
                            <!-- Modal Hapus -->
                            <div class="modal fade" id="modalHapus<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            Apakah anda yakin ingin menghapus data nasabah
                                            <br>
                                            <b><?= $d['nama'] ?></b> ?
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <a href="?hapus=<?= $d['id'] ?>" class="btn btn-danger">Ya, Hapus</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <!--Footer-->
        <footer class="text-center">
            <p style="color: grey;">Aplikasi Versi 1.0.0</p>
        </footer>



    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. Inisialisasi Peta (Default ke Mataram)
        var map = L.map('map').setView([-8.5830816, 116.0524079], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // 2. Tambahkan Marker Input (Yang bisa digerakkan)
        var inputMarker = L.marker([-8.5830816, 116.0524079], {
            draggable: true
        }).addTo(map);

        inputMarker.bindPopup("Geser saya ke lokasi nasabah").openPopup();

        // Fungsi untuk update nilai input di form
        function updateFields(lat, lng) {
            document.getElementsByName('latitude')[0].value = lat.toFixed(8);
            document.getElementsByName('longitude')[0].value = lng.toFixed(8);
        }

        // Event: Saat marker digeser
        inputMarker.on('dragend', function(e) {
            var position = inputMarker.getLatLng();
            updateFields(position.lat, position.lng);
        });

        // Event: Saat peta diklik
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            inputMarker.setLatLng([lat, lng]);
            updateFields(lat, lng);
        });

        // 3. Menampilkan Data Nasabah dari Database (Marker Biru Statis)
        <?php
        $data = $pdo->query("SELECT * FROM nasabah");
        foreach ($data as $d) {
            // Pastikan latitude dan longitude tidak kosong
            if (!empty($d['latitude']) && !empty($d['longitude'])) {
        ?>
                L.marker([<?= $d['latitude'] ?>, <?= $d['longitude'] ?>])
                    .addTo(map)
                    .bindPopup("<b><?= addslashes($d['nama']) ?></b><br><?= addslashes($d['alamat']) ?><br>Status: <?= $d['status'] ?>");
        <?php
            }
        }
        ?>
    </script>

</body>

</html>