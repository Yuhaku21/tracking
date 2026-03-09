<?php
require '../middleware/auth_admin.php';
require '../config/koneksi.php'; // koneksi database

/* =======================
   AMBIL DATA STAFF
======================= */
$stmt = $pdo->query("SELECT * FROM users WHERE role='staff' ORDER BY id DESC");
$staff = $stmt->fetchAll();
/* =======================
   PROSES EDIT STAFF
======================= */
if (isset($_POST['update'])) {
    $id   = $_POST['id'];
    $nama = $_POST['nama'];
    $kode = $_POST['kode_kantor'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama=?, kode_kantor=?, password=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $kode, $password, $id]);
    } else {
        $sql = "UPDATE users SET nama=?, kode_kantor=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $kode, $id]);
    }

    header("Location: tambah_staff.php");
    exit;
}

/* =======================
   PROSES HAPUS STAFF
======================= */
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);

    header("Location: tambah_staff.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Account Officer</title>

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

    <!-- Content -->
    <div class="container mt-4">

        <h2><b>Data Account Officer</b></h2>

        <!--CTA Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="data-nasabah.html">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data AO</li>
            </ol>
        </nav>

        <!--Main Content-->
        <a href="tambah_staff.php" class="btn btn-primary" style="font-size: 14px;">Tambah Data AO</a>

        <!--Show data nasabah-->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Kode Kantor</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
                <?php if (count($staff) > 0): ?>
                    <?php $no = 1;
                    foreach ($staff as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['nama']) ?></td>
                            <td><?= htmlspecialchars($s['kode_kantor']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $s['id'] ?>">Edit</button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapus<?= $s['id'] ?>">Hapus</button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="edit<?= $s['id'] ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Staff</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">

                                            <label>Nama</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $s['nama'] ?>" required>

                                            <label class="mt-2">Kode Kantor</label>
                                            <input type="text" name="kode_kantor" class="form-control" value="<?= $s['kode_kantor'] ?>" required>

                                            <label class="mt-2">Password Baru</label>
                                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button class="btn btn-warning" name="update">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL HAPUS -->
                        <div class="modal fade" id="hapus<?= $s['id'] ?>">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Hapus</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin hapus <b><?= htmlspecialchars($s['nama']) ?></b>?
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button class="btn btn-danger" name="hapus">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data staff</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </tbody>
        </table>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>