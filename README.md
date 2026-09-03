# KerjaLokal

KerjaLokal adalah aplikasi rekrutmen UMKM berbasis Laravel untuk mendukung SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi. Sistem mengintegrasikan pencari kerja, mitra UMKM, dan administrator dalam alur lowongan hingga keputusan seleksi.

## Fitur utama

- Autentikasi dan otorisasi tiga role dengan middleware.
- Lowongan dengan kategori, nominal upah, periode upah, lokasi, dan batas 8 jam kerja per hari.
- Relasi many-to-many lowongan dan keterampilan melalui `job_skill`.
- Pengajuan lamaran melalui pivot model `job_applications` dengan resume PDF privat.
- Dashboard mitra untuk mengelola lowongan, mengunduh resume, dan memperbarui status kandidat.
- Dashboard admin untuk meninjau seluruh lowongan, identitas akun mitra, pelamar, pengguna, kategori, dan keterampilan.
- Flash message di bawah navbar, error validasi per field, tema terang dan gelap, serta modal konfirmasi logout Bootstrap.

## Menjalankan aplikasi

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk MySQL, ubah `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env` sebelum menjalankan migrasi.

## Akun demo

Semua akun memakai kata sandi `REMOVED_CREDENTIAL`.

| Role | Email |
| --- | --- |
| Admin | `admin@kerjalokal.id` |
| Mitra UMKM | `mitra@suduttemu.id` |
| Mitra UMKM kedua | `mitra@berkahgrosir.id` |
| Pencari kerja | `budi@kerjalokal.id` |
| Pencari kerja kedua | `siti@kerjalokal.id` |

## Verifikasi

```bash
php artisan test --compact
vendor/bin/pint --test
npm run build
composer audit
npm audit
```

Resume tidak diekspos melalui folder publik. Berkas disimpan pada disk `local` dan hanya diberikan melalui controller download setelah policy memastikan pengguna adalah pemilik lowongan atau administrator.

## Struktur domain

- `users` memiliki banyak `jobs` sebagai mitra.
- `categories` memiliki banyak `jobs`.
- `jobs` dan `skills` memiliki relasi many-to-many melalui `job_skill`.
- `users` dan `jobs` memiliki relasi many-to-many melalui `job_applications`.
- `job_applications` menyimpan `resume_file`, `note`, dan status seleksi.
