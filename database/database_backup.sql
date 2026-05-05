-- Buat dan gunakan database
CREATE DATABASE IF NOT EXISTS `uts_perpustakaan_60324009`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `uts_perpustakaan_60324009`;

-- ============================================================
-- Tabel: kategori
-- ============================================================
CREATE TABLE IF NOT EXISTS `kategori` (
    `id_kategori`   INT             AUTO_INCREMENT PRIMARY KEY,
    `kode_kategori` VARCHAR(10)     UNIQUE NOT NULL,
    `nama_kategori` VARCHAR(50)     NOT NULL,
    `deskripsi`     TEXT,
    `status`        ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    `created_at`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Sample Data
-- ============================================================
INSERT INTO `kategori` (`kode_kategori`, `nama_kategori`, `deskripsi`, `status`) VALUES
('KAT-001', 'Pemrograman',  'Buku-buku tentang bahasa pemrograman',    'Aktif'),
('KAT-002', 'Database',     'Buku-buku tentang sistem basis data',      'Aktif'),
('KAT-003', 'Jaringan',     'Buku-buku tentang jaringan komputer',      'Aktif'),
('KAT-004', 'Sistem Operasi','Buku-buku tentang sistem operasi',        'Aktif'),
('KAT-005', 'Keamanan',     'Buku-buku tentang keamanan siber',         'Nonaktif');
