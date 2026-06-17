<?php 
session_start();
include 'config/database.php';

// ==========================================================================
// BACKEND SYSTEM: PENANGANAN AJAX UNTUK KALENDER (SAVE & FETCH DATA)
// ==========================================================================

// 1. Logika MENYIMPAN Catatan Baru dari Klik Kalender Guru
if (isset($_POST['action']) && $_POST['action'] == 'add_event') {
    // Pastikan hanya user dengan session guru yang bisa mengeksekusi insert database
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $start = mysqli_real_escape_string($conn, $_POST['start']);
        $end = mysqli_real_escape_string($conn, $_POST['end']);
        $color = mysqli_real_escape_string($conn, $_POST['color']);

        $query = "INSERT INTO agenda_tkj (title, start_date, end_date, bg_color) VALUES ('$title', '$start', '$end', '$color')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'unauthorized']);
    }
    exit;
}

// 2. Logika MENGAMBIL Data Agenda untuk Ditampilkan di Kalender (Siswa & Guru)
if (isset($_GET['action']) && $_GET['action'] == 'get_events') {
    $result = mysqli_query($conn, "SELECT id, title, start_date AS start, end_date AS end, bg_color AS backgroundColor FROM agenda_tkj");
    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
    echo json_encode($events);
    exit;
}

// ==========================================================================
// LAYOUT & LOGIKA HITUNG DATA SEPERTI SEMULA
// ==========================================================================
include 'includes/header.php';
include 'includes/sidebar.php';

// Menghitung jumlah modul per kelas
$count10 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='10' AND kategori != 'Software Utility'"));
$count11 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='11' AND kategori != 'Software Utility'"));
$count12 = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kelas='12' AND kategori != 'Software Utility'"));
$count_tools = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM modul_tkj WHERE kategori='Software Utility'"));

// Mengunci deteksi session guru (Pastikan $_SESSION['role'] sesuai dengan sistem login Anda)
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') ? true : false;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<style>
    /* Wrapper untuk memperkecil ukuran kalender agar tidak dominan dan mengacaukan layout */
    .calendar-wrapper {
        max-width: 600px; /* Batasi lebar maksimal kalender */
        margin: 0 auto;   /* Posisikan di tengah halaman */
    }
    
    .fc { 
        background-color: var(--card-color); 
        padding: 12px; 
        border-radius: 14px; 
        box-shadow: var(--card-shadow); 
        color: #ffffff; 
        font-size: 11px; /* Teks dalam kalender diperkecil agar rapi */
    }
    
    /* Memperkecil tinggi kotak tanggal di HP / Laptop */
    .fc .fc-daygrid-day-frame {
        min-height: 45px !important;
    }
    
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid rgba(255, 255, 255, 0.04); }
    .fc .fc-toolbar-title { font-size: 13px; font-weight: 700; color: var(--primary-color); }
    
    /* Memperkecil tombol navigasi bulan */
    .fc .fc-button-primary { 
        background-color: rgba(255, 255, 255, 0.03); 
        border: none; 
        color: #ffffff; 
        font-weight: 600; 
        font-size: 11px; 
        padding: 4px 8px; 
    }
    .fc .fc-button-primary:hover { background-color: var(--primary-color); color: #0f172a; }
    .fc .fc-button-primary:disabled { background-color: rgba(255, 255, 255, 0.01); color: #64748b; }
    .fc .fc-daygrid-day.fc-day-today { background-color: rgba(6, 182, 212, 0.1) !important; }
    
    /* Desain baris teks catatan agenda di dalam kotak kalender */
    .fc-event { 
        border: none; 
        padding: 2px 4px; 
        border-radius: 4px; 
        font-size: 9px; 
        font-weight: 600; 
        color: #0f172a !important; /* Teks berwarna gelap agar kontras dengan background cyan */
    }
    
    /* Berikan efek pointer hover khusus untuk Guru */
    <?php if($is_guru): ?>
    .fc-daygrid-day { cursor: pointer; }
    .fc-daygrid-day:hover { background-color: rgba(255, 255, 255, 0.03); }
    <?php endif; ?>
</style>

<div class="main-content">
    <div class="header-main">
        <h1>E-Learning SMK Jaya Buana</h1>
        <p>Pusat repositori bahan mengajar, jobsheet praktikum, dan utility laboratorium TKJ.</p>
    </div>

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

    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 15px;">Materi Update Terbaru</h2>
    <div class="mobile-card-list" style="margin-bottom: 40px;">
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

    <div id="kalender-section" style="margin-top: 25px; margin-bottom: 25px; text-align: center;">
        <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #ffffff; display: inline-flex; align-items: center; gap: 8px;">
            📅 Agenda & Kalender Kegiatan
        </h2>
        <?php if($is_guru): ?>
            <p style="font-size: 11px; color: var(--primary-color); margin-top: -8px; margin-bottom: 12px;">Mode Guru Aktif: Klik pada kotak tanggal untuk menyimpan catatan langsung ke database.</p>
        <?php endif; ?>
        
        <div class="calendar-wrapper">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
            locale: 'id', 
            height: 'auto',
            // Load otomatis data dari database lewat parameter URL Ajax GET
            events: 'index.php?action=get_events',
            
            selectable: <?= $is_guru ? 'true' : 'false'; ?>, 
            select: function(info) {
                // Berjalan hanya jika session user terdeteksi sebagai Guru
                var title = prompt('Simpan Catatan Agenda/Praktikum untuk Tanggal ' + info.startStr + ':');
                if (title) {
                    // Skema AJAX untuk melempar data inputan ke database MySQL secara background
                    var formData = new FormData();
                    formData.append('action', 'add_event');
                    formData.append('title', title);
                    formData.append('start', info.startStr);
                    formData.append('end', info.endStr);
                    formData.append('color', '#06b6d4'); // Warna tema utama (Cyber Cyan) untuk penanda baru

                    fetch('index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            calendar.refetchEvents(); // Segarkan render kalender agar data baru langsung muncul
                            alert('Catatan sukses disimpan ke database!');
                        } else {
                            alert('Gagal menyimpan catatan.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan koneksi server.');
                    });
                }
                calendar.unselect();
            }
        });
        calendar.render();
    });
</script>

</body>
</html>