# 🎓 SIM Skripsi Frontend Web Client (`SIM_SKRIPSI_FE`)

[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-38B2AC.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0.svg?style=flat-square&logo=alpine.js)](https://alpinejs.dev/)
[![FontAwesome](https://img.shields.io/badge/FontAwesome-6.5-528DD7.svg?style=flat-square&logo=font-awesome)](https://fontawesome.com/)
[![API Backend](https://img.shields.io/badge/Backend%20API-Vercel%20Live-black.svg?style=flat-square&logo=vercel)](https://sim-skripsi-be.vercel.app)
[![SSO Auth](https://img.shields.io/badge/SSO%20Auth-sim--mhs--api-008080.svg?style=flat-square)](https://sim-mhs-api.vercel.app)

Aplikasi Web Frontend untuk **Sistem Informasi Manajemen Skripsi & Tugas Akhir (SIM Skripsi)** Universitas Muhammadiyah Bengkulu. 

Aplikasi ini mengadopsi standar arsitektur MVC Native yang ringan, desain visual korporat (*Corporate Blue*, *Glassmorphism*, dan tipografi Inter), serta terintegrasi penuh secara *live* dengan **Backend API Vercel** dan **Single Sign-On (SSO) SIM Mahasiswa Pusat**.

---

## 📋 Daftar Isi
1. [Ringkasan Pembaruan Sistem](#-ringkasan-pembaruan-sistem)
2. [Hak Akses & Pembagian Tugas Per Role (Multi-Tenancy)](#-hak-akses--pembagian-tugas-per-role-multi-tenancy)
3. [Arsitektur & Integrasi Layanan (Cloud APIs)](#-arsitektur--integrasi-layanan-cloud-apis)
4. [Kredensial Akun Pengujian (SSO & Quick Demo)](#-kredensial-akun-pengujian-sso--quick-demo)
5. [Fitur & Modul Utama](#-fitur--modul-utama)
6. [Struktur Folder Proyek](#-struktur-folder-proyek)
7. [Daftar Rute & Endpoint UI](#-daftar-rute--endpoint-ui)
8. [Panduan Instalasi & Menjalankan di Lingkungan Lokal](#-panduan-instalasi--menjalankan-di-lingkungan-lokal)

---

## 🚀 Ringkasan Pembaruan Sistem

Berikut adalah rangkuman perubahan dan peningkatan besar yang telah diimplementasikan pada Frontend:

* 🌐 **Integrasi Backend Live Vercel**:
  * Frontend sekarang terhubung langsung ke Backend REST API yang telah dihosting di `https://sim-skripsi-be.vercel.app/api`.
  * Seluruh data pengajuan skripsi, riwayat logbook bimbingan, jadwal seminar, dan nilai tersimpan secara terpusat pada basis data Cloud.
* 🏛️ **Sinkronisasi Data Master Fakultas & Prodi (SSO Pusat)**:
  * Terintegrasi dengan API Master Data `sim-mhs-api` (8 Fakultas & 20 Program Studi).
  * Menampilkan badge nama program studi pada baris data mahasiswa dan filter dinamis sesuai lingkup akses peran (*scope*).
* 🛡️ **Multi-Tenancy & Data Scoping**:
  * Pemisahan wilayah kerja otomatis: **Admin Prodi** hanya melihat mahasiswa prodinya, **Admin Dekanat** mengelola fakultasnya, dan **Mahasiswa** terisolasi ke datanya sendiri.
* ⚙️ **Dual-Engine HTTP Client (`ApiClient.php`)**:
  * Mendukung eksekusi via **cURL** serta fallback otomatis ke **PHP Native Streams (`stream_context`)**. Menjamin aplikasi tetap berjalan normal di server lokal Windows/Laragon meskipun ekstensi `php_curl` belum diaktifkan di Apache.
* 🌿 **Konfigurasi Lingkungan (`.env`)**:
  * Pengaturan alamat API dilakukan melalui file `.env` yang fleksibel tanpa hardcoding.
* 🌟 **Landing Page Publik Modern (`/`)**:
  * Dilengkapi Hero Section, metrik statistik, alur panduan 3 tahap skripsi, serta modal *Quick Demo Switcher*.
* 🧭 **Router Adaptif Subfolder Laragon**:
  * Resolusi path URL di `public/index.php` secara otomatis menyesuaikan subpath folder Laragon (`/SIM_SKRIPSI/SIM_SKRIPSI_FE/index.php` atau `/SIM_SKRIPSI/SIM_SKRIPSI_FE/`).

---

## 👥 Hak Akses & Pembagian Tugas Per Role (Multi-Tenancy)

Sistem SIM Skripsi dirancang dengan sistem keamanan berjenjang (*Role-Based Access Control* + *Data Scoping*) untuk 5 peran pengguna utama:

```
[ ADMIN UNIVERSITAS / ADMINISTRATOR ]  ---> Seluruh Universitas (8 Fakultas, 20 Prodi)
                 │
                 ▼
        [ ADMIN DEKANAT ]              ---> Khusus Fakultas yang Dipimpin
                 │
                 ▼
         [ ADMIN PRODI / KAPRODI ]      ---> Khusus Program Studi yang Dipimpin
                 │
       ┌─────────┴─────────┐
       ▼                   ▼
   [ DOSEN ]         [ MAHASISWA ]     ---> Mahasiswa Bimbingan / Data Mandiri
```

### 1. 🎓 MAHASISWA (`MAHASISWA`)
Peran utama bagi mahasiswa tingkat akhir yang menempuh mata kuliah Skripsi/Tugas Akhir.
* **Tugas & Wewenang**:
  1. **Pengajuan Judul**: Mengajukan draf judul skripsi, deskripsi proposal, dan mengunggah berkas proposal PDF.
  2. **Bimbingan & Logbook**: Mengisi kartu konsultasi bimbingan secara berkala dan mengunggah revisi draf naskah.
  3. **Pendaftaran Ujian**: Mendaftarkan diri pada Seminar Proposal (Sempro), Seminar Hasil (Semhas), atau Sidang Munaqasyah Akhir.
  4. **Hasil Kelulusan**: Memantau feedback catatan revisi dosen, status ACC bimbingan, dan lembar nilai sidang.
* **Batasan Data (*Scope*)**:
  * **Strict Self-Access**: Hanya dapat melihat dan mengedit data skripsi miliknya sendiri berdasarkan NIM dan `mahasiswa_id`. Tidak dapat mengakses judul atau draf milik mahasiswa lain.

---

### 2. 👨‍🏫 DOSEN PEMBIMBING & PENGUJI (`DOSEN`)
Peran bagi tenaga pendidik/dosen yang ditugaskan sebagai Pembimbing Utama (1), Pembimbing Pendamping (2), atau Tim Dewan Penguji Sidang.
* **Tugas & Wewenang**:
  1. **Evaluasi Logbook**: Memeriksa draf naskah bimbingan yang diunggah mahasiswa bimbingan.
  2. **Persetujuan (ACC)**: Memberikan catatan revisi, arahan penelitian, serta menetapkan status ACC bimbingan untuk kelayakan ujian.
  3. **Penilaian Sidang**: Mengisi form penilaian ujian skripsi (skala 0–100) dan menentukan rekomendasi status kelulusan (*Lulus / Revisi / Tidak Lulus*).
* **Batasan Data (*Scope*)**:
  * Hanya memiliki akses pada data mahasiswa yang secara resmi **diplot kepadanya** sebagai Pembimbing 1, Pembimbing 2, atau Penguji Seminar/Sidang.

---

### 3. 🏢 ADMIN PRODI / KAPRODI (`ADMIN_PRODI`)
Peran operasional di tingkat Program Studi (Ketua Program Studi atau Operator Akademik Jurusan).
* **Tugas & Wewenang**:
  1. **Verifikasi Usulan Judul**: Menerima usulan judul dari mahasiswa prodinya dan mengambil keputusan (*Disetujui*, *Ditolak*, atau *Perlu Revisi*).
  2. **Plotting Dosen Pembimbing**: Menunjuk dan menetapkan secara resmi Dosen Pembimbing 1 dan Pembimbing 2 untuk judul yang disetujui.
  3. **Manajemen Sidang**: Mengatur jadwal pelaksanaan seminar/sidang (tanggal, jam, ruangan, link Zoom) dan menunjuk susunan tim penguji.
  4. **Rekapitulasi**: Memantau progres seluruh mahasiswa bimbingan di lingkup program studinya.
* **Batasan Data (*Scope*)**:
  * **Prodi-Level Isolation**: Hanya dapat melihat, memvalidasi, dan mengelola mahasiswa dan skripsi yang terdaftar di **Program Studi yang dimilikinya** (contoh: Admin Prodi S1 Teknik Informatika tidak bisa melihat atau mengubah data Prodi Ilmu Hukum).

---

### 4. 🏛️ ADMIN DEKANAT (`ADMIN_DEKANAT`)
Peran manajerial dan pengawasan di tingkat Fakultas (Dekan, Wakil Dekan Bidang Akademik, atau Bagian Tata Usaha Fakultas).
* **Tugas & Wewenang**:
  1. **Pengawasan Fakultas**: Melakukan monitoring terhadap seluruh jalannya kegiatan skripsi pada program-program studi di bawah naungan fakultasnya.
  2. **Validasi SK Dekan**: Meninjau penetapan pembimbing dan jadwal sidang sebagai dasar penerbitan Surat Keputusan (SK) Dekan.
  3. **Laporan & Statistik**: Melihat metrik statistik kelulusan, rerata masa penyelesaian tugas akhir, serta beban kuota bimbingan dosen di fakultas.
* **Batasan Data (*Scope*)**:
  * **Faculty-Level Isolation**: Khusus mengawasi program-program studi yang **berada di bawah fakultasnya** (contoh: Admin Dekanat Fakultas Teknik hanya dapat mengelola Prodi Informatika, Sistem Informasi, dan Elektro; tidak bercampur dengan Fakultas Ekonomi atau FISIP).

---

### 5. 🛡️ ADMINISTRATOR UNIVERSITAS (`ADMINISTRATOR` / `ADMIN_UNIVERSITAS`)
Peran pengelola sistem tertinggi (Superadmin / Bagian BAAK / PPTIK Universitas).
* **Tugas & Wewenang**:
  1. **Full Unrestricted Access**: Mengakses seluruh menu, tabel, konfigurasi, dan modul tanpa batasan prodi maupun fakultas.
  2. **Sinkronisasi Data SSO**: Mengelola integrasi data pengguna, dosen, dan mahasiswa dengan master SSO UMB.
  3. **Audit Log & Master Setting**: Mengawasi riwayat sistem, kuota pembimbingan tingkat universitas, dan periode tahun akademik.
* **Batasan Data (*Scope*)**:
  * **Global Scope**: Bebas memfilter dan melihat data seluruh fakultas dan program studi di lingkungan Universitas Muhammadiyah Bengkulu.

---

## 🌐 Arsitektur & Integrasi Layanan (Cloud APIs)

```
┌─────────────────────────────────┐
│     SIM_SKRIPSI_FE (PHP)        │
│  Tailwind CSS + Alpine.js       │
└────────────────┬────────────────┘
                 │
                 ├── [1] REST API Request (Bearer Token)
                 ▼
┌─────────────────────────────────┐
│     SIM_SKRIPSI_BE (Node.js)    │ <── Hosted on Vercel
│     (Prisma ORM + PostgreSQL)   │     https://sim-skripsi-be.vercel.app
└────────────────┬────────────────┘
                 │
                 ├── [2] SSO Login & Master Data Sync
                 ▼
┌─────────────────────────────────┐
│     SIM_MAHASISWA_API (SSO)     │ <── SSO Master Service
│   (Fakultas, Prodi, Mahasiswa)  │     https://sim-mhs-api.vercel.app
└─────────────────────────────────┘
```

1. **Frontend Client (`SIM_SKRIPSI_FE`)**: Berjalan di web server lokal (Laragon/Apache) menggunakan port standar web.
2. **Backend API (`SIM_SKRIPSI_BE`)**: Layanan microservice REST API berbasis Express.js & Prisma ORM yang aktif di Vercel Serverless.
3. **Master SSO (`sim-mhs-api`)**: Sumber tunggal data master universitas (*Single Source of Truth*) untuk akun login, data 8 Fakultas, dan 20 Program Studi.

---

## 🔑 Kredensial Akun Pengujian (SSO & Quick Demo)

Anda dapat masuk menggunakan dua metode:

### Metode 1: Fitur 1-Klik "Quick Demo" (Rekomendasi Uji Coba)
Pada halaman login (`/login`) atau Landing Page (`/`), tersedia tombol instan untuk berganti peran tanpa perlu mengetikkan username dan sandi:
* 🎓 **Mahasiswa**: Ahmad Fauzan (NIM: `20260001` — S1 Teknik Informatika)
* 👨‍🏫 **Dosen**: Dr. Budi Santoso, M.Kom. (NIP: `198501012010121001`)
* 🏢 **Admin Prodi**: Operator S1 Teknik Informatika (`prodi_id: 1`)
* 🏛️ **Admin Dekanat**: Pengelola Fakultas Teknik (`fakultas_id: 1`)
* 🛡️ **Admin Univ**: Superadmin Pusat SIM Skripsi

### Metode 2: Akun SSO Asli (`https://sim-mhs-api.vercel.app`)
Jika ingin menguji alur autentikasi Single Sign-On yang sesungguhnya:
| Peran Akun | Username SSO | Password SSO | Keterangan |
|---|---|---|---|
| **Mahasiswa** | `mhs001` | `Mhs12345!` | Akun mahasiswa aktif |
| **Dosen** | `dosen01` | `Dosen123!` | Dosen pembimbing akademik |
| **Admin Prodi** | `adm_prodi_ti` | `AdmProdi123!` | Admin Prodi Teknik Informatika |
| **Admin Dekanat** | `adm_dekanat` | `AdmDekan123!` | Admin Dekanat Fakultas Teknik |
| **Admin Univ** | `admin` | `Admin123!` | Administrator Universitas |

---

## 🧭 Daftar Rute & Endpoint UI

| Rute URL | Controller | Hak Akses | Deskripsi Halaman |
|---|---|---|---|
| `/` | `LandingController@index` | Publik | Beranda publik, informasi fitur, & portal demo |
| `/login` | `AuthController@login` | Publik | Formulir masuk SSO & shortcut Quick Switch |
| `/logout` | `AuthController@logout` | Pengguna Aktif | Mengakhiri sesi login |
| `/quick-login/:role` | `AuthController@quickLogin` | Publik | Otomasi login instan peran tertentu |
| `/dashboard` | `DashboardController@index` | Semua Peran | Dashboard statistik & ringkasan aktivitas |
| `/pengajuan` | `PengajuanJudulController@index` | Semua Peran | Daftar pengajuan skripsi (terfilter scope) |
| `/pengajuan/tambah` | `PengajuanJudulController@create` | Mahasiswa | Form pengajuan judul baru & upload proposal PDF |
| `/pengajuan/detail/:id` | `PengajuanJudulController@detail` | Semua Peran | Lembar detail skripsi & panel verifikasi Kaprodi |
| `/pengajuan/verifikasi/:id` | `PengajuanJudulController@verifikasi` | Kaprodi/Admin | Aksi persetujuan judul & plotting pembimbing |
| `/bimbingan` | `BimbinganController@index` | Semua Peran | Logbook konsultasi bimbingan mahasiswa |
| `/bimbingan/tambah` | `BimbinganController@create` | Mahasiswa | Form input konsultasi baru & upload draf revisi |
| `/bimbingan/acc/:id` | `BimbinganController@acc` | Dosen | Pemberian feedback catatan & status ACC |
| `/seminar` | `SeminarController@index` | Semua Peran | Daftar jadwal ujian seminar & sidang |
| `/seminar/daftar` | `SeminarController@create` | Mahasiswa | Form pendaftaran Sempro / Semhas / Munaqasyah |
| `/seminar/jadwal/:id` | `SeminarController@updateJadwal` | Kaprodi/Admin | Penetapan waktu, ruang ujian, & penguji |
| `/nilai` | `NilaiController@index` | Dosen/Admin | Rekapitulasi nilai ujian sidang |
| `/nilai/input/:id` | `NilaiController@inputNilai` | Dosen/Penguji | Input angka nilai ujian & kelulusan |

---

## 💻 Panduan Instalasi & Menjalankan di Lingkungan Lokal

### 1. Prasyarat Sistem
* PHP 8.1 / 8.2 / 8.3 dengan ekstensi aktif: `json`, `mbstring`, `session`, `fileinfo`.
* Laragon (Windows) atau web server Apache/Nginx.
* Koneksi internet (untuk CDN Tailwind/Alpine serta memanggil Backend Cloud Vercel).

### 2. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env` di folder utama `SIM_SKRIPSI_FE`:
```ini
SIM_SKRIPSI_API_URL=https://sim-skripsi-be.vercel.app/api
SSO_API_URL=https://sim-mhs-api.vercel.app/api/v1
```

### 3. Menjalankan Menggunakan Laragon (Disarankan)
1. Letakkan folder ini di direktori Laragon:
   ```text
   d:\laragon\laragon\www\SIM_SKRIPSI\SIM_SKRIPSI_FE
   ```
2. Buka Laragon dan klik tombol **Start All** (Apache & MySQL).
3. Buka browser dan buka tautan berikut:
   ```text
   http://localhost/SIM_SKRIPSI/SIM_SKRIPSI_FE/index.php
   ```
   *(Atau `http://localhost/SIM_SKRIPSI/SIM_SKRIPSI_FE/`)*.

### 4. Menjalankan Menggunakan PHP Built-in Server
Jika ingin menjalankan tanpa Apache:
```bash
cd SIM_SKRIPSI_FE
php -S localhost:8000 -t public
```
Akses di peramban: `http://localhost:8000`

---

## 🛠️ Catatan Pemeliharaan & Troubleshooting

* **Layar Putih (WSOD) di Laragon**:
  * Pastikan file `.env` telah ada dan berisi alamat API yang benar.
  * Router secara otomatis menangani ketiadaan `php_curl` menggunakan fallback PHP Stream bawaan.
  * Mode debug `display_errors = 1` aktif pada `public/index.php` untuk mempermudah identifikasi masalah saat tahap pengembangan.
* **Upload Berkas PDF**:
  * Ukuran file draf proposal/naskah skripsi maksimal yang diizinkan adalah **10 MB** dengan format `.pdf`. Berkas yang diunggah akan diteruskan ke backend dan disimpan secara aman.

---

&copy; 2026 **SIM Skripsi** — Tim Pengembang Sistem Informasi Universitas Muhammadiyah Bengkulu.
