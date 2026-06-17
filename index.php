<?php 
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Menghitung jumlah modul per kelas
$count10 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='10' AND kategori != 'Software Utility'"));
$count11 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='11' AND kategori != 'Software Utility'"));
$count12 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='12' AND kategori != 'Software Utility'"));
$count_tools = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kategori='Software Utility'"));
?>

<div class="main-content">
    <div class="header-main">
        <h1>E-Learning SMK Jaya Buana</h1>
        <p>Pusat repositori bahan mengajar, jobsheet praktikum, dan utility laboratorium TKJ.</p>
    </div>

    <!-- AREA MATERI HARI INI (MOBILE OPTIMIZED HERO) -->
    <?php
    $pin_query = mysqli_query($conn, "SELECT * FROM modul_tkj WHERE is_pinned = 1 ORDER BY tgl_upload DESC LIMIT 1");
    if(mysqli_num_rows($pin_query) > 0):
        $pinned = mysqli_fetch_array($pin_query);
    ?>
        <div class="pinned-container">
            <span class="pinned-badge">📌 JADWAL PRAKTIKUM HARI INI</span>
            <h2><?= $pinned['judul']; ?></h2>
            <p>Materi Kompetensi Keahlian <strong><?= $pinned['kategori']; ?></strong> untuk <strong>Kelas <?= $pinned['kelas']; ?> TKJ</strong>.</p>
            <div style="display: flex; gap: 10px;">
                <?php if(!empty($pinned['nama_file'])): ?>
                    <a href="uploads/<?= $pinned['nama_file']; ?>" class="btn btn-success" target="_blank" style="width: 100%;">📥 Unduh Jobsheet</a>
                <?php endif; ?>
                <?php if(!empty($pinned['link_eksternal'])): ?>
                    <a href="<?= $pinned['link_eksternal']; ?>" class="btn btn-success" target="_blank" style="width: 100%; background-color: #047857;">🌐 Buka Link</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pilihan Menu Ruang Kelas -->
    <div class="grid-kelas">
        <div class="card-kelas">
            <div>
                <h3>Kelas 10 TKJ</h3>
                <p><?= $count10; ?> Materi Aktif</p>
            </div>
            <br>
            <a href="modul.php?kelas=10" class="btn">Buka Kelas</a>
        </div>
        <div class="card-kelas">
            <div>
                <h3>Kelas 11 TKJ</h3>
                <p><?= $count11; ?> Materi Aktif</p>
            </div>
            <br>
            <a href="modul.php?kelas=11" class="btn">Buka Kelas</a>
        </div>
        <div class="card-kelas">
            <div>
                <h3>Kelas 12 TKJ</h3>
                <p><?= $count12; ?> Materi Aktif</p>
            </div>
            <br>
            <a href="modul.php?kelas=12" class="btn">Buka Kelas</a>
        </div>
        <div class="card-kelas" style="border-top: 3px solid var(--success-color);">
            <div>
                <h3>🛠️ Tools Lab TKJ</h3>
                <p><?= $count_tools; ?> Software Siap Pakai</p>
            </div>
            <br>
            <a href="modul.php?kategori=tools" class="btn btn-success">Buka Repositori</a>
        </div>
    </div>

    <!-- Rilisan Berkas Terbaru versi Card List -->
    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 10px;">Materi Update Terbaru</h2>
    <div class="mobile-card-list">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM modul_tkj ORDER BY tgl_upload DESC LIMIT 4");
        if(mysqli_num_rows($query) == 0){
            echo "<p style='color: var(--text-muted); font-size:14px;'>Belum ada rilisan materi terbaru.</p>";
        }
        while($data = mysqli_fetch_array($query)) {
            ?>
            <div class="data-card">
                <div class="card-meta">
                    <span class="badge">Kelas <?= $data['kelas']; ?></span>
                    <span class="badge badge-type"><?= $data['tipe_berkas']; ?></span>
                </div>
                <div class="card-title"><?= $data['judul']; ?></div>
                <div class="card-info-row">
                    <span>🛠️ <?= $data['kategori']; ?></span>
                </div>
                <div class="card-actions">
                    <?php if(!empty($data['nama_file'])): ?>
                        <a href="uploads/<?= $data['nama_file']; ?>" class="btn" target="_blank">Unduh</a>
                    <?php endif; ?>
                    <?php if(!empty($data['link_eksternal'])): ?>
                        <a href="<?= $data['link_eksternal']; ?>" class="btn btn-success" target="_blank">Buka Link</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

</body>
</html>