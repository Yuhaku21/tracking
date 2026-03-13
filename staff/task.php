<?php
include '../config/koneksi.php';

// TAMBAH DATA
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("INSERT INTO nasabah 
        (nama, alamat, status, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?)");
}

$cari = $_GET['cari'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM nasabah WHERE 1=1";
$params = [];

// fitur cari
if ($cari) {
    $query .= " AND nama LIKE ?";
    $params[] = "%$cari%";
}

// fitur filter status
if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();


// ambil status unik untuk tombol filter
$statusList = $pdo->query("SELECT DISTINCT status FROM nasabah")->fetchAll();





// Pagination

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
                <h2 id="username">Tugas Saya</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tugas Saya</li>
                </ol>
            </nav>

            <!--Fitur Filter and Search-->
            <form class="d-flex mt-2" method="GET">

                <input
                    class="form-control me-2"
                    type="search"
                    name="cari"
                    placeholder="Cari Nasabah..."
                    value="<?= htmlspecialchars($cari ?? '') ?>" />

                <button class="btn btn-outline-success" type="submit">
                    Cari
                </button>

            </form>
            <div class="mt-3">

                <!-- Tombol Semua -->
                <a href="?" class="btn btn-sm btn-secondary">Semua</a>

                <?php foreach ($statusList as $s) { ?>

                    <a href="?status=<?= $s['status'] ?>"
                        class="btn btn-sm btn-outline-primary">

                        <?= $s['status'] ?>

                    </a>

                <?php } ?>

            </div>
            <!--Data Nasabah-->
            <div class="mt-3">

                <?php foreach ($data as $d) { ?>

                    <div class="card shadow-sm mb-2" style="max-width: 400px;">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>
                                <p class="mb-0 fw-semibold" style="font-size:14px;">
                                    <?= $d['nama'] ?>
                                </p>
                                <br>

                                <span class="badge bg-success">
                                    <?= $d['status'] ?>
                                </span>
                            </div>

                            <a href="detail-nasabah.php?id=<?= $d['id'] ?>" class="btn btn-primary btn-sm">
                                Detail
                            </a>

                        </div>
                    </div>

                <?php } ?>

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