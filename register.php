<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style/auth.css">
    <link rel="stylesheet" href="style/common.css">
</head>
<body>

<div class="content-section">

    <h2>Buat Akun Baru</h2>

    <form action="process_register.php" method="POST">

        <label>Email</label>
        <input class="email" type="email" name="email" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-blue">Register</button>
    </form>

    <form action="index.php">
        <button type="submit" class="btn-grey">Kembali ke Login</button>
    </form>

</div>

</body>
</html>

