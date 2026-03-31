<?php
require '../middleware/auth_staff.php';
require '../config/koneksi.php';


// ================= FILTER =================
$filter = $_GET['filter'] ?? '';
$where = "";

if ($filter == 'hari') {
    $where = "AND DATE(progress_tugas.created_at) = CURDATE()";
} elseif ($filter == 'bulan') {
    $where = "AND progress_tugas.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
} elseif ($filter == '6bulan') {
    $where = "AND progress_tugas.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
}

// ================= USER LOGIN =================
$user_id = $_SESSION['user_id'] ?? 0;

// ================= PAGINATION (PINDAH KE ATAS 🔥) =================
$limit = 5;
$page = $_GET['page'] ?? 1;

if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// ================= HITUNG TOTAL DATA =================
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) 
    FROM progress_tugas
    WHERE user_id = ?
    $where
");
$stmtTotal->execute([$user_id]);

$totalData = $stmtTotal->fetchColumn();
$totalPage = ceil($totalData / $limit);

// ================= AMBIL DATA =================
$stmtUser = $pdo->prepare("
    SELECT 
        progress_tugas.*,
        nasabah.nama AS nama_nasabah,
        nasabah.alamat
    FROM progress_tugas
    LEFT JOIN nasabah ON progress_tugas.nasabah_id = nasabah.id
    WHERE progress_tugas.user_id = ?
    $where
    ORDER BY progress_tugas.created_at DESC
    LIMIT $limit OFFSET $offset
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
            <!--Filter-->
            <form method="GET" class="mb-3">
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="hari" <?= ($filter == 'hari') ? 'selected' : '' ?>>Hari Ini</option>
                    <option value="bulan" <?= ($filter == 'bulan') ? 'selected' : '' ?>>1 Bulan</option>
                    <option value="6bulan" <?= ($filter == '6bulan') ? 'selected' : '' ?>>6 Bulan</option>
                </select>
            </form>
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
                <div class="task-not-found text-center mt-5">
                    <h4 style="font-size: medium;">Belum ada progress Tugas, Klik tombol dibawah untuk menyelesaikan tugas</h4>
                <a class="btn btn-primary" href="../staff/task.php">Lihat Tugas</a>
                </div>
            <?php endif; ?>
            <!--Pagination-->
            <nav>
                <ul class="pagination justify-content-center">

                    <?php for ($i = 1; $i <= $totalPage; $i++): ?>

                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= $i ?>&filter=<?= $filter ?>">
                                <?= $i ?>
                            </a>
                        </li>

                    <?php endfor; ?>

                </ul>
            </nav>
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