DROP DATABASE IF EXISTS db_bahan_ajar;
CREATE DATABASE db_bahan_ajar;
USE db_bahan_ajar;

-- 1. Tabel Pengguna (Guru & Siswa)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('guru', 'siswa') NOT NULL
);

-- 2. Tabel Modul & Bank Soal yang Diperluas
CREATE TABLE modul_tkj (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kelas ENUM('10', '11', '12') NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    tipe_berkas ENUM('Modul Belajar', 'Soal Teori', 'Lembar Praktik') NOT NULL,
    nama_file VARCHAR(255) NULL,
    link_eksternal VARCHAR(255) NULL,
    tgl_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Data Akun Default (Password menggunakan MD5/Plain untuk kemudahan lokal XAMPP)
-- Akun Guru -> user: pakasep , pass: guru123
-- Akun Siswa -> user: siswa_tkj , pass: siswa123
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('pakasep', 'guru123', 'Pak Asep, S.Kom.', 'guru'),
('siswa_tkj', 'siswa123', 'Siswa TKJ Jaya Buana', 'siswa');

-- Insert Data Awal
INSERT INTO modul_tkj (judul, kelas, kategori, tipe_berkas, nama_file, link_eksternal) VALUES
('Dasar Jaringan Komputer & Pengabelan UTP', '10', 'Dasar Jaringan', 'Modul Belajar', 'modul_utp.pdf', ''),
('Ujian Praktik 1: Routing MikroTik MTCNA', '11', 'Networking', 'Lembar Praktik', 'praktik_routing.pdf', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
('Soal Teori WAN & Fiber Optic', '12', 'Fiber Optic', 'Soal Teori', '', 'https://forms.gle/contohGoogleForm')

ALTER TABLE modul_tkj ADD COLUMN is_pinned TINYINT(1) DEFAULT 0;

-- Menambahkan data dummy untuk Tools & Simulator Lab TKJ
INSERT INTO modul_tkj (judul, kelas, kategori, tipe_berkas, nama_file, link_eksternal, is_pinned) VALUES
('Winbox v3.40 (Aplikasi Remot MikroTik)', '11', 'Software Utility', 'Modul Belajar', '', 'https://mikrotik.com/download', 0),
('Cisco Packet Tracer v8.2', '10', 'Software Utility', 'Modul Belajar', '', 'https://www.netacad.com', 0);