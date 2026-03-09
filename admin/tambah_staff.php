<?php
require '../config/koneksi.php';

/* =======================
   PROSES TAMBAH STAFF
======================= */
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $kode = $_POST['kode_kantor'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama,kode_kantor,password,role) VALUES (?,?,?,'staff')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $kode, $password]);

    header("Location: tambah_staff.php");
    exit;
}

/* =======================
   AMBIL DATA STAFF
======================= */
$stmt = $pdo->query("SELECT * FROM users WHERE role='staff' ORDER BY id DESC");
$staff = $stmt->fetchAll();
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!--Link style-->
    <link rel="stylesheet" href="style-dashboard-admin.css">
</head>

<body class="bg-light">
    <!--Navbar--><!-- Navbar Atas -->
    <nav class="navbar navbar-light bg-white shadow-sm px-3">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-bold">Dashboard Admin</span>
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
                    <a class="nav-link active" href="dashboard-admin.php">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-badge me-2"></i> Data AO
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="data-nasabah.php">
                        <i class="bi bi-people me-2"></i> Data Nasabah
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container mt-5">

        <h3 class="mb-4">Tambah Staff</h3>
        <!-- FORM TAMBAH -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Nama Staff</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Kode Kantor</label>
                        <input type="text" name="kode_kantor" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-primary" name="simpan">Tambah Staff</button>
                    
                </form>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>