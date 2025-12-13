<?php
// pasien_form.php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}
require_once __DIR__ . "/config/config.php";

$mode = 'create'; // default
$error = '';
$success = '';
$pasien = null;

// Ambil daftar dokter (jadwal) untuk client-side filter
$dokter_list = [];
if ($conn) {
    $sql = "SELECT id_dokter, nama_dokter, spesialis, hari_praktik, jam_mulai, jam_selesai
            FROM dokter
            ORDER BY nama_dokter ASC, hari_praktik ASC, jam_mulai ASC";
    if ($res = mysqli_query($conn, $sql)) {
        while ($r = mysqli_fetch_assoc($res)) $dokter_list[] = $r;
        mysqli_free_result($res);
    }
}

// Jika ada id di query -> ambil data pasien (edit mode)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $mode = 'edit';
    if ($conn) {
        $sql = "SELECT p.id, p.nama, p.deskripsi_keluhan, p.id_dokter, p.hari_praktik, p.jam_mulai, p.email,
                       d.nama_dokter
                FROM pasien p
                LEFT JOIN dokter d ON p.id_dokter = d.id_dokter
                WHERE p.id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $pasien = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmt);
        }
    }
    if (!$pasien) {
        // Jika id tidak ditemukan, kembali ke dashboard
        header("Location: dashboard.php");
        exit;
    }
}

