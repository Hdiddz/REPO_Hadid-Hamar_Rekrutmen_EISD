# Panduan, Syarat, & Peraturan Penugasan Study Case Rekrutmen V7 EISD

Dokumen ini memuat seluruh aturan teknis, kriteria sistem, dan format pengumpulan penugasan seleksi Asisten Laboratorium (ASLAB) dan Asisten Praktikum (ASPRAK) Enterprise Information System Development (EISD) Laboratory[cite: 1].

---

## 1. Alur Rekrutmen
Rangkaian tahapan seleksi terdiri atas lima tahapan utama[cite: 1]:
1. **Pemaparan Tugas:** Penyampaian studi kasus dan penjelasan teknis pengerjaan tugas[cite: 1].
2. **Pengerjaan Tugas:** Waktu pengerjaan mandiri oleh calon asisten[cite: 1].
3. **Pengumpulan Tugas:** Penyerahan berkas melalui tautan formulir resmi[cite: 1].
4. **Wawancara & Microteaching:** Sesi uji kelayakan, wawancara teknis, dan praktik mengajar[cite: 1].
5. **Pengumuman:** Penetapan hasil akhir seleksi[cite: 1].

---

## 2. Ketentuan Pemilihan Tema Sustainable Development Goals (SDG)
Calon asisten diwajibkan memilih salah satu topik SDG yang tersedia untuk diangkat permasalahannya dan dibangun solusinya ke dalam sistem[cite: 1]:

* **SDG 11: Sustainable Cities & Communities** – 100 Poin[cite: 1]
* **SDG 13: Climate Action** – 95 Poin[cite: 1]
* **SDG 8: Decent Work & Economic Growth** – 90 Poin[cite: 1]
* **SDG 2: Zero Hunger** – 85 Poin[cite: 1]
* **SDG 3: Good Health & Well-being** – 80 Poin[cite: 1]
* **SDG 4: Quality Education** – 80 Poin[cite: 1]

### Catatan Penting SDG:
* Identifikasi permasalahan nyata yang relevan dengan SDG yang dipilih[cite: 1].
* Rancang dan bangun solusi inovatif atas permasalahan tersebut[cite: 1].
* Solusi yang dibangun **wajib diimplementasikan menjadi aplikasi website** (*at least* website; penambahan fitur IoT atau aplikasi mobile bersifat opsional/nilai tambah)[cite: 1].
* Setiap topik memiliki tingkat kompleksitas berbeda (contoh: SDG 4 berbobot 80 poin karena solusinya cenderung umum/mudah ditebak seperti LMS, sedangkan SDG berpoin lebih tinggi menuntut eksplorasi solusi yang lebih mendalam)[cite: 1].

---

## 3. Syarat Analisis Perancangan Sistem Informasi (APSI)
Perancangan sistem wajib dibuat menggunakan perangkat lunak **Visual Paradigm** (lisensi akademik dapat diakses via: `https://ap.visual-paradigm.com/telkom-university`)[cite: 1].

Diagram yang wajib disertakan mencakup[cite: 1]:
1. **Use Case Diagram:** Memvisualisasikan seluruh fungsionalitas fitur sistem serta batasan hak akses tiap peran pengguna (*role*)[cite: 1].
2. **Class Diagram:** Menggambarkan arsitektur kode secara utuh (Model, Controller) serta pemetaan relasi antar kelas/tabel basis data[cite: 1].
3. **Activity Diagram (Minimal 1 Fitur Utama):** Menjelaskan langkah demi langkah proses bisnis sistem, termasuk percabangan kondisi berhasil (*success path*) dan gagal (*failure/validation path*)[cite: 1].
4. **Sequence Diagram (Minimal 1 Fitur Utama):** Menunjukkan urutan waktu interaksi antar objek sistem saat fitur utama dijalankan[cite: 1].

---

## 4. Syarat Teknis Pembuatan Website (WAD / Laravel)
Aplikasi web wajib memenuhi batasan teknis berikut[cite: 1]:
* **Framework:** Wajib menggunakan **Laravel murni** (*Laravel only*)[cite: 1].
* **Kesesuaian Isu:** Tema sistem harus selaras dengan topik SDG yang dipilih[cite: 1].
* **Autentikasi & Autorisasi:** Fitur login dan register dengan batasan hak akses yang tegas untuk masing-masing role[cite: 1].
* **Relasi Basis Data Wajib:**
  * Menerapkan relasi **One-to-Many (1-to-Many)**[cite: 1].
  * Menerapkan relasi **Many-to-Many** wajib menggunakan tabel perantara (**tabel pivot**)[cite: 1].
