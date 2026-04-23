<?php
require '../middleware/auth_admin.php';
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

// ================= PAGINATION =================
$limit = 10;
$page = $_GET['page'] ?? 1;

if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// ================= HITUNG TOTAL DATA =================
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) 
    FROM progress_tugas
    WHERE 1=1
    $where
");

$stmtTotal->execute();
$totalData = $stmtTotal->fetchColumn();
$totalPage = ceil($totalData / $limit);

// ================= AMBIL DATA =================
$stmt = $pdo->prepare("
    SELECT 
        progress_tugas.*,
        users.nama AS nama_user,
        nasabah.nama AS nama_nasabah
    FROM progress_tugas
    LEFT JOIN users ON progress_tugas.user_id = users.id
    LEFT JOIN nasabah ON progress_tugas.nasabah_id = nasabah.id
    WHERE 1=1
    $where
    ORDER BY progress_tugas.created_at DESC
    LIMIT $limit OFFSET $offset
");

$stmt->execute();
$dataProgress = $stmt->fetchAll();
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
        <h2><b>Pantau Progress</b></h2>

        <!--CTA Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard-admin.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pantau Progress</li>
            </ol>
        </nav>

        <!--Filter-->
        <form method="GET" class="mb-3">

            <div class="d-flex align-items-end gap-2">

                <!-- DROPDOWN -->
                <div style="width: 150px;">
                    <label class="form-label fw-semibold">Filter Waktu</label>
                    <select name="filter" class="form-select" onchange="this.form.submit()"">
                        <option value="">Semua</option>
                        <option value=" hari" <?= ($filter == 'hari') ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="bulan" <?= ($filter == 'bulan') ? 'selected' : '' ?>>1 Bulan</option>
                        <option value="6bulan" <?= ($filter == '6bulan') ? 'selected' : '' ?>>6 Bulan</option>
                    </select>
                </div>

                <!-- BUTTON PDF -->
                <div>
                    <a href="cetak_pdf.php?filter=<?= $filter ?>" class="btn btn-danger">
                        Cetak PDF <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>

            </div>

        </form>



        <div class="row">
            <table class="table">

                <thead class="table">
                    <tr>
                        <th>Petugas AO</th>
                        <th>Nama Nasabah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Bukti Kunjungan</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($dataProgress): ?>

                        <?php foreach ($dataProgress as $row): ?>

                            <tr>

                                <td><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['nama_nasabah'] ?? '-') ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>

                                <td>
                                    <?php
                                    $color = $row['status'] == 'selesai' ? 'success' : 'warning';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>

                                <!-- 🔥 KOLOM FOTO -->
                                <td>
                                    <?php if (!empty($row['foto'])): ?>

                                        <img src="../assets/<?= htmlspecialchars($row['foto']) ?>"
                                            width="80"
                                            class="rounded shadow"
                                            style="cursor:pointer"
                                            onclick="previewGambar(this.src)">

                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="text-center">
                                Belum ada data progress
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
            <!-- Modal Preview -->
            <div class="modal fade" id="modalPreview" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark border-0">
                        <div class="modal-body text-center">
                            <img id="imgPreview" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAGINATION -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function previewGambar(src) {
        document.getElementById('imgPreview').src = src;

        let modal = new bootstrap.Modal(document.getElementById('modalPreview'));
        modal.show();
    }
</script>

</body>

</html>