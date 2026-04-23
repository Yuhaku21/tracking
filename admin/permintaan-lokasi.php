<?php
require '../middleware/auth_admin.php';
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
<html>

<head>
    <title>Tambah Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!--Link style-->
    <link rel="stylesheet" href="../style-dashboard-admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                    <a class="nav-link" href="../admin/permintaan-lokasi.php">
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

        <!-- Table -->
        <div class="card mt-4">
            <div class="card-body">
                <h5>Data Lokasi</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = $pdo->query("SELECT * FROM lokasi_nasabah ORDER BY id DESC");

                        foreach ($data as $row) {
                            echo "<tr>
        <td>{$row['nama']}</td>
        <td>{$row['latitude']}</td>
        <td>{$row['longitude']}</td>
    </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!--Footer-->
        <footer class="text-center">
            <p style="color: grey;">Aplikasi Versi 1.0.0</p>
        </footer>



    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>