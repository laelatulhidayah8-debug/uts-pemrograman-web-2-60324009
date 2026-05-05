<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
require_once 'config/database.php';

// Inisialisasi variabel form dan error
$errors      = [];
$kode        = '';
$nama        = '';
$deskripsi   = '';
$status      = 'Aktif'; // default

// -----------------------------------------------------------------
// Proses form jika method POST
// -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Ambil dan sanitasi input
    $kode      = trim(htmlspecialchars($_POST['kode_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $nama      = trim(htmlspecialchars($_POST['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']     ?? '', ENT_QUOTES, 'UTF-8'));
    $status    = trim(htmlspecialchars($_POST['status']        ?? '', ENT_QUOTES, 'UTF-8'));

    // -----------------------------------------------------------------
    // 2. Validasi Kode Kategori
    // -----------------------------------------------------------------
    if ($kode === '') {
        $errors['kode'] = 'Kode kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode kategori harus antara 4–10 karakter.';
    } elseif (!preg_match('/^KAT-/i', $kode)) {
        // Format harus diawali "KAT-"
        $errors['kode'] = 'Kode kategori harus diawali dengan "KAT-" (contoh: KAT-001).';
    } else {
        // Cek duplikasi kode di database (prepared statement)
        $stmtCek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
        $stmtCek->bind_param("s", $kode);
        $stmtCek->execute();
        $stmtCek->store_result();
        if ($stmtCek->num_rows > 0) {
            $errors['kode'] = 'Kode kategori sudah digunakan. Gunakan kode lain.';
        }
        $stmtCek->close();
    }

    // -----------------------------------------------------------------
    // 3. Validasi Nama Kategori
    // -----------------------------------------------------------------
    if ($nama === '') {
        $errors['nama'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama kategori minimal 3 karakter.';
    } elseif (strlen($nama) > 50) {
        $errors['nama'] = 'Nama kategori maksimal 50 karakter.';
    }

    // -----------------------------------------------------------------
    // 4. Validasi Deskripsi (opsional)
    // -----------------------------------------------------------------
    if ($deskripsi !== '' && strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    // -----------------------------------------------------------------
    // 5. Validasi Status
    // -----------------------------------------------------------------
    $statusValid = ['Aktif', 'Nonaktif'];
    if (!in_array($status, $statusValid)) {
        $errors['status'] = 'Status tidak valid. Pilih Aktif atau Nonaktif.';
    }

    // -----------------------------------------------------------------
    // 6. Jika tidak ada error, INSERT data
    // -----------------------------------------------------------------
    if (empty($errors)) {
        $stmtInsert = $conn->prepare(
            "INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)"
        );
        // s = string, untuk semua field
        $stmtInsert->bind_param("ssss", $kode, $nama, $deskripsi, $status);

        if ($stmtInsert->execute()) {
            // Berhasil → redirect ke index dengan pesan sukses
            $stmtInsert->close();
            header("Location: index.php?pesan=" . urlencode("Kategori berhasil ditambahkan!") . "&tipe=sukses");
            exit();
        } else {
            // Gagal insert
            $errors['db'] = 'Gagal menyimpan data. Silakan coba lagi.';
        }
        $stmtInsert->close();
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">➕ Tambah Kategori Baru</h4>
                </div>
                <div class="card-body">

                    <?php
                    // Tampilkan semua pesan error
                    if (!empty($errors)):
                    ?>
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
                                   value="<?= $kode ?>"
                                   placeholder="Contoh: KAT-001"
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
                                   value="<?= $nama ?>"
                                   placeholder="Nama kategori buku"
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
                                      maxlength="200"
                                      placeholder="Keterangan singkat kategori (opsional)"><?= $deskripsi ?></textarea>
                            <div class="form-text">Maksimal 200 karakter. Boleh kosong.</div>
                            <?php if (isset($errors['deskripsi'])): ?>
                                <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status (Radio Button) -->
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
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
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
