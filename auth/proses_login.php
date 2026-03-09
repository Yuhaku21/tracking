<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../config/koneksi.php';

$nama = $_POST['nama'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE nama = ? LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nama]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan");
}

if (!password_verify($password, $user['password'])) {
    die("Password salah");
}

$_SESSION['nama'] = $user['nama'];
$_SESSION['role'] = $user['role'];

if ($user['role'] == 'admin') {
    header("Location: ../admin/dashboard-admin.php");
} else {
    header("Location: ../index.php");
}

exit;
