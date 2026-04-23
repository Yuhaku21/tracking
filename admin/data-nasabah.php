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

    header("Location: data-nasabah.php");
    exit;
}

// UPDATE DATA
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("UPDATE nasabah SET 
        nama = ?, 
        status = ?
        WHERE id = ?");

    $stmt->execute([
        $_POST['nama'],
        $_POST['status'],
        $_POST['id']
    ]);

    header("Location: data-nasabah.php");
    exit;
}

// HAPUS DATA
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM nasabah WHERE id=?");
    $stmt->execute([$_GET['hapus']]);
    header("Location: data-nasabah.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!--Link style-->
    <link rel="stylesheet" href="style-dashboard-admin.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
        }
    </style>
</head>

<body>

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

    <!-- Content -->
    <div class="container mt-4">

        <h2><b>Data Nasabah</b></h2>

        <!--CTA Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="data-nasabah.html">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Nasabah</li>
            </ol>
        </nav>

        <!--Main Content-->
        <a class="btn btn-primary" href="../admin/tambah_nasabah.php" style="font-size: 14px;">Tambah Data Nasabah</a>

        <!--Show data nasabah-->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $data = $pdo->query("SELECT * FROM nasabah");
                foreach ($data as $d) {
                ?>
                    <tr>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['alamat'] ?></td>
                        <td><?= $d['status'] ?></td>
                        <td>
                            <!-- Button Edit -->
                            <button
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit<?= $d['id'] ?>">
                                Edit
                            </button>
                            <!--butoon hapus-->
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
                            <!--Modal Edit -->
                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Data Nasabah</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden" name="id" value="<?= $d['id'] ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= $d['nama'] ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="Aktif" <?= $d['status'] == "Aktif" ? 'selected' : '' ?>>Aktif</option>
                                                        <option value="Tidak Aktif" <?= $d['status'] == "Tidak Aktif" ? 'selected' : '' ?>>Tidak Aktif</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="update" class="btn btn-warning">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!--Footer-->
        <footer class="fixed-bottom text-center">
            <p style="color: grey;">Aplikasi Versi 1.0.0</p>
        </footer>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>