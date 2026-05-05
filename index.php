<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
require_once 'config/database.php';

// Query data kategori menggunakan prepared statement
// ORDER BY id_kategori DESC agar data terbaru muncul di atas
$stmt = $conn->prepare("SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status, created_at FROM kategori ORDER BY id_kategori DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">📚 Daftar Kategori Buku</h2>
            <small class="text-muted">Sistem Manajemen Perpustakaan</small>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i>+</i> Tambah Kategori
        </a>
    </div>

    <?php
    // Tampilkan pesan sukses atau error dari session/GET
    if (isset($_GET['pesan'])) {
        $pesan = htmlspecialchars($_GET['pesan']);
        $tipe  = (isset($_GET['tipe']) && $_GET['tipe'] === 'error') ? 'danger' : 'success';
        echo "<div class='alert alert-{$tipe} alert-dismissible fade show' role='alert'>
                {$pesan}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
    ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="50"  class="text-center">No</th>
                        <th width="110">Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="110" class="text-center">Status</th>
                        <th width="160" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1; // Nomor urut dimulai dari 1
                        while ($row = $result->fetch_assoc()):
                            // Tentukan badge status
                            $badge_class = ($row['status'] === 'Aktif') ? 'bg-success' : 'bg-danger';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($row['kode_kategori']) ?></code></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td>
                            <?php
                            $desk = htmlspecialchars($row['deskripsi']);
                            // Potong teks panjang agar tabel rapi
                            echo (strlen($desk) > 60) ? substr($desk, 0, 60) . '...' : ($desk ?: '<span class="text-muted">-</span>');
                            ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $badge_class ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Tombol Edit (warna kuning/warning) -->
                            <a href="edit.php?id=<?= $row['id_kategori'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>

                            <!-- Tombol Hapus (warna merah/danger) dengan konfirmasi JS -->
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="confirmDelete(<?= $row['id_kategori'] ?>)">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada data kategori.
                            <a href="create.php">Tambah sekarang</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted small">
            Total: <?= $result->num_rows > 0 ? $result->num_rows : 0 ?> kategori
        </div>
    </div>

</div><!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * Konfirmasi sebelum menghapus kategori.
 * @param {number} id - ID kategori yang akan dihapus
 */
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus kategori ini? Data yang dihapus tidak dapat dikembalikan.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>
</body>
</html>
