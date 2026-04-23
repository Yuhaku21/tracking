<?php
require '../middleware/auth_staff.php';
include '../config/koneksi.php';

// ================= HANDLE AJAX =================
if (isset($_POST['action']) && $_POST['action'] == 'simpan') {

    $nama = $_POST['nama'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $stmt = $pdo->prepare("INSERT INTO lokasi_nasabah (nama, latitude, longitude) VALUES (?, ?, ?)");
    $stmt->execute([$nama, $lat, $lng]);

    echo "OK";
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'get') {

    $data = $pdo->query("SELECT * FROM lokasi_nasabah ORDER BY id DESC");

    foreach ($data as $row) {
        echo "<tr>
            <td>{$row['nama']}</td>
            <td>{$row['latitude']}</td>
            <td>{$row['longitude']}</td>
        </tr>";
    }

    exit;
}
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
</head>

<body>

    <div class="app">
        <div class="header">
            AO Tracking
        </div>

        <div class="content">
            <!--HeroSection-->
            <div class="welcome">
                <h2 id="username">Tambah Lokasi</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Lokasi</li>
                </ol>
            </nav>

            <!--Main Content-->
            <div class="card mt-3">
                <div class="card-body">

                    <!-- Nama -->
                    <div class="mb-3">
                        <label>Nama Nasabah</label>
                        <input type="text" id="nama_nb" class="form-control">
                    </div>

                    <!-- Tombol ambil lokasi -->
                    <button class="btn btn-primary mb-3" onclick="getLocation()">
                        Ambil Lokasi
                    </button>

                    <!-- Map -->
                    <div id="map"></div>

                    <!-- Latitude -->
                    <div class="mt-3">
                        <label>Latitude</label>
                        <input type="text" id="lat" class="form-control" readonly>
                    </div>

                    <!-- Longitude -->
                    <div class="mt-3">
                        <label>Longitude</label>
                        <input type="text" id="lng" class="form-control" readonly>
                    </div>

                    <!-- Simpan -->
                    <button class="btn btn-success mt-3" onclick="simpanLokasi()">
                        Simpan Lokasi
                    </button>

                </div>
            </div>

            <!-- Modal Sukses -->
            <div class="modal fade" id="modalSukses" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">

                        <div class="modal-body p-4">
                            <h5 class="mb-3 text-success">
                                <i class="bi bi-check-circle-fill"></i> Sukses!
                            </h5>
                            <p>Berhasil menambahkan lokasi</p>

                            <button type="button" class="btn btn-success mt-2" data-bs-dismiss="modal">
                                OK
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        let map = L.map('map').setView([-8.5830816, 116.0524079], 13);
        let marker;

        // Tile
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Ambil lokasi GPS
        function getLocation() {
            navigator.geolocation.getCurrentPosition(function(position) {

                let lat = position.coords.latitude;
                let lng = position.coords.longitude;

                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;

                map.setView([lat, lng], 15);

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }
            });
        }

        // Simpan data (ke file yang sama)
        function simpanLokasi() {
            let nama = document.getElementById('nama_nb').value;
            let lat = document.getElementById('lat').value;
            let lng = document.getElementById('lng').value;

            if (!nama || !lat || !lng) {
                alert("Lengkapi data dulu!");
                return;
            }

            fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=simpan&nama=${nama}&lat=${lat}&lng=${lng}`
                })
                .then(res => res.text())
                .then(() => {

                    // 🔥 TAMPILKAN MODAL
                    let modal = new bootstrap.Modal(document.getElementById('modalSukses'));
                    modal.show();

                    // reload data
                    loadData();

                    // reset form
                    document.getElementById('nama_nb').value = '';
                    document.getElementById('lat').value = '';
                    document.getElementById('lng').value = '';
                });
        }

        // Load data (dari file yang sama)
        function loadData() {
            fetch('tambah-lokasi.php?action=get')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('dataLokasi').innerHTML = data;
                });
        }

        // auto load
        loadData();
    </script>

</body>

</html>