// Handle POST (simpan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil semua field
    $post_id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi_keluhan'] ?? '');
    $hari = trim($_POST['hari_praktik'] ?? '');
    $jam = trim($_POST['jam_mulai'] ?? '');
    $id_dokter = isset($_POST['id_dokter']) && $_POST['id_dokter'] !== '' ? (int)$_POST['id_dokter'] : null;
    $email = trim($_POST['email'] ?? '');

    // validasi dasar
    if ($nama === '' || $deskripsi === '' || $hari === '' || $jam === '' || !$id_dokter) {
        $error = "Semua field wajib diisi (termasuk memilih dokter).";
    } else {
        // normalisasi jam HH:MM -> HH:MM:00
        if (preg_match('/^\d{1,2}:\d{2}$/', $jam)) $jam_db = $jam . ':00';
        else $jam_db = $jam;

        if (!$conn) {
            $error = "Koneksi database gagal.";
        } else {
            if ($post_id) {
                // ---- UPDATE ----
                $sql = "UPDATE pasien SET nama = ?, deskripsi_keluhan = ?, id_dokter = ?, hari_praktik = ?, jam_mulai = ?, email = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssisssi", $nama, $deskripsi, $id_dokter, $hari, $jam_db, $email, $post_id);
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Data pasien berhasil diperbarui.";
                        // ambil ulang pasien untuk refill form
                        mysqli_stmt_close($stmt);
                        $mode = 'edit';
                        $id = $post_id;
                        if ($sq = mysqli_prepare($conn, "SELECT * FROM pasien WHERE id = ?")) {
                            mysqli_stmt_bind_param($sq, "i", $id);
                            mysqli_stmt_execute($sq);
                            $resq = mysqli_stmt_get_result($sq);
                            $pasien = mysqli_fetch_assoc($resq);
                            mysqli_stmt_close($sq);
                        }
                    } else {
                        $error = "Gagal update: " . mysqli_stmt_error($stmt);
                        mysqli_stmt_close($stmt);
                    }
                } else {
                    $error = "Gagal menyiapkan query update: " . mysqli_error($conn);
                }
            } else {
                // ---- INSERT ----
                $sql = "INSERT INTO pasien (nama, deskripsi_keluhan, id_dokter, hari_praktik, jam_mulai, email)
                        VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssisss", $nama, $deskripsi, $id_dokter, $hari, $jam_db, $email);
                    if (mysqli_stmt_execute($stmt)) {
                        $insert_id = mysqli_insert_id($conn);
                        $success = "Pasien berhasil ditambahkan (ID: $insert_id).";
                        // redirect ke edit mode atau dashboard jika mau:
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error = "Gagal menyimpan: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Gagal menyiapkan query insert: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title><?= $mode === 'edit' ? 'Edit Pasien' : 'Daftar Berobat Baru' ?></title>
  <link rel="stylesheet" href="style/common.css ">
  <link rel="stylesheet" href="style/form.css">
  <style>
    .message { padding:10px; margin-bottom:12px; border-radius:6px; }
    .error { background:#ffe6e6; color:#8a2a2a; border:1px solid #f5c6c6; }
    .success { background:#e6ffef; color:#1f7a3d; border:1px solid #b6f0c7; }
  </style>
</head>
<body>
<div class="container">
  <form action="dashboard.php" style="margin-bottom:12px;">
    <button class="btn btn-grey">← Kembali ke Dashboard</button>
  </form>

  <h2><?= $mode === 'edit' ? 'Edit Pasien' : 'Daftar Berobat Baru' ?></h2>

  <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="message success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form id="pasienForm" method="POST" action="">
    <input type="hidden" name="id" value="<?= $pasien['id'] ?? '' ?>">

    <div class="form-group">
      <label>Nama:</label>
      <input name="nama" required value="<?= htmlspecialchars($pasien['nama'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Keluhan:</label>
      <input name="deskripsi_keluhan" required value="<?= htmlspecialchars($pasien['deskripsi_keluhan'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Pilih hari:</label>
      <select id="hariPraktik" name="hari_praktik" required>
        <option value="">-- Pilih hari --</option>
        <?php
          $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
          $cur = $pasien['hari_praktik'] ?? '';
          foreach($days as $d) {
            $s = strcasecmp($d, $cur) === 0 ? 'selected' : '';
            echo "<option $s>" . htmlspecialchars($d) . "</option>";
          }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label>Pilih Jam:</label>
      <select id="jamSelect" name="jam_mulai" required>
        <!-- option generated by JS -->
      </select>
    </div>

    <div class="form-group">
      <label>Pilih Dokter:</label>
      <select id="dokterSelect" name="id_dokter" required>
        <option value="">--Pilih hari & Jam Terlebih Dahulu--</option>
      </select>
    </div>

    <div class="form-group">
      <label>Email (opsional):</label>
      <input name="email" type="email" value="<?= htmlspecialchars($pasien['email'] ?? '') ?>">
    </div>

    <button type="submit" class="btn-primary"><?= $mode === 'edit' ? 'Update' : 'Simpan' ?></button>
  </form>
</div>

<script>
  // doctorData & pasienData disuntik dari server agar tidak pakai API
  const doctorData = <?= json_encode($dokter_list, JSON_UNESCAPED_UNICODE) ?>;
  const pasienData = <?= json_encode($pasien ?? new stdClass(), JSON_UNESCAPED_UNICODE) ?>;

  // generate time options (08:00 - 17:00)
  function pad2(n){ return String(n).padStart(2,'0'); }
  function generateTimeOptions() {
    const sel = document.getElementById('jamSelect');
    sel.innerHTML = '<option value="">--Pilih Jam--</option>';
    for (let h = 8; h <= 17; h++) {
      const v = pad2(h) + ':00';
      const opt = document.createElement('option');
      opt.value = v; opt.textContent = v;
      sel.appendChild(opt);
    }
  }

  // filter dokter berdasarkan hari + jam (client-side)
  function populateDokterSelect(hari, time, selectedId = null) {
    const sel = document.getElementById('dokterSelect');
    if (!hari || !time) {
      sel.innerHTML = '<option value="">--Pilih hari & Jam Terlebih Dahulu--</option>'; return;
    }
    const filtered = doctorData.filter(d => {
      if (!d.hari_praktik || !d.jam_mulai) return false;
      const jam = d.jam_mulai.length >=5 ? d.jam_mulai.slice(0,5) : d.jam_mulai;
      return String(d.hari_praktik).toLowerCase() === String(hari).toLowerCase() && jam === time;
    });
    if (!filtered.length) {
      sel.innerHTML = '<option value="">Tidak ada dokter pada hari/jam ini</option>'; return;
    }
    sel.innerHTML = '<option value="">--Pilih Dokter--</option>';
    filtered.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d.id_dokter;
      opt.textContent = d.nama_dokter + (d.spesialis ? ' ('+d.spesialis+')' : '');
      sel.appendChild(opt);
    });
    if (selectedId) sel.value = selectedId;
  }

  document.addEventListener('DOMContentLoaded', () => {
    generateTimeOptions();

    // set jam from pasienData if edit
    const jam_db = pasienData.jam_mulai || '';
    const jam_short = jam_db.length >=5 ? jam_db.slice(0,5) : jam_db;
    if (jam_short) document.getElementById('jamSelect').value = jam_short;

    // populate dokter based on pasien values (if any)
    const hari = pasienData.hari_praktik || document.getElementById('hariPraktik').value;
    const jam = jam_short || document.getElementById('jamSelect').value;
    populateDokterSelect(hari, jam, pasienData.id_dokter || null);

    // event listeners to refresh doctors when user changes hari/jam
    document.getElementById('hariPraktik').addEventListener('change', (e) => {
      const h = e.target.value; const t = document.getElementById('jamSelect').value;
      populateDokterSelect(h, t, null);
    });
    document.getElementById('jamSelect').addEventListener('change', (e) => {
      const t = e.target.value; const h = document.getElementById('hariPraktik').value;
      populateDokterSelect(h, t, null);
    });
  });
</script>
</body>
</html>