* **Validasi & Umpan Balik:**
  * Wajib menerapkan validasi input di sisi server (*server-side validation*)[cite: 1].
  * Wajib menampilkan pesan kilat (*flash message*) sebagai umpan balik aksi pengguna (sukses/gagal)[cite: 1].
  * Disarankan menyediakan modal konfirmasi interaktif (pop-up) untuk aksi krusial, seperti konfirmasi keluar sistem (*logout*)[cite: 1].
* **Standar Arsitektur:** Wajib mematuhi alur standar MVC (*Route* $\rightarrow$ *Controller* $\rightarrow$ *Model* $\rightarrow$ *View*)[cite: 1].
* **Sinkronisasi Desain & Kode:** Skema database dan migration wajib **100% cocok** dengan Class Diagram/UML yang telah dirancang[cite: 1].
* **Larangan Paket Otomatis:** **Dilarang keras** menggunakan paket CMS/CRUD generator otomatis seperti Filament, Nova, atau Voyager[cite: 1].
* **Repositori Kode:** Kode diunggah ke GitHub publik dengan format penamaan:  
  `REPO_Nama Lengkap_Rekrutmen_EISD`[cite: 1].

---

## 5. Syarat Video Dokumentasi
Video dibuat untuk mempresentasikan hasil analisis dan aplikasi yang telah dibangun[cite: 1]:
* **Ketentuan Teknis Video:**
  * Durasi video: Maksimal 10 menit[cite: 1].
  * Resolusi video: Minimal 720p (kualitas jernih)[cite: 1].
  * Platform unggah: Google Drive atau YouTube (pastikan akses tautan bersifat publik)[cite: 1].
  * Format judul/penamaan video: `VIDEO_Nama Lengkap_Rekrutmen_EISD`[cite: 1].
* **Struktur Isi Video:**
  1. Perkenalan diri (Nama, NIM, kelas/asal)[cite: 1].
  2. Penjelasan SDG yang dipilih, latar belakang masalah, dan solusi yang dibangun secara singkat[cite: 1].
  3. Pemaparan ringkas Use Case Diagram dan satu Sequence Diagram[cite: 1].
  4. Demonstrasi langsung fungsionalitas website (*demo website*)[cite: 1].
  5. Penjelasan alur kode (*walkthrough code*) untuk satu fitur utama secara runtut (dari *Route*, *Controller*, *Model*, hingga *Blade View*)[cite: 1].

---

## 6. Format Laporan Pengumpulan (PDF)
Laporan disusun dalam satu berkas PDF dengan format penamaan:  
`Readme_Nama Lengkap_Rekrutmen_EISD`[cite: 1]

Berkas laporan wajib memuat konten berikut[cite: 1]:
1. Penjelasan topik SDG yang diambil dan uraian singkat mengenai solusi yang ditawarkan[cite: 1].
2. Lampiran gambar hasil analisis perancangan (Use Case, Class, Activity, dan Sequence Diagram) disertai deskripsi penjelasan singkat, serta mencantumkan tautan Google Drive untuk tiap gambar agar dapat ditinjau dalam resolusi tinggi (HD)[cite: 1].
3. Uraian reflektif mengenai kendala/kesulitan selama pengerjaan penugasan beserta saran solutifnya[cite: 1].
4. Tautan repositori GitHub proyek website[cite: 1].
5. Tautan video dokumentasi (YouTube atau Google Drive)[cite: 1].

---

## 7. Ketentuan Slide Presentasi Microteaching (Sesi Wawancara)
Calon asisten wajib menyiapkan materi ajar untuk sesi wawancara/praktik mengajar (*microteaching*)[cite: 1]:
* **Ketentuan Slide:**
  * Jumlah slide maksimal 12 slide (sudah mencakup slide pembuka dan penutup)[cite: 1].
  * Desain dan tema bebas, namun wajib menjaga etika kesopanan (dilarang menyertakan konten tidak pantas/nyeleneh)[cite: 1].
* **Materi Ajar:**
  * Wajib memuat minimal 2 materi ajar dari mata kuliah yang diminati untuk posisi ASPRAK[cite: 1].
  * Contoh: Apabila meminati mata kuliah WAD dan APSI, slide harus berisi minimal 2 topik materi WAD dan 2 topik materi APSI (total akumulasi slide tetap maksimal 12 slide)[cite: 1].
* **Mekanisme Pengumpulan:**
  * Slide PPT **tidak perlu diunggah** pada formulir pengumpulan, melainkan langsung ditayangkan (*share screen*) saat sesi wawancara/microteaching berlangsung[cite: 1].

---

## 8. Tautan Pengumpulan & Tenggat Waktu (Deadline)
* **Tautan Formulir Pengumpulan:** `https://tinyurl.com/PendaftaranMKPraktikumEISD`[cite: 1]
* **Batas Akhir (Deadline):** **08 September, Pukul 23.59 WIB**[cite: 1]