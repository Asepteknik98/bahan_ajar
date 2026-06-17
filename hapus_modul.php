<?php
include 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil nama file fisik agar tidak mengotori penyimpanan local XAMPP
    $get_file = mysqli_query($conn, "SELECT nama_file FROM modul_tkj WHERE id = '$id'");
    $data = mysqli_fetch_array($get_file);
    $target_file = "uploads/" . $data['nama_file'];
    
    if (file_exists($target_file)) {
        unlink($target_file); // Hapus berkas dari server lokal
    }
    
    // Hapus record dari database
    mysqli_query($conn, "DELETE FROM modul_tkj WHERE id = '$id'");
}

header("Location: modul.php");
exit();
?>