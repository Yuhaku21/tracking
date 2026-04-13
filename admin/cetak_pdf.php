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
");

$stmt->execute();
$dataProgress = $stmt->fetchAll();
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Data</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 180px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 22px;
        }

        .header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: normal;
        }

        /* INFO ATAS */
        .info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background: #f2f2f2;
        }

        th {
            padding: 10px;
            border-bottom: 2px solid #000;
            text-align: center;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* STATUS */
        .status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .selesai {
            background: #198754;
            color: white;
        }

        .pending {
            background: #ffc107;
            color: black;
        }

        img {
            max-width: 80px;
            height: auto;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body onload="window.print()">

    <!-- INFO ATAS -->
    <div class="info">
        <div><?= date('d/m/Y H:i') ?></div>
        <div><b>Cetak Data</b></div>
    </div>

    <!-- HEADER -->
    <div class="header">
        <img src="../asset/logo border damara.jpg">
        <h2>Data History AO</h2>
        <h3>PT. BANK DANA MASTER LOTARA</h3>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
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
                        <td><?= htmlspecialchars($row['nama_user']) ?></td>
                        <td><?= htmlspecialchars($row['nama_nasabah']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <span class="status <?= $row['status'] == 'selesai' ? 'selesai' : 'pending' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>

                        <!-- 🔥 TAMPILKAN GAMBAR -->
                        <td>
                            <?php if (!empty($row['foto'])): ?>
                                <img src="../assets/<?= htmlspecialchars($row['foto']) ?>"
                                    width="80"
                                    style="border-radius:8px;">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>



    <!-- FOOTER -->
    <div class="footer">
        <div>localhost/tracking/admin/cetak_pdf.php</div>
        <div>1/1</div>
    </div>

</body>


</html>