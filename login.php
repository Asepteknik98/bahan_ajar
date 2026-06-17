<?php
include 'config/database.php';
session_start();

$pesan = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);
        $_SESSION['username']     = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['role']         = $data['role'];

        header("Location: index.php");
        exit();
    } else {
        $pesan = "<div style='color: #ef4444; margin-bottom:15px; text-align:center;'>Username atau Password salah!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bahan Ajar SMK Jaya Buana</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0f172a;
        }
        .login-card {
            background-color: #1e293b;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #3b82f6;
        }
        .login-card p {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>SMK JAYA BUANA</h2>
    <p>Sistem Repositori Bahan Ajar TKJ</p>
    
    <?= $pesan; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn btn-success" style="width: 100%; padding: 12px;">Masuk ke Sistem</button>
    </form>
</div>

</body>
</html>