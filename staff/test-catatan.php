<?php
require '../middleware/auth_staff.php';
require '../config/koneksi.php';

$user_id = $_SESSION['user_id']; // id user login

// TAMBAH CATATAN
if(isset($_POST['tambah'])){

    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $nama = $_SESSION['nama']; // nama pembuat catatan

    $stmt = $pdo->prepare("INSERT INTO catatan (user_id, dibuat_oleh, judul, isi) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $nama, $judul, $isi]);

    header("Location: test-catatan.php");
    exit;
}

// HAPUS CATATAN
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $stmt = $pdo->prepare("DELETE FROM catatan WHERE id=? AND user_id=?");
    $stmt->execute([$id,$user_id]);

    header("Location: test-catatan.php");
    exit;
}

// AMBIL DATA CATATAN USER
$stmt = $pdo->prepare("SELECT * FROM catatan WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Catatan Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

    <h3>Catatan Saya</h3>

    <!-- FORM TAMBAH -->
    <form method="POST" class="mb-4">

        <div class="mb-2">
            <input type="text" name="judul" class="form-control" placeholder="Judul catatan" required>
        </div>

        <div class="mb-2">
            <textarea name="isi" class="form-control" placeholder="Isi catatan..." required></textarea>
        </div>

        <button name="tambah" class="btn btn-primary">Tambah Catatan</button>

    </form>


    <!-- TAMPIL DATA -->
    <table class="table table-bordered">

        <tr>
            <th>Judul</th>
            <th>Isi</th>
            <th>Waktu</th>
            <th>Aksi</th>
        </tr>

        <?php foreach ($data as $row) { ?>

            <tr>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['isi']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>

                    <a href="edit_catatan.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus catatan ini?')">
                        Hapus
                    </a>

                </td>
            </tr>

        <?php } ?>

    </table>

</body>

</html>