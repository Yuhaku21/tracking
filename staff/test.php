<?php
require '../middleware/auth_staff.php';
require '../config/koneksi.php';

// ================= AMBIL USER LOGIN =================
$user_id = $_SESSION['user_id'] ?? 0;

// ================= AMBIL DATA PROGRESS USER =================
$stmtUser = $pdo->prepare("
    SELECT 
        progress_tugas.*,
        nasabah.nama AS nama_nasabah,
        nasabah.alamat
    FROM progress_tugas
    LEFT JOIN nasabah ON progress_tugas.nasabah_id = nasabah.id
    WHERE progress_tugas.user_id = ?
    ORDER BY progress_tugas.created_at DESC
");

$stmtUser->execute([$user_id]);
$dataUser = $stmtUser->fetchAll();
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
                <h2 id="username">History</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">History</li>
                </ol>
            </nav>
            <!--Data Dummy-->
             <!-- DATA -->
    <?php if ($dataUser): ?>

        <?php foreach ($dataUser as $row): ?>

            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <!-- Nama Nasabah -->
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($row['nama_nasabah']) ?>
                    </h6>

                    <!-- Alamat -->
                    <p class="text-muted small mb-2">
                        <i class="bi bi-geo-alt"></i>
                        <?= htmlspecialchars($row['alamat']) ?>
                    </p>

                    <!-- Status -->
                    <?php
                    $color = $row['status'] == 'selesai' ? 'success' : 'warning';
                    ?>
                    <span class="badge bg-<?= $color ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>

                    <!-- Tanggal -->
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i>
                            <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                        </small>
                    </div>

                    <!-- Catatan -->
                    <?php if (!empty($row['catatan'])): ?>
                        <div class="mt-2 p-2 bg-light rounded">
                            <small>
                                <i class="bi bi-journal-text"></i>
                                <?= htmlspecialchars($row['catatan']) ?>
                            </small>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> Belum ada progress tugas
        </div>

    <?php endif; ?>
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