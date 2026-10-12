# 🎨 SIM Skripsi Frontend Web Client (`SIM_SKRIPSI_FE`)

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-38B2AC.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0.svg?style=flat-square&logo=alpine.js)](https://alpinejs.dev/)
[![FontAwesome](https://img.shields.io/badge/FontAwesome-6.5-528DD7.svg?style=flat-square&logo=font-awesome)](https://fontawesome.com/)

Aplikasi Web Frontend untuk **Sistem Informasi Manajemen Skripsi (SIM Skripsi)** Universitas Muhammadiyah Bengkulu. Antarmuka ini mengadopsi standar desain visual yang seragam dan persis dengan ekosistem [SIM_MAHASISWA_FE](https://github.com/egiputuwijaya/SIM_MAHASISWA_FE) menggunakan palet warna Corporate Blue, tipografi Inter, dan sistem navigasi adaptif.

---

## 📋 Daftar Isi
1. [Fitur & Modul Antarmuka](#-fitur--modul-antarmuka)
2. [Teknologi & Desain Visual](#-teknologi--desain-visual)
3. [Struktur Folder Proyek](#-struktur-folder-proyek)
4. [Daftar Rute & Halaman](#-daftar-rute--halaman)
5. [Mode Pengujian Cepat (Quick Demo)](#-mode-pengujian-cepat-quick-demo)
6. [Panduan Menjalankan di Lingkungan Lokal](#-panduan-menjalankan-di-lingkungan-lokal)

---

## 🚀 Fitur & Modul Antarmuka

* **Autentikasi SSO & Quick Switch**:
  * Tampilan login elegan berlatar mesh gradient dengan kartu kaca (*glassmorphism*).
  * Terhubung dengan SSO JWT serta fitur *Quick Login* 1-klik untuk peran Mahasiswa, Dosen, dan Admin.
* **Dashboard Eksekutif**:
  * Kartu KPI statistik (Total Pengajuan, Judul Disetujui, Logbook Bimbingan, dan Agenda Sidang).
  * Banner sambutan dinamis sesuai nama dan peran pengguna yang aktif.
  * Tabel ringkasan usulan terkini & panduan alur tahapan penyelesaian tugas akhir.
* **Modul Pengajuan Judul**:
  * Filter status pengajuan (*Semua, Diajukan, Disetujui, Ditolak*).
  * Formulir pengajuan judul & pengunggahan berkas proposal penelitian PDF.
  * Lembar detail pengajuan lengkap dengan panel verifikasi dan plotting dosen pembimbing bagi dosen/kaprodi.
* **Modul Logbook Bimbingan**:
  * Kartu kendali konsultasi bimbingan mahasiswa.
  * Unggah draf naskah bab revisi (PDF).
  * Modal tanggapan evaluasi dan keputusan status ACC bimbingan bagi dosen pembimbing.
* **Modul Seminar & Sidang**:
  * Pendaftaran Sempro, Semhas, dan Sidang Akhir Munaqasyah.
  * Modal plotting jadwal pelaksanaan (tanggal, jam, dan ruangan/link ujian).
* **Modul Penilaian Sidang**:
  * Input nilai dewan penguji (skala 0–100) dan keputusan kelulusan (*Lulus / Revisi*).

---

## 🎨 Teknologi & Desain Visual

* **Arsitektur**: PHP Native berpolanya Controller & View (MVC) ringan tanpa framework berat.
* **Styling**: Tailwind CSS (Corporate Blue `#1d4ed8`, Elegant Slate `#f0f2f5`, dan Emerald Green).
* **Reaktivitas UI**: Alpine.js (sidebar collapse/expand dengan transisi mulus, modal popup dialog, dan dropdown).
* **Tipografi & Ikon**: Google Fonts (*Inter* & *Poppins*) serta FontAwesome 6 Free.
* **HTTP Client**: `ApiClient.php` berbasis cURL terpadu dengan penanganan file multipart upload ke backend Node.js.

---

## 📂 Struktur Folder Proyek

```text
SIM_SKRIPSI_FE/
├── app/
│   ├── Controllers/
│   │   ├── Controller.php               # Base Controller (render view & redirect helper)
│   │   ├── AuthController.php           # Autentikasi sesi & quick switch demo
│   │   ├── DashboardController.php      # Ringkasan statistik & aktivitas terbaru
│   │   ├── PengajuanJudulController.php # Alur usulan, upload proposal, & verifikasi
│   │   ├── BimbinganController.php      # Kartu bimbingan & ACC dosen
│   │   ├── SeminarController.php        # Pendaftaran sidang & plotting jadwal
│   │   └── NilaiController.php          # Penilaian ujian sidang dewan penguji
│   └── Helpers/
│       └── ApiClient.php                # Wrapper cURL request ke API Node.js (port 5000)
├── assets/
│   └── logoumb.png                      # Logo resmi universitas
├── public/                              # Web Root
│   ├── assets/
│   │   └── logoumb.png
│   ├── .htaccess                        # Apache URL rewrite
│   └── index.php                        # Front controller & router utama
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.php                  # Layout utama (sidebar adaptif & topbar profil)
│       │   └── auth.php                 # Layout autentikasi login
│       ├── auth/
│       │   └── login.php                # Halaman login responsif
│       ├── dashboard/
│       │   └── index.php                # Tampilan dashboard metrik
│       ├── pengajuan/
│       │   ├── index.php                # Tabel pengajuan & filter status
│       │   ├── create.php               # Form ajukan judul baru
│       │   └── detail.php               # Detail & panel verifikasi prodi
│       ├── bimbingan/
│       │   ├── index.php                # Riwayat bimbingan & modal respon
│       │   └── create.php               # Form tambah konsultasi
│       ├── seminar/
│       │   ├── index.php                # Jadwal sidang & modal plotting
│       │   └── create.php               # Form pendaftaran sidang
│       └── nilai/
│           └── index.php                # Tabel rekap & modal input nilai
├── routes/
│   └── web.php                          # Definisi pemetaan endpoint rute ke Controller
├── .htaccess                            # Root Apache rewrite ke public/
├── index.php                            # Entrypoint Laragon root
└── README.md
```

---

## 🧭 Daftar Rute & Halaman

| Rute URL | Controller | Fungsi |
|---|---|---|
| `/login` | `AuthController@login` | Halaman login SSO & Quick Switch Demo |
| `/logout` | `AuthController@logout` | Mengakhiri sesi login |
| `/quick-login/:role`| `AuthController@quickLogin` | Switch akun instan (`mahasiswa`, `dosen`, `admin`) |
| `/dashboard` | `DashboardController@index` | Dashboard ringkasan metrik & KPI |
| `/pengajuan` | `PengajuanJudulController@index` | Daftar judul pengajuan skripsi |
| `/pengajuan/tambah`| `PengajuanJudulController@create` | Form usulan judul & upload proposal PDF |
| `/pengajuan/detail/:id` | `PengajuanJudulController@detail` | Detail judul & verifikasi pembimbing |
| `/bimbingan` | `BimbinganController@index` | Logbook bimbingan & feedback pembimbing |
| `/bimbingan/tambah`| `BimbinganController@create` | Form tambah konsultasi & upload revisi |
| `/seminar` | `SeminarController@index` | Jadwal seminar/sidang & plotting waktu |
| `/seminar/daftar` | `SeminarController@create` | Form pendaftaran Sempro / Semhas / Sidang |
| `/nilai` | `NilaiController@index` | Penilaian ujian & kelulusan munaqasyah |

---

## ⚡ Mode Pengujian Cepat (Quick Demo)

Pada halaman login (`/login`), Anda dapat langsung mengklik tombol demo:
* 🎓 **Mahasiswa**: Masuk sebagai *Contoh Mahasiswa* (NIM: `20260001`).
* 👨‍🏫 **Dosen**: Masuk sebagai *Dr. Budi Santoso, M.Kom.* (NIP: `198501012010121001`).
* 🛡️ **Admin**: Masuk sebagai *Administrator SIM Skripsi*.

---

## 💻 Panduan Menjalankan di Lingkungan Lokal

### Opsi A: Menggunakan Laragon (Rekomendasi)
1. Letakkan folder ini di direktori `c:\laragon\www\SIM_SKRIPSI\SIM_SKRIPSI_FE`.
2. Pastikan Laragon telah dijalankan (**Start All**).
3. Buka peramban dan akses:
   ```text
   http://localhost/SIM_SKRIPSI/SIM_SKRIPSI_FE/public/
   ```

### Opsi B: Menggunakan PHP Built-in Server
1. Masuk ke direktori frontend di terminal:
   ```bash
   cd SIM_SKRIPSI_FE
   ```
2. Jalankan perintah server lokal:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Buka peramban di:
   ```text
   http://localhost:8000
   ```
   *(Pastikan Backend Node.js telah aktif di `http://localhost:5000`)*.
