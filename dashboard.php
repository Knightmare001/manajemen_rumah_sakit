<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/config/config.php"; // sesuaikan path jika perlu

$doctor_name = $_SESSION["nama"] ?? '';

$response_error = null;
$pasien_list = [];
$dokter_list = [];

if (!$conn) {
    $response_error = "Koneksi database gagal.";
} else {
    // Ambil semua pasien (LEFT JOIN dokter untuk nama dokter)
    $sql_pasien = "SELECT p.id, p.nama, p.deskripsi_keluhan, p.id_dokter, p.hari_praktik, p.jam_mulai, p.email,
                          d.nama_dokter
                   FROM pasien p
                   LEFT JOIN dokter d ON p.id_dokter = d.id_dokter
                   ORDER BY p.id ASC";
    if ($res = mysqli_query($conn, $sql_pasien)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $pasien_list[] = $row;
        }
        mysqli_free_result($res);
    } else {
        $response_error = "Gagal mengambil pasien: " . mysqli_error($conn);
    }

    // Ambil semua data dokter (jadwal)
    $sql_dokter = "SELECT id_dokter, nama_dokter, spesialis, hari_praktik, jam_mulai, jam_selesai
                   FROM dokter
                   ORDER BY nama_dokter ASC";
    if ($res2 = mysqli_query($conn, $sql_dokter)) {
        while ($row = mysqli_fetch_assoc($res2)) {
            $dokter_list[] = $row;
        }
        mysqli_free_result($res2);
    } else {
        // jangan overwrite pasien error jika sudah ada
        if (!$response_error) $response_error = "Gagal mengambil dokter: " . mysqli_error($conn);
    }
}

// Tutup koneksi jika ingin
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/common.css">
    <script>
        function confirmDelete(url) {
            if (confirm("Yakin ingin menghapus data ini?")) {
                window.location.href = url;
            }
            return false;
        }
        function switchTab(tabName, ev) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            const tabButtons = document.querySelectorAll('.tab');
            tabButtons.forEach(btn => btn.classList.remove('active'));

            document.getElementById('tab-' + tabName).classList.add('active');
            if (ev && ev.currentTarget) ev.currentTarget.classList.add('active');
        }
    </script>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
        <div>
            <button onclick="window.location.href='pasien_form.php'" class="btn btn-primary">Tambah Pasien</button>
        </div>

        <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-weight:600;">👤 <?= htmlspecialchars($doctor_name); ?></span>
            <form action="logout.php" method="POST" style="margin:0;">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    <?php if ($response_error): ?>
        <div style="margin-bottom:12px; color:#a94442; background:#f2dede; padding:10px; border-radius:4px;">
            <?= htmlspecialchars($response_error); ?>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab active" onclick="switchTab('pasien', event)">Kelola Pasien</button>
        <button class="tab" onclick="switchTab('jadwal', event)">Kelola Jadwal Dokter</button>
    </div>

    <div id="tab-pasien" class="tab-content active">
        <table class="table_dashboard">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Keluhan</th>
                    <th>Dokter</th>
                    <th>Hari Praktik</th>
                    <th>Jam Mulai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pasien_list)): ?>
                    <?php foreach ($pasien_list as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']); ?></td>
                            <td><?= htmlspecialchars($p['nama']); ?></td>
                            <td><?= htmlspecialchars($p['deskripsi_keluhan']); ?></td>
                            <td><?= htmlspecialchars($p['nama_dokter']); ?></td>
                            <td><?= htmlspecialchars($p['hari_praktik']); ?></td>
                            <td><?= htmlspecialchars($p['jam_mulai']); ?></td>
                            <td>
                                <button class="btn btn-warning">
                                    <a href="pasien_form.php?id=<?= urlencode($p['id']); ?>" style="color:inherit; text-decoration:none;">Edit</a>
                                </button>
                                <button class="btn btn-danger">
                                    <a href="delete.php?id=<?= urlencode($p['id']); ?>" style="color:inherit; text-decoration:none;" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Tidak ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="tab-jadwal" class="tab-content">
        <h2>Jadwal Dokter</h2>
        <table class="table_dashboard">
            <thead>
                <tr>
                    <th>Nama Dokter</th>
                    <th>Spesialis</th>
                    <th>Tanggal Praktik</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
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
                            <td><?= htmlspecialchars($d['jam_selesai']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Tidak ada data dokter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

