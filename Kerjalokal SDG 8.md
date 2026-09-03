# Product Requirement Document (PRD)

## 1. Identitas Proyek
* **Nama Platform:** KerjaLokal
* **Fokus SDG:** SDG 8 – Decent Work & Economic Growth (Bobot: 90 Poin)
* **Arsitektur:** Monolith Laravel MVC murni (Route -> Controller -> Model -> View)
* **Prinsip Antarmuka (UI):** *Simplified UI* menggunakan Bootstrap 5 via CDN (ringan, cepat dibuat, tanpa build tools rumit/Vite, dan tanpa package otomatis seperti Filament)
* **Database:** MySQL dengan relasi 1-to-Many dan Many-to-Many (Tabel Pivot)
* **Target Deployment:** Railway Cloud (Layanan Web Service + Managed MySQL Service)
* **Format Repositori:** `REPO_Nama Lengkap_Rekrutmen_EISD`

---

## 2. Landasan Solusi: Integrasi Dua Pilar SDG 8

Sistem menyeimbangkan kebutuhan pencari kerja dan pelaku usaha mikro dalam satu alur bisnis:

* **Pilar Decent Work (Pekerjaan Layak):**
  * Mewajibkan transparansi nominal upah (`salary_amount`) dan skema pembayaran (`salary_type`: per jam, harian, bulanan) pada setiap lowongan untuk mencegah eksploitasi kerja murah.
  * Membatasi dan mencantumkan jam kerja harian (`work_hours_per_day`) agar beban kerja tetap manusiawi dan sesuai kapasitas mahasiswa/pekerja lepas.
  * Menerapkan *skill-matching* melalui relasi *many-to-many* ke tabel keahlian (`job_skill`), memastikan tugas yang diberikan sesuai kompetensi pekerja.
  * Memberikan kepastian status lamaran (*real-time status tracking*) tanpa ketidakpastian (*ghosting*).
* **Pilar Economic Growth (Pertumbuhan Ekonomi):**
  * Memberikan akses rekrutmen instan bagi UMKM lokal dalam memenuhi kebutuhan tenaga kerja operasional harian saat masa ramai (*peak season*).
  * Meningkatkan penyerapan tenaga kerja usia produktif di tingkat lokal yang berdampak langsung pada perputaran daya beli masyarakat.
  * Menyajikan metrik agregasi ekonomi pada *dashboard*: total serapan tenaga kerja dan estimasi sirkulasi modal upah yang disalurkan UMKM ke pekerja lokal.

---

## 3. Pembagian Hak Akses Pengguna (Role-Based Access Control)

Sistem menggunakan Laravel Middleware untuk membatasi hak akses tiap peran:

* **Role: Admin**
  * Mengelola master data kategori pekerjaan (`categories`).
  * Mengelola master data keahlian (`skills`).
  * Mengakses *Macro Dashboard*: memantau total lowongan aktif, total tenaga kerja terserap (*accepted applications*), dan total akumulasi upah yang berputar.
  * Mengelola seluruh data akun pengguna dan moderasi lowongan.
* **Role: Mitra UMKM (Employer)**
  * Melengkapi profil usaha/toko.
  * Membuat, mengedit, dan menutup lowongan pekerjaan (CRUD Lowongan) dengan parameter kepatuhan kerja layak.
  * Menentukan kategori pekerjaan (1-to-Many) dan memilih daftar keahlian wajib (Many-to-Many).
  * Meninjau daftar pelamar, mengunduh berkas resume PDF, dan memperbarui status lamaran (*Pending*, *Interview*, *Accepted*, *Rejected*).
  * Melihat ringkasan serapan kerja pada usahanya.
* **Role: Pencari Kerja (Jobseeker)**
  * Melihat katalog lowongan dengan informasi transparan (nominal upah, jam kerja, lokasi, dan syarat keahlian).
  * Mengajukan lamaran pada lowongan yang terbuka dengan mengunggah resume PDF dan catatan pengalaman.
  * Memantau daftar riwayat lamaran dan status penerimaan secara langsung.

---

## 4. Desain Basis Data & Skema Migrasi (Traceability APSI)

Skema database wajib 100% cocok dengan Class Diagram UML pada Visual Paradigm:

* **Relasi 1-to-Many (One-to-Many):**
  * `users` (Employer) $\rightarrow$ `jobs`: 1 Mitra UMKM dapat mempublikasikan banyak lowongan kerja.
  * `categories` $\rightarrow$ `jobs`: 1 Kategori menaungi banyak lowongan kerja.
* **Relasi Many-to-Many (Wajib Tabel Pivot):**
  * **Pivot 1 (`job_skill`):** Menghubungkan `jobs` dan `skills`. Satu lowongan dapat membutuhkan banyak keahlian, dan satu keahlian dapat dimiliki oleh banyak lowongan.
  * **Pivot 2 (`job_applications`):** Menghubungkan `jobs` dan `users` (Jobseeker). Satu pelamar dapat melamar ke banyak lowongan, dan satu lowongan menerima banyak pelamar. Tabel ini memuat atribut pivot tambahan.

```sql
-- 1. Table: users
id: BIGINT UNSIGNED (PK, Auto Increment)
name: VARCHAR(255)
email: VARCHAR(255) (UNIQUE)
password: VARCHAR(255)
role: ENUM('admin', 'employer', 'jobseeker')
phone: VARCHAR(20) (NULLABLE)
business_name: VARCHAR(255) (NULLABLE)
created_at, updated_at: TIMESTAMP

-- 2. Table: categories
id: BIGINT UNSIGNED (PK, Auto Increment)
name: VARCHAR(100)
slug: VARCHAR(100) (UNIQUE)
created_at, updated_at: TIMESTAMP

-- 3. Table: skills
id: BIGINT UNSIGNED (PK, Auto Increment)
name: VARCHAR(100)
created_at, updated_at: TIMESTAMP

-- 4. Table: jobs
id: BIGINT UNSIGNED (PK, Auto Increment)
employer_id: BIGINT UNSIGNED (FK -> users.id, ON DELETE CASCADE)
category_id: BIGINT UNSIGNED (FK -> categories.id, ON DELETE RESTRICT)
title: VARCHAR(255)
description: TEXT
location: VARCHAR(255)
salary_type: ENUM('hourly', 'daily', 'monthly')
salary_amount: DECIMAL(12, 2)
work_hours_per_day: INT UNSIGNED
status: ENUM('open', 'closed') DEFAULT 'open'
created_at, updated_at: TIMESTAMP

-- 5. Table: job_skill (Pivot Table 1)
id: BIGINT UNSIGNED (PK, Auto Increment)
job_id: BIGINT UNSIGNED (FK -> jobs.id, ON DELETE CASCADE)
skill_id: BIGINT UNSIGNED (FK -> skills.id, ON DELETE CASCADE)
created_at, updated_at: TIMESTAMP

-- 6. Table: job_applications (Pivot Table 2 with Attributes)
id: BIGINT UNSIGNED (PK, Auto Increment)
job_id: BIGINT UNSIGNED (FK -> jobs.id, ON DELETE CASCADE)
user_id: BIGINT UNSIGNED (FK -> users.id, ON DELETE CASCADE)
resume_file: VARCHAR(255)
note: TEXT (NULLABLE)
status: ENUM('pending', 'interview', 'accepted', 'rejected') DEFAULT 'pending'
created_at, updated_at: TIMESTAMP