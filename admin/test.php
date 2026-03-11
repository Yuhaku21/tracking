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
    <title>CRUD Nasabah + Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-4">
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

        <h3>Peta Lokasi Nasabah</h3>
        <div class="card p-3">
            <div id="map"></div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var map = L.map('map').setView([-8.5830695, 116.3202515], 12); // Default Lombok

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        <?php
        $data = $pdo->query("SELECT * FROM nasabah");
        foreach ($data as $d) {
        ?>
            L.marker([<?= $d['latitude'] ?>, <?= $d['longitude'] ?>])
                .addTo(map)
                .bindPopup("<b><?= $d['nama'] ?></b><br><?= $d['alamat'] ?><br>Status: <?= $d['status'] ?>");
        <?php } ?>
    </script>

</body>

</html>