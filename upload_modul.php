<?php 
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'guru') {
    header("Location: index.php");
    exit();
}

include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$pesan = "";

if (isset($_POST['submit'])) {
    $judul        = mysqli_real_escape_string($conn, $_POST['judul']);
    $kelas        = $_POST['kelas'];
    $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tipe_berkas  = $_POST['tipe_berkas'];
    $link_luar    = mysqli_real_escape_string($conn, $_POST['link_eksternal']);
    
    // Otomatis set kategori jika user memilih opsi upload Tools Lab
    if($kelas == "Tools") {
        $kelas = "10"; // Default fallback database
        $kategori = "Software Utility";
    }

    $filename = "";
    $upload_ok = true;

    if (!empty($_FILES['file_modul']['name'])) {
        $filename = $_FILES['file_modul']['name'];
        $target   = "uploads/" . basename($filename);
        if (!move_uploaded_file($_FILES['file_modul']['tmp_name'], $target)) {
            $upload_ok = false;
            $pesan = "<div style='color: #ef4444; margin-bottom:15px;'>Gagal menyimpan berkas file fisik ke folder server.</div>";
        }
    }

    if ($upload_ok) {
        $insert = mysqli_query($conn, "INSERT INTO modul_tkj (judul, kelas, kategori, tipe_berkas, nama_file, link_eksternal, is_pinned) VALUES ('$judul', '$kelas', '$kategori', '$tipe_berkas', '$filename', '$link_luar', 0)");
        if ($insert) {
            $pesan = "<div style='color: #10b981; margin-bottom:15px;'>Konten baru berhasil diterbitkan ke sistem!</div>";
        } else {
            $pesan = "<div style='color: #ef4444; margin-bottom:15px;'>Gagal menyimpan data ke database server.</div>";
        }
    }
}
?>

<div class="main-content">
    <div class="header-main">
        <h1>Manajemen Terbit Bahan Konten</h1>
        <p>Unggah modul baru, kelola lembar penilaian praktik, atau lampirkan master aplikasi utilitas Lab komputer.</p>
    </div>

    <div class="form-container">
        <?= $pesan; ?>
        <form action="upload_modul.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Materi / Nama Ujian / Nama Aplikasi</label>
                <input type="text" name="judul" class="form-control" placeholder="Contoh: Cisco Packet Tracer v8.2 Installer" required>
            </div>
            
            <div class="form-group">
                <label>Tipe Konten Pembelajaran</label>
                <select name="tipe_berkas" class="form-control" required>
                    <option value="Modul Belajar">📚 Modul Belajar / Installer Utility</option>
                    <option value="Soal Teori">📝 Soal Teori (Bank Soal / Google Form)</option>
                    <option value="Lembar Praktik">⚙️ Lembar Praktik (Jobsheet / Rubrik Penilaian)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Alokasi Kategori Penempatan</label>
                <select name="kelas" class="form-control" required>
                    <option value="10">Kelas 10 TKJ</option>
                    <option value="11">Kelas 11 TKJ</option>
                    <option value="12">Kelas 12 TKJ</option>
                    <option value="Tools">🛠️ Tools / Installer Master Aplikasi Lab</option>
                </select>
            </div>

            <div class="form-group">
                <label>Kategori Kompetensi Keahlian (Abaikan jika memilih opsi Tools Lab)</label>
                <input type="text" name="kategori" class="form-control" placeholder="Contoh: Routing MikroTik, Linux Server, Fiber Optic">
            </div>

            <div class="form-group" style="border: 1px dashed var(--accent-color); padding: 15px; border-radius: 8px; margin-top: 20px;">
                <label style="color: #3b82f6; font-weight: bold;">[Opsi A] Unggah Berkas Fisik (PDF/DOCX/ZIP/EXE)</label>
                <input type="file" name="file_modul" class="form-control">
            </div>

            <div class="form-group" style="border: 1px dashed var(--accent-color); padding: 15px; border-radius: 8px;">
                <label style="color: #3b82f6; font-weight: bold;">[Opsi B] Tautkan Link Luar (YouTube / Website Resmi / Drive)</label>
                <input type="url" name="link_eksternal" class="form-control" placeholder="https://example.com/atau-link-download">
            </div>

            <button type="submit" name="submit" class="btn btn-success" style="width: 100%; margin-top: 15px;">Terbitkan Konten</button>
        </form>
    </div>
</div>

</body>
</html>