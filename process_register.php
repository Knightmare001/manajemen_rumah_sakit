<?php
include "config/config.php"; 

$email = $_POST['email'];
$username = $_POST['username'];
$password = md5($_POST['password']); 

$response = "";

// Cek apakah email sudah ada
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if (mysqli_num_rows($check) > 0) {
    $response = "email_used";
} else {
    // Insert user baru
    $query = "INSERT INTO users (email, nama, password) VALUES ('$email', '$username', '$password')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $response = "success";
    } else {
        $response = "failed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        body {
            font-family: Arial, sans-serif; 
        }
        .swal2-popup,
        .swal2-title,
        .swal2-html-container,
        .swal2-confirm {
            font-family: Arial, sans-serif !important;
            font-weight: normal !important;
        }
        .swal2-confirm {
            background-color: #4d64fc !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 8px 20px !important;
        }
        .swal2-confirm:hover {
            background-color: #3a4fd1 !important;
        }
    </style>
</head>
<body>

<script>
let status = "<?= $response ?>";

if (status === "success") {
    Swal.fire({
        title: "Registrasi Berhasil!",
        text: "Akun Anda berhasil dibuat.",
        icon: "success",
        confirmButtonText: "OK"
    }).then(() => {
        window.location.href = "index.php";
    });
} 
else if (status === "email_used") {
    Swal.fire({
        title: "Email Sudah Digunakan!",
        text: "Silakan gunakan email lain.",
        icon: "warning",
        confirmButtonText: "OK"
    }).then(() => {
        history.back();
    });
}
else {
    Swal.fire({
        title: "Registrasi Gagal!",
        text: "Terjadi kesalahan pada server.",
        icon: "error",
        confirmButtonText: "OK"
    }).then(() => {
        history.back();
    });
}
</script>

</body>
</html>