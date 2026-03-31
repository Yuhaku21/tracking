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
                <h2 id="username">Buat Catatan</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catatan</li>
                </ol>
            </nav>
            <!--Main Content-->
            <!-- FORM TAMBAH -->
            <form method="POST" class="mb-4">

                <div class="mb-2">
                    <input type="text" name="judul" class="form-control" placeholder="Judul catatan" required>
                </div>

                <div class="mb-2">
                    <textarea name="isi" class="form-control" placeholder="Isi catatan..." required rows="10"></textarea>
                </div>

                <button name="tambah" class="btn btn-primary">Simpan Catatan</button>

            </form>
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