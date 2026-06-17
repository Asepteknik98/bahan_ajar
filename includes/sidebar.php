<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$is_guru = (isset($_SESSION['username']) && $_SESSION['role'] === 'guru');
$nama = $is_guru ? $_SESSION['nama_lengkap'] : 'Siswa / Pengunjung';
?>
<div class="sidebar">
    <div>
        <h3>SMK JAYA BUANA</h3>
        <p style="color: var(--text-muted); font-size: 12px; text-align: center; margin-bottom: 15px;">
            👤 <?= $nama; ?> <?php if($is_guru) echo "(<span style='color: var(--accent-color);'>GURU</span>)"; ?>
        </p>
    </div>
    <div class="sidebar-menu">
        <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">💻 Dashboard</a>
        <a href="modul.php" class="<?= $current_page == 'modul.php' ? 'active' : '' ?>">📚 Semua Materi</a>
        
        <!-- Menu bersyarat: Hanya muncul jika yang login adalah akun Guru resmi -->
        <?php if ($is_guru) : ?>
            <a href="upload_modul.php" class="<?= $current_page == 'upload_modul.php' ? 'active' : '' ?>">📤 Upload Bahan</a>
            <a href="logout.php" style="background-color: #ef4444; color: white; text-align: center;">🚪 Keluar</a>
        <?php else: ?>
            <a href="login.php" style="background-color: var(--accent-color); color: white; text-align: center;">🔑 Login Guru</a>
        <?php endif; ?>
    </div>
</div>