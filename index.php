<?php 
session_start();
include 'config/database.php';

// ==========================================================================
// BACKEND SYSTEM: PENANGANAN AJAX UNTUK KALENDER (SAVE & FETCH DATA)
// ==========================================================================

// 1. Logika MENYIMPAN Catatan Baru dari Klik Kalender Guru
if (isset($_POST['action']) && $_POST['action'] == 'add_event') {
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

// 2. Logika MENGAMBIL Data Agenda untuk Ditampilkan di Kalender
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
$total_materi = $count10 + $count11 + $count12 + $count_tools;

// Mengunci deteksi session guru
$is_guru = (isset($_SESSION['role']) && $_SESSION['role'] == 'guru') ? true : false;
?>

<!-- Pustaka FullCalendar CSS & Google Fonts untuk UI Modern -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<style>
    /* Global Content Tuning */
    .main-content {
        padding: 25px;
        font-family: 'Inter', sans-serif;
    }

    /* Modernized Hero Header */
    .header-main {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px -15px rgba(0,0,0,0.5);
    }
    .header-main h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        background: linear-gradient(to right, #ffffff, #06b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
    }
    .header-main p {
        color: #94a3b8;
        font-size: 14px;
        max-width: 600px;
        line-height: 1.5;
    }

    /* Pinned Post Modern Card */
    .pinned-container {
        background: rgba(6, 182, 212, 0.06);
        border: 1px solid rgba(6, 182, 212, 0.2);
        padding: 20px;
        border-radius: 14px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .pinned-badge {
        background: #06b6d4;
        color: #0f172a;
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 30px;
        letter-spacing: 0.5px;
    }
    .pinned-container h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 12px 0 6px 0;
        color: #ffffff;
    }
    .pinned-container p {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 15px;
    }

    /* Grid Ruang Kelas Elevate Effect */
    .grid-kelas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }
    .card-kelas {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.03);
        padding: 22px;
        border-radius: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
    }
    .card-kelas:hover {
        transform: translateY(-4px);
        border-color: rgba(6, 182, 212, 0.3);
        box-shadow: 0 12px 20px -8px rgba(6, 182, 212, 0.15);
    }
    .card-kelas h3 {
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 5px;
    }
    .card-kelas p {
        font-size: 12px;
        color: #64748b;
    }
    .card-kelas .btn {
        width: 100%;
        text-align: center;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        margin-top: 15px;
    }

    /* Section Title */
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 16px;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Update Terbaru Card Grid */
    .mobile-card-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-bottom: 35px;
    }
    .data-card {
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .card-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .badge {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
    }
    .badge-type {
        background: rgba(14, 165, 233, 0.15);
        color: #38bdf8;
    }
    .card-title {
        font-size: 13px;
        font-weight: 600;
        color: #f1f5f9;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    .card-info-row {
        font-size: 11px;
        color: #64748b;
        margin-bottom: 14px;
    }
    .card-actions {
        display: flex;
        gap: 8px;
    }
    .card-actions .btn {
        flex: 1;
        text-align: center;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 6px;
    }

    /* Bottom 3-Column Layout */
    .bottom-grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        align-items: stretch;
    }
    .widget-box {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.02);
        padding: 20px;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .widget-box h3 {
        font-size: 13px;
        font-weight: 700;
        color: #06b6d4;
        margin-bottom: 15px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .status-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }
    .status-item:last-of-type { border: none; }

    /* Clean Minimalist Calendar Area */
    .calendar-wrapper {
        background: #1e293b;
        padding: 16px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.02);
    }
    .fc { color: #ffffff; font-size: 10.5px; }
    .fc .fc-daygrid-day-frame { min-height: 40px !important; }
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid rgba(255, 255, 255, 0.03); }
    .fc .fc-toolbar-title { font-size: 12px; font-weight: 700; color: #ffffff; }
    .fc .fc-button-primary { 
        background-color: rgba(255, 255, 255, 0.04); 
        border: none; 
        color: #cbd5e1; 
        font-size: 10px; 
        padding: 3px 6px; 
    }
    .fc .fc-button-primary:hover { background-color: #06b6d4; color: #0f172a; }
    .fc .fc-daygrid-day.fc-day-today { background-color: rgba(6, 182, 212, 0.08) !important; }
    .fc-event { border: none; padding: 2px; border-radius: 3px; font-size: 8.5px; font-weight: 600; color: #0f172a !important; }
    
    <?php if($is_guru): ?>
    .fc-daygrid-day { cursor: pointer; }
    .fc-daygrid-day:hover { background-color: rgba(6, 182, 212, 0.04); }
    <?php endif; ?>

    /* Desktop Adjustments (3 Kolom Sejajar Sempurna) */
    @media (min-width: 992px) {
        .bottom-grid-container {
            grid-template-columns: 280px 1fr 280px;
        }
    }
</style>

<div class="main-content">
    
    <!-- HEADER HERO -->
    <div class="header-main">
        <h1>E-Learning SMK Jaya Buana</h1>
        <p>Pusat repositori bahan mengajar, jobsheet praktikum, dan utility laboratorium Computer & Network Engineering.</p>
    </div>

    <!-- AREA MATERI HARI INI -->
    <?php
    $pin_query = mysqli_query($conn, "SELECT * FROM modul_tkj WHERE is_pinned = 1 ORDER BY tgl_upload DESC LIMIT 1");
    if(mysqli_num_rows($pin_query) > 0):
        $pinned = mysqli_fetch_array($pin_query);
    ?>
        <div class="pinned-container">
            <span class="pinned-badge">📌 JADWAL PRAKTIKUM HARI INI</span>
            <h2><?= $pinned['judul']; ?></h2>
            <p>Materi Kompetensi Keahlian <strong><?= $pinned['kategori']; ?></strong> untuk <strong>Kelas <?= $pinned['kelas']; ?> TKJ</strong>.</p>
            <div style="display: flex; gap: 10px; max-width: 400px;">
                <?php if(!empty($pinned['nama_file'])): ?>
                    <a href="uploads/<?= $pinned['nama_file']; ?>" class="btn btn-success" target="_blank" style="width: 100%; font-size: 12px; font-weight: 600;">📥 Unduh Jobsheet</a>
                <?php endif; ?>
                <?php if(!empty($pinned['link_eksternal'])): ?>
                    <a href="<?= $pinned['link_eksternal']; ?>" class="btn btn-success" target="_blank" style="width: 100%; font-size: 12px; font-weight: 600; background-color: #047857;">🌐 Buka Link</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- RUANG KELAS GRID -->
    <div class="grid-kelas">
        <div class="card-kelas">
            <div>
                <h3>Kelas 10 TKJ</h3>
                <p><?= $count10; ?> Materi Aktif</p>
            </div>
            <a href="modul.php?kelas=10" class="btn btn-primary">Buka Kelas</a>
        </div>
        <div class="card-kelas">
            <div>
                <h3>Kelas 11 TKJ</h3>
                <p><?= $count11; ?> Materi Aktif</p>
            </div>
            <a href="modul.php?kelas=11" class="btn btn-primary">Buka Kelas</a>
        </div>
        <div class="card-kelas">
            <div>
                <h3>Kelas 12 TKJ</h3>
                <p><?= $count12; ?> Materi Aktif</p>
            </div>
            <a href="modul.php?kelas=12" class="btn btn-primary">Buka Kelas</a>
        </div>
        <div class="card-kelas" style="border-top: 2px solid #10b981;">
            <div>
                <h3>🛠️ Tools Lab TKJ</h3>
                <p><?= $count_tools; ?> Software Siap Pakai</p>
            </div>
            <a href="modul.php?kategori=tools" class="btn btn-success" style="background-color: #10b981;">Buka Repositori</a>
        </div>
    </div>

    <!-- UPDATE TERBARU -->
    <div class="section-title">⚡ Materi Update Terbaru</div>
    <div class="mobile-card-list">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM modul_tkj ORDER BY tgl_upload DESC LIMIT 4");
        if(mysqli_num_rows($query) == 0){
            echo "<p style='color: #64748b; font-size:13px;'>Belum ada rilisan materi terbaru.</p>";
        }
        while($data = mysqli_fetch_array($query)) {
            ?>
            <div class="data-card">
                <div>
                    <div class="card-meta">
                        <span class="badge">Kelas <?= $data['kelas']; ?></span>
                        <span class="badge badge-type"><?= $data['tipe_berkas']; ?></span>
                    </div>
                    <div class="card-title"><?= $data['judul']; ?></div>
                </div>
                <div>
                    <div class="card-info-row">🛠️ <?= $data['kategori']; ?></div>
                    <div class="card-actions">
                        <?php if(!empty($data['nama_file'])): ?>
                            <a href="uploads/<?= $data['nama_file']; ?>" class="btn btn-primary" target="_blank">Unduh</a>
                        <?php endif; ?>
                        <?php if(!empty($data['link_eksternal'])): ?>
                            <a href="<?= $data['link_eksternal']; ?>" class="btn btn-success" target="_blank">Link</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- TATA LETAK 3 KOLOM SEJAJAR (STATISTIK, KALENDER, HELPDESK) -->
    <div class="section-title">📅 Agenda & Informasi Laboratorium</div>
    <div class="bottom-grid-container">
        
        <!-- KOLOM LEFT: INFO SISTEM -->
        <div class="widget-box">
            <div>
                <h3>🖥️ Status Infrastruktur</h3>
                <div class="status-item">
                    <span style="color: #94a3b8;">Sistem Server</span>
                    <span style="color: #10b981; font-weight: 700;">🟢 Online (XAMPP)</span>
                </div>
                <div class="status-item">
                    <span style="color: #94a3b8;">Koleksi Berkas</span>
                    <span style="font-weight: 600; color: #f1f5f9;"><?= $total_materi; ?> Items</span>
                </div>
                <div class="status-item">
                    <span style="color: #94a3b8;">Segmentasi</span>
                    <span style="color: #06b6d4; font-weight: 600;">Intranet Lab</span>
                </div>
            </div>
            <div style="font-size: 11px; color: #475569; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.02); padding-top: 10px;">
                Sinkronisasi database lokal stabil.
            </div>
        </div>

        <!-- KOLOM CENTER: KALENDER MINI -->
        <div class="calendar-wrapper">
            <div id="calendar"></div>
            <?php if($is_guru): ?>
                <div style="font-size: 10px; color: #06b6d4; text-align: center; margin-top: 8px; font-weight: 500;">
                    💡 Mode Guru Aktif: Klik tanggal kosong untuk mencatat agenda baru.
                </div>
            <?php endif; ?>
        </div>

        <!-- KOLOM RIGHT: HELPDESK GURU -->
        <div class="widget-box">
            <div>
                <h3>💬 Instruktur & Lab Support</h3>
                <p style="font-size: 12px; color: #94a3b8; line-height: 1.5; margin-bottom: 15px;">
                    Mengalami problem *corrupt data*, gagal koneksi, atau butuh bantuan peralatan ukur fiber optik? Hubungi instruktur lab:
                </p>
            </div>
            <a href="https://wa.me/6281234567890?text=Assalamu%27alaikum%20Pak%20Asep%2C%20saya%20siswa%20TKJ%20ingin%20bertanya%20terkait%20praktikum..." 
               target="_blank" 
               class="btn" 
               style="width: 100%; display: block; font-size: 12px; font-weight: 700; padding: 10px; background-color: #25d366; color: #ffffff; text-align: center; border-radius: 8px; border: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);">
                Hubungi Pak Asep (WhatsApp)
            </a>
        </div>

    </div>
</div>

<!-- Pustaka FullCalendar JS Core -->
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
            events: 'index.php?action=get_events',
            
            selectable: <?= $is_guru ? 'true' : 'false'; ?>, 
            select: function(info) {
                var title = prompt('Simpan Catatan Agenda/Praktikum untuk Tanggal ' + info.startStr + ':');
                if (title) {
                    var formData = new FormData();
                    formData.append('action', 'add_event');
                    formData.append('title', title);
                    formData.append('start', info.startStr);
                    formData.append('end', info.endStr);
                    formData.append('color', '#06b6d4');

                    fetch('index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            calendar.refetchEvents();
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