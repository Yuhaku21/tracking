<?php

require '../config/koneksi.php';

$nama = $_POST['nama'];
$kode_kantor = $_POST['kode_kantor'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (nama,kode_kantor,password,role) VALUES (?,?,?,'admin')";
$stmt = $pdo->prepare($sql);

$stmt->execute([$nama, $kode_kantor, $password]);

header("Location: login.php");
