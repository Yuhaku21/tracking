<?php
require '../middleware/auth_admin.php';
require '../config/koneksi.php'; // koneksi database

// ================= AMBIL DATA PROGRESS =================
$stmt = $pdo->prepare("
    SELECT 
        progress_tugas.*,
        users.nama AS nama_user,
        nasabah.nama AS nama_nasabah
    FROM progress_tugas
    LEFT JOIN users ON progress_tugas.user_id = users.id
    LEFT JOIN nasabah ON progress_tugas.nasabah_id = nasabah.id
    ORDER BY progress_tugas.created_at DESC
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
                    <a class="nav-link" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-left"></i> Logout
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

        <div class="row">
                <table class="table">

                    <thead class="table">
                        <tr>
                            <th>Nama User</th>
                            <th>Nama Nasabah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($dataProgress): ?>

                            <?php $no = 1;
                            foreach ($dataProgress as $row): ?>

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
            </div>
        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>