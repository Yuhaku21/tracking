<?php
include '../config/koneksi.php';

// ambil id dari URL
$id = $_GET['id'] ?? 0;

// ambil data nasabah berdasarkan id
$stmt = $pdo->prepare("SELECT * FROM nasabah WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

// jika data tidak ditemukan
if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <link rel="stylesheet" href="../style.css">

    <style>
        #map {
            height: 250px;
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <div class="app">
        <div class="header">
            AO Tracking
        </div>

        <div class="content">

            <!-- Header -->
            <div class="welcome">
                <h2>Detail Tugas</h2>
                <h5 class="text-muted"><?= $data['nama'] ?></h5>
            </div>

            <!-- Modal Loading -->
            <div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem;"></div>
                            <h5>Mengambil Lokasi...</h5>
                            <p class="text-muted small">
                                Sistem sedang menentukan titik koordinat terbaik
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP -->
            <div id="map" class="shadow mb-3"></div>

            <!-- STATUS -->
            <div class="mb-2">
                <span class="badge bg-success"><?= $data['status'] ?></span>
            </div>

            <!-- ALAMAT -->
            <p class="text-muted small">
                <?= $data['alamat'] ?>
            </p>

            <!-- CATATAN -->
            <form method="POST" action="">
                <div class="mb-3">
                    <textarea name="catatan" class="form-control" rows="4"
                        placeholder="Buat Catatan..."></textarea>
                </div>

                <!-- BUTTON -->
                <div class="d-grid gap-2">
                    <button type="submit" name="selesai" class="btn btn-primary">
                        Selesaikan Tugas
                    </button>

                    <a href="task.php" class="btn btn-outline-primary">
                        Kembali ke Tugas
                    </a>
                </div>
            </form>

        </div>
    </div>

    <!-- JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // tampilkan modal loading
        let loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        loadingModal.show();

        // ambil koordinat dari PHP
        let lat = <?= $data['latitude'] ?? -8.583069 ?>;
        let lng = <?= $data['longitude'] ?? 116.320251 ?>;

        // init map
        let map = L.map('map').setView([lat, lng], 15);

        // tile
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // marker
        L.marker([lat, lng]).addTo(map)
            .bindPopup("Lokasi Nasabah")
            .openPopup();

        // hide loading setelah map ready
        setTimeout(() => {
            loadingModal.hide();
        }, 1000);
    </script>

</body>

</html>