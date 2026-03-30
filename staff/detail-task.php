<?php
include '../config/koneksi.php';
session_start();

// ================= VALIDASI LOGIN =================
if (!isset($_SESSION['user_id'])) {
    die("❌ Anda belum login");
}

$user_id = $_SESSION['user_id'];


// ================= AMBIL ID DARI URL =================
$id = $_GET['id'] ?? null;

if (!$id) {
    die("❌ ID tidak ditemukan");
}


// ================= AMBIL DATA NASABAH =================
$stmt = $pdo->prepare("SELECT * FROM nasabah WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("❌ Data nasabah tidak ditemukan");
}


// ================= PROSES SUBMIT =================
if (isset($_POST['selesai'])) {

    $catatan = $_POST['catatan'] ?? '';

    // validasi input
    if (empty($catatan)) {
        echo "<script>alert('Catatan tidak boleh kosong!');</script>";
    } else {

        $stmt = $pdo->prepare("INSERT INTO progress_tugas 
            (user_id, nasabah_id, catatan, status) 
            VALUES (?, ?, ?, ?)");

        $stmt->execute([
            $user_id,
            $id,
            $catatan,
            'selesai'
        ]);

        // trigger popup sukses
        echo "<script>
            localStorage.setItem('success', '1');
            window.location.href = window.location.href;
        </script>";
        exit;
    }
}

$stmt = $pdo->prepare("
    SELECT 
        progress_tugas.*, 
        users.nama AS nama_user
    FROM progress_tugas
    LEFT JOIN users ON progress_tugas.user_id = users.id
    WHERE progress_tugas.nasabah_id = ?
");
$stmt->execute([$id]);
$progress = $stmt->fetchAll();
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

                            <h5>Menentukan lokasi anda dengan nasabah</h5>
                            <p class="text-muted small">
                                Pastikan GPS aktif dan izin lokasi diberikan
                            </p>

                            <button id="skipBtn" class="btn btn-outline-secondary mt-3">
                                Lewati
                            </button>
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
                    <textarea name="catatan" id="catatan" class="form-control" rows="4"
                        placeholder="Buat Catatan..." disabled></textarea>
                </div>

                <!-- BUTTON -->
                <div class="d-grid gap-2">
                    <button type="submit" name="selesai" id="btnSelesai" class="btn btn-primary" disabled>
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
        document.addEventListener("DOMContentLoaded", function() {

            // ================= HITUNG JARAK =================
            function hitungJarak(lat1, lon1, lat2, lon2) {
                let R = 6371e3;
                let φ1 = lat1 * Math.PI / 180;
                let φ2 = lat2 * Math.PI / 180;
                let Δφ = (lat2 - lat1) * Math.PI / 180;
                let Δλ = (lon2 - lon1) * Math.PI / 180;

                let a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

                let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c;
            }

            // ================= AKTIFKAN FORM =================
            function aktifkanForm() {
                document.getElementById("catatan").disabled = false;
                document.getElementById("btnSelesai").disabled = false;
            }

            // ================= INIT MODAL =================
            let loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // ================= DATA NASABAH =================
            let latNasabah = <?= $data['latitude'] ?? -8.583069 ?>;
            let lngNasabah = <?= $data['longitude'] ?? 116.320251 ?>;

            // ================= INIT MAP =================
            let map = L.map('map').setView([latNasabah, lngNasabah], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([latNasabah, lngNasabah]).addTo(map)
                .bindPopup("Lokasi Nasabah")
                .openPopup();

            // ================= BUTTON LEWATI =================
            document.getElementById("skipBtn").addEventListener("click", function() {
                loadingModal.hide();
                aktifkanForm();
            });

            // ================= TRACKING USER =================
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(function(position) {

                    let latUser = position.coords.latitude;
                    let lngUser = position.coords.longitude;

                    let jarak = hitungJarak(latUser, lngUser, latNasabah, lngNasabah);

                    console.log("Jarak ke nasabah: " + jarak.toFixed(2) + " meter");

                    // ✅ AUTO AKTIF kalau dekat
                    if (jarak <= 5) {
                        loadingModal.hide();
                        aktifkanForm();
                    }

                }, function(error) {
                    alert("Gagal mengambil lokasi, silakan gunakan tombol lewati.");
                }, {
                    enableHighAccuracy: true
                });
            }

            // ================= POPUP SUCCESS =================
            if (localStorage.getItem('success') === '1') {
                localStorage.removeItem('success');

                setTimeout(() => {
                    alert("✅ Anda telah berhasil menyelesaikan tugas ini");
                    window.location.href = "task.php";
                }, 300);
            }

        });
    </script>
</body>

</html>