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

// ========== PROSES DATA SELESAI ===========
if (isset($_POST['selesai'])) {

    $catatan = $_POST['catatan'] ?? '';
    $foto = $_POST['foto'] ?? '';

    if (empty($catatan) || empty($foto)) {
        echo "<script>alert('Catatan & Foto wajib diisi!');</script>";
    } else {

        // 🔥 PROSES SIMPAN GAMBAR
        $folder = __DIR__ . "/../assets/";
        $namaFile = "bukti_" . time() . ".png";

        $foto = str_replace('data:image/png;base64,', '', $foto);
        $foto = str_replace(' ', '+', $foto);
        $data = base64_decode($foto);

        $path = $folder . $namaFile;

        if (file_put_contents($path, $data)) {
            echo "✅ Gambar berhasil disimpan: " . $path;
        } else {
            die("❌ Gagal menyimpan gambar ke folder");
        }

        // 🔥 SIMPAN KE DATABASE
        $stmt = $pdo->prepare("INSERT INTO progress_tugas 
            (user_id, nasabah_id, catatan, status, foto) 
            VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([
            $user_id,
            $id,
            $catatan,
            'selesai',
            $namaFile
        ]);

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

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

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Ambil Bukti Kunjungan 📸
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Bukti Kunjungan</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <!--Ambil Gambar -->
                                <div class="mb-3 text-center">
                                    <video id="video" autoplay class="w-100 rounded mb-2"></video>

                                    <!--Switch Kamera-->
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-dark" onclick="switchCamera()">
                                            🔄 Ganti Kamera
                                        </button>
                                    </div>

                                    <button type="button" class="btn btn-secondary mb-2" onclick="ambilFoto()">
                                        📸 Ambil Foto
                                    </button>

                                    <canvas id="canvas" style="display:none;"></canvas>

                                    <img id="preview" class="img-fluid rounded mt-2 mb-2" />

                                    <!-- Hidden input untuk kirim ke PHP -->
                                    <input type="hidden" name="foto" id="foto">

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" onclick="simpanFoto()">
                                    ✅ Gunakan Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔥 HASIL FOTO DI LUAR MODAL -->
                <div class="text-center mt-3 mb-3">
                    <img id="hasilLuar" class="img-fluid rounded shadow" style="display:none;" />
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
        <!-- Modal Jauh -->
        <div class="modal fade" id="modalJauh" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">

                    <h5 class="text-danger">❌ Lokasi Tidak Valid</h5>
                    <p>Maaf, Anda terlalu jauh dari lokasi nasabah</p>

                    <button class="btn btn-danger" data-bs-dismiss="modal">
                        OK
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let video = document.getElementById('video');
        let currentStream = null;
        let facingMode = "environment"; // default belakang

        // 🔥 START CAMERA
        async function startCamera() {
            try {
                // matikan kamera lama
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                }

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: facingMode
                    },
                    audio: false
                });

                video.srcObject = stream;
                currentStream = stream;

            } catch (err) {
                alert("❌ Kamera tidak bisa diakses: " + err.message);
            }
        }

        // 🛑 STOP CAMERA
        function stopCamera() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
        }

        // 🔄 SWITCH CAMERA
        function switchCamera() {
            facingMode = (facingMode === "user") ? "environment" : "user";
            startCamera();
        }

        // 📸 AMBIL FOTO
        function ambilFoto() {
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');
            const inputFoto = document.getElementById('foto');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            const dataURL = canvas.toDataURL('image/png');

            preview.src = dataURL;
            inputFoto.value = dataURL;
        }

        // ✅ SIMPAN FOTO (tampilkan di luar modal)
        function simpanFoto() {
            const preview = document.getElementById('preview');
            const hasilLuar = document.getElementById('hasilLuar');

            if (!preview.src) {
                alert("❌ Ambil foto dulu!");
                return;
            }

            hasilLuar.src = preview.src;
            hasilLuar.style.display = "block";

            // tutup modal
            const modalEl = document.getElementById('exampleModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        }

        // 🎯 EVENT MODAL

        // saat modal dibuka → nyalakan kamera
        document.getElementById('exampleModal').addEventListener('shown.bs.modal', function() {
            startCamera();
        });

        // saat modal ditutup → matikan kamera
        document.getElementById('exampleModal').addEventListener('hidden.bs.modal', function() {
            stopCamera();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            let modalJauh = new bootstrap.Modal(document.getElementById('modalJauh'));

            loadingModal.show();

            let latNasabah = <?= $data['latitude'] ?? -8.583069 ?>;
            let lngNasabah = <?= $data['longitude'] ?? 116.320251 ?>;

            let map = L.map('map').setView([latNasabah, lngNasabah], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            let markerNasabah = L.marker([latNasabah, lngNasabah]).addTo(map)
                .bindPopup("📍 Lokasi Nasabah");

            let markerUser;
            let routingControl;
            let sudahCek = false;

            function aktifkanForm() {
                document.getElementById("catatan").disabled = false;
                document.getElementById("btnSelesai").disabled = false;
            }

            document.getElementById("skipBtn").onclick = () => {
                loadingModal.hide();
                aktifkanForm();
            };

            // ================= REALTIME =================
            navigator.geolocation.watchPosition(
                function(position) {

                    let latUser = position.coords.latitude;
                    let lngUser = position.coords.longitude;

                    // ================= MARKER USER =================
                    if (!markerUser) {
                        markerUser = L.marker([latUser, lngUser]).addTo(map);
                    } else {
                        markerUser.setLatLng([latUser, lngUser]);
                    }

                    map.setView([latUser, lngUser], 15);

                    // ================= ROUTING (HANYA SEKALI) =================
                    if (!routingControl) {
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(latUser, lngUser),
                                L.latLng(latNasabah, lngNasabah)
                            ],
                            routeWhileDragging: false,
                            addWaypoints: false,
                            draggableWaypoints: false,
                            createMarker: () => null,
                            lineOptions: {
                                styles: [{
                                    color: 'blue',
                                    weight: 5
                                }]
                            }
                        }).addTo(map);

                        // 🔥 AMBIL DATA ROUTE
                        routingControl.on('routesfound', function(e) {

                            let route = e.routes[0];

                            let jarak = route.summary.totalDistance;
                            let waktu = route.summary.totalTime;

                            let jarakText = (jarak < 1000) ?
                                jarak.toFixed(0) + " meter" :
                                (jarak / 1000).toFixed(2) + " km";

                            let menit = Math.round(waktu / 60);

                            // 🔥 POPUP DI MARKER USER
                            markerUser.bindPopup(`
                        <b>📍 Posisi Anda</b><br>
                        Jarak: ${jarakText}<br>
                        Estimasi: ${menit} menit
                    `);

                            // ================= VALIDASI =================
                            if (!sudahCek) {
                                sudahCek = true;
                                loadingModal.hide();

                                if (jarak <= 20) {
                                    aktifkanForm();
                                    alert("✅ Anda berada di area nasabah");
                                } else {
                                    modalJauh.show();
                                }
                            }

                        });

                    } else {
                        // 🔥 UPDATE POSISI TANPA BUAT ULANG ROUTE
                        routingControl.setWaypoints([
                            L.latLng(latUser, lngUser),
                            L.latLng(latNasabah, lngNasabah)
                        ]);
                    }

                },
                function(error) {
                    console.log(error);
                    loadingModal.hide();
                    alert("❌ Gagal mengambil lokasi");
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );

        });
    </script>

</body>

</html>