<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
require_once 'config/database.php';

// -----------------------------------------------------------------
// 1. Ambil dan validasi ID dari parameter GET
// -----------------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // ID tidak ada atau bukan angka → redirect dengan pesan error
    header("Location: index.php?pesan=" . urlencode("ID tidak valid.") . "&tipe=error");
    exit();
}

$id = (int) $_GET['id']; // Cast ke integer untuk keamanan

// -----------------------------------------------------------------
// 2. Retrieve data berdasarkan ID (prepared statement)
// -----------------------------------------------------------------
$stmtAmbil = $conn->prepare(
    "SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status FROM kategori WHERE id_kategori = ?"
);
$stmtAmbil->bind_param("i", $id);
$stmtAmbil->execute();
$hasil = $stmtAmbil->get_result();

if ($hasil->num_rows === 0) {
    // Data tidak ditemukan → redirect dengan pesan error
    $stmtAmbil->close();
    header("Location: index.php?pesan=" . urlencode("Kategori tidak ditemukan.") . "&tipe=error");
    exit();
}

// Ambil data saat ini untuk pre-fill form
$data = $hasil->fetch_assoc();
$stmtAmbil->close();

// Inisialisasi variabel form dari data yang ada di database
$kode      = $data['kode_kategori'];
$nama      = $data['nama_kategori'];
$deskripsi = $data['deskripsi'];
$status    = $data['status'];

$errors = [];

// -----------------------------------------------------------------
// 3. Proses Update jika method POST
// -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil dan sanitasi input
    $kode      = trim(htmlspecialchars($_POST['kode_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $nama      = trim(htmlspecialchars($_POST['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']     ?? '', ENT_QUOTES, 'UTF-8'));
    $status    = trim(htmlspecialchars($_POST['status']        ?? '', ENT_QUOTES, 'UTF-8'));

    // -----------------------------------------------------------------
    // Validasi Kode Kategori
    // -----------------------------------------------------------------
    if ($kode === '') {
        $errors['kode'] = 'Kode kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode kategori harus antara 4–10 karakter.';
    } elseif (!preg_match('/^KAT-/i', $kode)) {
        $errors['kode'] = 'Kode kategori harus diawali dengan "KAT-" (contoh: KAT-001).';
    } else {
        // Cek duplikasi kode, EXCLUDE record yang sedang diedit
        // WHERE kode_kategori = ? AND id_kategori != ?
        $stmtCek = $conn->prepare(
            "SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?"
        );
        $stmtCek->bind_param("si", $kode, $id);
        $stmtCek->execute();
        $stmtCek->store_result();
        if ($stmtCek->num_rows > 0) {
            $errors['kode'] = 'Kode kategori sudah digunakan oleh data lain.';
        }
        $stmtCek->close();
    }

    // Validasi Nama Kategori
    if ($nama === '') {
        $errors['nama'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama kategori minimal 3 karakter.';
    } elseif (strlen($nama) > 50) {
        $errors['nama'] = 'Nama kategori maksimal 50 karakter.';
    }

    // Validasi Deskripsi (opsional)
    if ($deskripsi !== '' && strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    // Validasi Status
    $statusValid = ['Aktif', 'Nonaktif'];
    if (!in_array($status, $statusValid)) {
        $errors['status'] = 'Status tidak valid.';
    }

    // -----------------------------------------------------------------
    // Jika tidak ada error, lakukan UPDATE
    // -----------------------------------------------------------------
    if (empty($errors)) {
        $stmtUpdate = $conn->prepare(
            "UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?"
        );
        // s=string, i=integer
        $stmtUpdate->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

        if ($stmtUpdate->execute()) {
            $stmtUpdate->close();
            // Berhasil → redirect ke index dengan pesan sukses
            header("Location: index.php?pesan=" . urlencode("Kategori berhasil diperbarui!") . "&tipe=sukses");
            exit();
        } else {
            $errors['db'] = 'Gagal memperbarui data. Silakan coba lagi.';
        }
        $stmtUpdate->close();
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">✏️ Edit Kategori</h4>
                </div>
                <div class="card-body">

                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            <?php foreach ($errors as $err): ?>
                                <li><?= $err ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>

                        <!-- Kode Kategori -->
                        <div class="mb-3">
                            <label for="kode_kategori" class="form-label fw-semibold">
                                Kode Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="kode_kategori"
                                   name="kode_kategori"
                                   class="form-control <?= isset($errors['kode']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($kode) ?>"
                                   required
                                   maxlength="10">
                            <div class="form-text">Format: diawali "KAT-", 4–10 karakter, unik.</div>
                            <?php if (isset($errors['kode'])): ?>
                                <div class="invalid-feedback"><?= $errors['kode'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Nama Kategori -->
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-semibold">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nama_kategori"
                                   name="nama_kategori"
                                   class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($nama) ?>"
                                   required
                                   minlength="3"
                                   maxlength="50">
                            <?php if (isset($errors['nama'])): ?>
                                <div class="invalid-feedback"><?= $errors['nama'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea id="deskripsi"
                                      name="deskripsi"
                                      class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                                      rows="3"
                                      maxlength="200"><?= htmlspecialchars($deskripsi) ?></textarea>
                            <div class="form-text">Maksimal 200 karakter. Boleh kosong.</div>
                            <?php if (isset($errors['deskripsi'])): ?>
                                <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status (Radio Button) - pre-filled dari database -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="status" id="statusAktif" value="Aktif"
                                           <?= ($status === 'Aktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label text-success fw-semibold" for="statusAktif">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="status" id="statusNonaktif" value="Nonaktif"
                                           <?= ($status === 'Nonaktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger fw-semibold" for="statusNonaktif">
                                        Nonaktif
                                    </label>
                                </div>
                            </div>
                            <?php if (isset($errors['status'])): ?>
                                <div class="text-danger small"><?= $errors['status'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">💾 Update</button>
                            <a href="index.php" class="btn btn-secondary">← Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
