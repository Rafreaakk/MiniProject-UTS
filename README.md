# Profil

Nama: Muhammad Rafi Ardiyansyah
NIM : 251511051
Kelas : 2B-D3

# Mini Project MBG

Aplikasi web sederhana untuk pelaporan, peninjauan, tindak lanjut, dan rekap laporan terkait keamanan makanan MBG.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL/MariaDB
- XAMPP (untuk menjalankan MySQL lokal)

## Fitur

- Login dan akses berdasarkan peran: Pelapor, Pemantau Komunitas, dan Koordinator Komunitas.
- Pelapor dapat membuat, melihat, dan memperbaiki laporannya selama status laporan mengizinkan.
- Pelapor dapat mengunggah bukti pendukung JPG, JPEG, PNG, atau PDF dengan ukuran maksimal 2 MB.
- Pemantau dapat meninjau laporan, memberikan catatan, dan menandai laporan untuk tindak lanjut.
- Koordinator dapat mencatat penerusan laporan kepada pihak terkait.
- Pemantau dan Koordinator dapat melihat rekap laporan.
- Perubahan status laporan dicatat melalui Laravel Observer.

## Persiapan

Pastikan PHP, Composer, Git, dan XAMPP sudah tersedia. Jalankan MySQL dari XAMPP. Buat database bernama `mbg_reporting` melalui phpMyAdmin atau perintah SQL berikut:

## Instalasi

Jalankan perintah dari folder proyek:

```bash
composer install
```

Salin `.env.example` menjadi `.env` (di PowerShell gunakan `Copy-Item .env.example .env`), lalu sesuaikan bagian database di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mbg_reporting
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` jika konfigurasi MySQL lokal berbeda. Setelah itu jalankan:

```bash
php artisan key:generate
php artisan migrate --seed
```

Perintah `migrate --seed` membuat tabel dan mengisi data demo sekolah, distribusi, akun, serta beberapa laporan contoh.

## Menjalankan aplikasi

```bash
php artisan serve
```

Buka alamat `http://127.0.0.1:8000` di browser. MySQL harus tetap berjalan di XAMPP selama aplikasi digunakan. Apache XAMPP tidak diperlukan untuk menjalankan Laravel melalui `php artisan serve`.

## Akun demo

Semua akun demo menggunakan kata sandi `Demo12345!`.

| Peran | Email |
|---|---|
| Pelapor | `pelapor@mbg.test` |
| Pemantau Komunitas | `pemantau@mbg.test` |
| Koordinator Komunitas | `koordinator@mbg.test` |

Akun dan data contoh dibuat oleh `DemoDataSeeder`. Data awal sekolah, distribusi, dan akun belum dikelola melalui halaman aplikasi.

