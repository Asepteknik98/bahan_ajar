<?php 
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$is_guru = (isset($_SESSION['username']) && $_SESSION['role'] === 'guru');
?>

<div class="main-content">
    <div class="header-main">
        <h1>Materi & Administrasi</h1>
        <p>Gunakan kolom pencarian instan jika ingin mencari topik secara spesifik.</p>
    </div>

    <!-- Filter & Pencarian Dinamis -->
    <div class="filter-wrapper">
        <input type="text" id="searchInput" class="search-control" placeholder="🔍 Ketik kata kunci materi...">
        
        <div class="filter-buttons">
            <a href="modul.php" class="btn <?= ($filter_kelas == '' && $filter_kategori == '') ? 'btn-filter-active' : '' ?>" style="background-color: var(--card-color); color: white;">Semua</a>
            <a href="modul.php?kelas=10" class="btn <?= $filter_kelas == '10' ? 'btn-filter-active' : '' ?>" style="background-color: var(--card-color); color: white;">Kelas 10</a>
            <a href="modul.php?kelas=11" class="btn <?= $filter_kelas == '11' ? 'btn-filter-active' : '' ?>" style="background-color: var(--card-color); color: white;">Kelas 11</a>
            <a href="modul.php?kelas=12" class="btn <?= $filter_kelas == '12' ? 'btn-filter-active' : '' ?>" style="background-color: var(--card-color); color: white;">Kelas 12</a>
            <a href="modul.php?kategori=tools" class="btn <?= $filter_kategori == 'tools' ? 'btn-filter-active' : '' ?>" style="background-color: var(--card-color); color: white;">🛠️ Tools</a>
        </div>
    </div>

    <!-- Tampilan Berbasis Struktur UI Card Android -->
    <div class="mobile-card-list" id="moduleCardContainer">
        <?php
        if ($filter_kategori == 'tools') {
            $sql = "SELECT * FROM modul_tkj WHERE kategori = 'Software Utility' ORDER BY id DESC";
        } elseif ($filter_kelas != '') {
            $sql = "SELECT * FROM modul_tkj WHERE kelas = '$filter_kelas' AND kategori != 'Software Utility' ORDER BY id DESC";
        } else {
            $sql = "SELECT * FROM modul_tkj ORDER BY id DESC";
        }
        
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) == 0){
            echo "<p id='noDataMsg' style='color: var(--text-muted); font-size:14px; text-align:center; width:100%; padding:20px;'>Belum ada data materi yang tersedia di sini.</p>";
        }
        
        while($row = mysqli_fetch_array($query)) {
            ?>
            <div class="data-card searchable-card">
                <div class="card-meta">
                    <span class="badge">Kelas <?= $row['kelas']; ?></span>
                    <span class="badge badge-type"><?= $row['tipe_berkas']; ?></span>
                </div>
                
                <div class="card-title">
                    <?php if($row['is_pinned'] == 1): ?>
                        <span style="color: var(--success-color); font-size:13px; margin-right:5px;">📌</span>
                    <?php endif; ?>
                    <?= $row['judul']; ?>
                </div>
                
                <div class="card-info-row">
                    <span>📂 Kategori: <strong><?= $row['kategori']; ?></strong></span>
                    <span>📆 <?= date('d M Y', strtotime($row['tgl_upload'])); ?></span>
                </div>
                
                <div class="card-actions">
                    <?php if(!empty($row['nama_file'])): ?>
                        <a href="uploads/<?= $row['nama_file']; ?>" class="btn" target="_blank">Download</a>
                    <?php endif; ?>

                    <?php if(!empty($row['link_eksternal'])): ?>
                        <a href="<?= $row['link_eksternal']; ?>" class="btn btn-success" target="_blank">Buka Link</a>
                    <?php endif; ?>

                    <!-- Opsi Tambahan Khusus untuk Manajemen Sisi Guru -->
                    <?php if($is_guru): ?>
                        <?php if($row['is_pinned'] == 0): ?>
                            <a href="pin_proses.php?id=<?= $row['id']; ?>&action=pin" class="btn" style="background-color: var(--warning-color); max-width: fit-content;">Pin</a>
                        <?php else: ?>
                            <a href="pin_proses.php?id=<?= $row['id']; ?>&action=unpin" class="btn" style="background-color: #6b7280; max-width: fit-content;">Unpin</a>
                        <?php endif; ?>
                        <a href="hapus_modul.php?id=<?= $row['id']; ?>" class="btn btn-danger" style="max-width: fit-content;" onclick="return confirm('Hapus berkas ini?')">🗑️</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- LIVE SEARCH JAVASCRIPT GAYA ANDROID (SANGAT RINGAN) -->
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('#moduleCardContainer .searchable-card');
    
    cards.forEach(function(card) {
        let text = card.textContent.toLowerCase();
        if(text.includes(filter)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

</body>
</html>