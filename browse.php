<?php
session_start();
$email = $_SESSION['email'] ?? null;

// koneksi DB
require_once __DIR__ . "/config/config.php";

// Ambil semua dokter langsung dari database
$dokter_list = [];

if ($conn) {
    $sql = "SELECT nama_dokter, spesialis, hari_praktik, jam_mulai
            FROM dokter
            ORDER BY hari_praktik ASC, jam_mulai ASC";

    if ($res = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $dokter_list[] = $row;
        }
        mysqli_free_result($res);
    }
} else {
    die("Koneksi database gagal.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Dokter</title>
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body>

<div class="container">
    <h2>Jadwal Dokter</h2>

    <div class="btn-container">
        <form action="dashboard.php" style="margin-bottom:12px;">
            <button class="btn btn-grey">← Kembali ke Dashboard</button>
        </form>
    </div>

    <table class="table_dashboard">
        <thead>
            <tr>
                <th>Nama Dokter</th>
                <th>Spesialis</th>
                <th>Hari Praktik</th>
                <th>Jam Mulai</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($dokter_list)): ?>
                <?php foreach ($dokter_list as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama_dokter']); ?></td>
                        <td><?= htmlspecialchars($d['spesialis']); ?></td>
                        <td><?= htmlspecialchars($d['hari_praktik']); ?></td>
                        <td><?= htmlspecialchars($d['jam_mulai']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada data dokter.</td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>

</div>
</body>
</html>
