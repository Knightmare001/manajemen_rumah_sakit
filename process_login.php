<?php
session_start();
include "config/config.php"; 

$email = $_POST['email'];
$password = md5($_POST['password']);

// Query login
$query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    // Simpan ke session
    $_SESSION['nama'] = $user['nama'];    // username / nama user
    $_SESSION['email'] = $user['email'];   // email
    $_SESSION['role'] = $user['role'];  //role
    $_SESSION['user_id'] = $user['id'];

    header("Location: dashboard.php");
    exit();
} else {
    header("Location: index.php?error=1");
    exit();
}
?>
