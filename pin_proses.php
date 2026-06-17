<?php
session_start();
include 'config/database.php';

// Validasi Keamanan Hak Akses Guru
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'guru') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'pin') {
        // Reset/Unpin semua modul terlebih dahulu
        mysqli_query($conn, "UPDATE modul_tkj SET is_pinned = 0");
        // Pin modul yang dipilih guru sekarang
        mysqli_query($conn, "UPDATE modul_tkj SET is_pinned = 1 WHERE id = '$id'");
    } elseif ($action == 'unpin') {
        // Matikan status pin
        mysqli_query($conn, "UPDATE modul_tkj SET is_pinned = 0 WHERE id = '$id'");
    }
}

header("Location: modul.php");
exit();
?>