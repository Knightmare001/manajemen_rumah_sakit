<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style/auth.css">
    <link rel="stylesheet" href="style/common.css">
</head>

<body>

<div class="content-section">

    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <script>
        alert("Login gagal! Email atau password salah.");
    </script>
    <?php endif; ?>

    <h2>Login</h2>

    <form action="process_login.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-blue">Login</button>
    </form>

    <form action="register.php">
        <button type="submit" class="btn-grey">Register</button>
    </form>

    <a href="browse.php">Cek Jadwal Dokter</a>

</div>

</body>
</html>