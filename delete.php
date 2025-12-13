<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once __DIR__ . "/config/config.php";

$id = (int)$_GET['id'];

// karena tombol delete hanya muncul pada admin, 
// maka TIDAK PERLU CEK ROLE

$stmt = $conn->prepare("DELETE FROM pasien WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// selesai → kembali ke dashboard
header("Location: dashboard.php");
exit();
