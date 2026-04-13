<?php
require '../middleware/auth_staff.php';
require '../config/koneksi.php';

$user_id = $_SESSION['user_id']; // id user login

// TAMBAH CATATAN
if (isset($_POST['tambah'])) {

    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $nama = $_SESSION['nama']; // nama pembuat catatan

    $stmt = $pdo->prepare("INSERT INTO catatan (user_id, dibuat_oleh, judul, isi) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $nama, $judul, $isi]);

    header("Location: test-catatan.php");
    exit;
}

// HAPUS CATATAN
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $stmt = $pdo->prepare("DELETE FROM catatan WHERE id=? AND user_id=?");
    $stmt->execute([$id, $user_id]);

    header("Location: catatan.php");
    exit;
}

// EDIT CATATAN
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];

    $stmt = $pdo->prepare("UPDATE catatan SET judul=?, isi=? WHERE id=? AND user_id=?");
    $stmt->execute([$judul, $isi, $id, $user_id]);

    header("Location: catatan.php");
    exit;
}

// AMBIL DATA CATATAN USER
$stmt = $pdo->prepare("SELECT * FROM catatan WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$data = $stmt->fetchAll();
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
                <h2 id="username">Catatan</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catatan</li>
                </ol>
            </nav>
            <!--Main Content-->
            <a href="buat-catatan.php" class="btn btn-primary" style="font-size: 12px;">Buat Catatan Baru</a>
            <!--Data Dummy-->
            <div class="mt-3">
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): ?>
                        <div class="card shadow-sm border-0 rounded-3 mb-3">
                            <div class="card-body py-3 px-4">

                                <div class="d-flex justify-content-between align-items-start">

                                    <!-- Judul & Info -->
                                    <div>
                                        <h6 class="mb-1 fw-bold">
                                            <?= htmlspecialchars($row['judul']) ?>
                                        </h6>


                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                        </small>

                                        <!-- Isi -->
                                        <p class="mt-2 mb-0" style="font-size: 13px;">
                                            <?= nl2br(htmlspecialchars($row['isi'])) ?>
                                        </p>
                                    </div>

                                    <!-- Tombol Aksi Hapus-->
                                    <div class="text-end">
                                        <a href="?hapus=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus catatan ini?')">
                                            Hapus
                                        </a>
                                        <button
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $row['id'] ?>">
                                            Edit
                                        </button>
                                    </div>
                                    <!-- Tombol Aksi Edit-->

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted mt-4">
                        Belum ada catatan 😢
                    </div>
                <?php endif; ?>
            </div>
            <!-- Modal Edit -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Catatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                <div class="mb-3">
                                    <label class="form-label">Judul</label>
                                    <input type="text" name="judul" class="form-control"
                                        value="<?= htmlspecialchars($row['judul']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Isi</label>
                                    <textarea name="isi" class="form-control" rows="4" required><?= htmlspecialchars($row['isi']) ?></textarea>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" name="edit" class="btn btn-success">
                                    Simpan Perubahan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
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