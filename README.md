# KerjaLokal

KerjaLokal adalah website rekrutmen UMKM berbasis Laravel yang dibuat untuk mendukung **SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi**.

Aplikasi ini menghubungkan tiga role: pencari kerja, mitra UMKM, dan admin. Fitur utamanya meliputi pengelolaan lowongan, lamaran kerja dengan resume PDF, proses seleksi pelamar, chat, notifikasi, laporan lowongan, dan pengawasan admin.

## Diagram Perancangan Sistem

### Use Case Diagram

Menunjukkan aktor dan fitur yang dapat digunakan oleh pengunjung, pencari kerja, Mitra UMKM, dan admin.

![Use Case Diagram KerjaLokal](<diagram/Kerjalokal Use Case.png>)

### Activity Diagram Pengajuan Lamaran Kerja

Menjelaskan alur aktivitas pencari kerja dan sistem dari login sampai lamaran berstatus menunggu tinjauan.

![Activity Diagram Pengajuan Lamaran Kerja](<diagram/Kerjalokal Activity Diagram [Fitur Pengajuan Lamaran Kerja].png>)

### Sequence Diagram Pengajuan Lamaran Kerja

Menunjukkan urutan interaksi antara aktor, antarmuka, pengendali, model, penyimpanan, dan layanan notifikasi.

![Sequence Diagram Pengajuan Lamaran Kerja](<diagram/Kerjalokal Sequence Diagram [Fitur Pengajuan Lamaran Kerja].png>)

### Class Diagram Model dan Relasi

Menggambarkan struktur model data beserta atribut, operasi, relasi, dan kardinalitasnya.

![Class Diagram Model dan Relasi](<diagram/Kerjalokal Class Diagram [Model dan Relasi].png>)

### Class Diagram Pengendali dan Model

Menjelaskan hubungan pengendali, model, dan Form Request yang digunakan dalam aplikasi.

![Class Diagram Pengendali dan Model](<diagram/Kerjalokal Class Diagram 2 [Controller & Model].png>)

## Menjalankan Project

Persyaratan: PHP 8.3 atau lebih baru, Composer, Node.js, dan npm.

```bash
git clone https://github.com/Hdiddz/REPO_Hadid-Hamar_Rekrutmen_EISD.git
cd REPO_Hadid-Hamar_Rekrutmen_EISD
composer install
npm install
copy .env.example .env
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
```

Buka file `.env`, lalu isi password akun demo dengan nilai buatan sendiri. Jangan commit file `.env`.

```dotenv
DEMO_USER_PASSWORD=password-yang-anda-tentukan-sendiri
```

Setelah itu, lanjutkan instalasi:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

Akun hasil seeding:

- Admin: username `Hadid` atau email `Hadid@adm.id`.
- Mitra UMKM: username `johan1` sampai `johan10`.
- Password seluruh akun seed mengikuti nilai `DEMO_USER_PASSWORD` pada `.env` masing-masing penguji.
- Akun pencari kerja dapat dibuat melalui halaman registrasi.
