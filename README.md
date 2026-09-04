# KerjaLokal

KerjaLokal adalah website rekrutmen UMKM berbasis Laravel yang dibuat untuk mendukung **SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi**.

Aplikasi ini menghubungkan tiga role: pencari kerja, mitra UMKM, dan admin. Fitur utamanya meliputi pengelolaan lowongan, lamaran kerja dengan resume PDF, proses seleksi pelamar, chat, notifikasi, laporan lowongan, dan pengawasan admin.

## Menjalankan Project

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.
