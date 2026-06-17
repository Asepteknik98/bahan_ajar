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
        $pesan = "<div style='color: #ef4444; margin-bottom:15px; text-align:center; font-weight: 500;'>Username atau Password salah!</div>";
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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Ganti text di dalam url() dengan lokasi gambar Anda */
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('assets/fotojb.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 20px;
        }
        .login-card {
            background-color: rgba(30, 41, 59, 0.95); /* Menggunakan opacity agar sedikit transparan/glassmorphism */
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px); /* Memberikan efek blur halus pada background di belakang card */
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #3b82f6;
            font-size: 24px;
        }
        .login-card p {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
        }
        
        /* Pengaturan tambahan untuk memastikan form input terlihat rapi di HP */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #cbd5e1;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
            color: #f8fafc;
            font-size: 15px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }
        
        /* Media Query untuk memastikan kenyamanan di layar HP yang sangat kecil */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            .login-card h2 {
                font-size: 22px;
            }
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
        <button type="submit" name="login" class="btn btn-success" style="width: 100%; padding: 12px; cursor: pointer; font-weight: 600; border-radius: 6px;">Masuk ke Sistem</button>
    </form>
</div>

</body>
</html>