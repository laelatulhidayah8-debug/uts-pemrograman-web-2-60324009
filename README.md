# UTS Pemrograman Web 2 — Sistem Manajemen Kategori Buku

> Nama: Laelatul Hidayah  
> NIM: 60324009  
> Program Studi: Informatika  
> Mata Kuliah: Pemrograman Web 2  
> Institusi: UIN K.H. Abdurrahman Wahid Pekalongan  

---

## 📋 Deskripsi Aplikasi

Aplikasi web sederhana untuk mengelola **Kategori Buku** di perpustakaan. Dibangun menggunakan **PHP Native** dan **MySQL**, dengan antarmuka berbasis **Bootstrap 5**.

Fitur utama:
-  **CREATE** — Tambah kategori baru dengan validasi lengkap
-  **READ** — Tampilkan daftar semua kategori dalam tabel
-  **UPDATE** — Edit data kategori yang sudah ada
-  **DELETE** — Hapus kategori dengan konfirmasi

---

## ⚙️ Cara Instalasi dan Menjalankan

### Persyaratan
- PHP >= 7.4
- MySQL / MariaDB
- XAMPP / WAMP / Laragon (atau web server lokal lainnya)

### Langkah Instalasi

1. **Clone / Download** repository ini ke folder htdocs (XAMPP) atau www (WAMP):
   ```
   git clone https://github.com/laelatulhidayah8-debug/uts-pemrograman-web-2-60324009.git
   ```

2. **Import Database:**
   - Buka phpMyAdmin → http://localhost/phpmyadmin
   - Buat database baru: `uts_perpustakaan_60324009`
   - Pilih database tersebut → tab **Import**
   - Pilih file `database/database_backup.sql` → klik **Go**

3. **Konfigurasi Koneksi:**
   - Buka file `config/database.php`
   - Sesuaikan `DB_NAME` dengan nama database Anda (ganti `60324009`)
   - Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` jika perlu

4. **Jalankan Aplikasi:**
   - Buka browser → `http://localhost/uts-pemrograman-web-2-60324009/`

---

## 📁 Struktur Folder

```
uts_[NIM]/
├── config/
│   └── database.php        # Konfigurasi & koneksi database
├── database/
│   └── database_backup.sql # Export struktur + sample data
├── index.php               # READ  — Daftar semua kategori
├── create.php              # CREATE — Form tambah kategori
├── edit.php                # UPDATE — Form edit kategori
├── delete.php              # DELETE — Proses hapus kategori
└── README.md               # Dokumentasi ini
```

---

## 🔐 Keamanan

- Semua query menggunakan **Prepared Statement** (mencegah SQL Injection)
- Input disanitasi dengan `htmlspecialchars()` dan `trim()` (mencegah XSS)
- Validasi ID menggunakan casting `(int)` sebelum diproses
- Konfirmasi JavaScript sebelum menghapus data

# Link Repository

https://github.com/laelatulhidayah8-debug/uts-pemrograman-web-2-60324009

