<?php
require_once 'config/database.php';

// -----------------------------------------------------------------
// 1. Validasi parameter ID dari GET
// -----------------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // ID tidak ada atau bukan angka
    header("Location: index.php?pesan=" . urlencode("ID tidak valid.") . "&tipe=error");
    exit();
}

$id = (int) $_GET['id']; // Cast ke integer untuk keamanan

// -----------------------------------------------------------------
// 2. Cek keberadaan data di database sebelum dihapus
// -----------------------------------------------------------------
$stmtCek = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$stmtCek->bind_param("i", $id);
$stmtCek->execute();
$hasil = $stmtCek->get_result();

if ($hasil->num_rows === 0) {
    // Data tidak ditemukan
    $stmtCek->close();
    header("Location: index.php?pesan=" . urlencode("Kategori tidak ditemukan.") . "&tipe=error");
    exit();
}

$stmtCek->close();

// -----------------------------------------------------------------
// 3. Proses DELETE menggunakan prepared statement
// -----------------------------------------------------------------
$stmtDel = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmtDel->bind_param("i", $id);
$stmtDel->execute();

// Cek affected_rows untuk memastikan berhasil
if ($stmtDel->affected_rows > 0) {
    // Berhasil dihapus
    $stmtDel->close();
    header("Location: index.php?pesan=" . urlencode("Kategori berhasil dihapus.") . "&tipe=sukses");
    exit();
} else {
    // Tidak ada baris yang terhapus (seharusnya tidak terjadi jika cek di atas lolos)
    $stmtDel->close();
    header("Location: index.php?pesan=" . urlencode("Gagal menghapus kategori. Silakan coba lagi.") . "&tipe=error");
    exit();
}
?>
