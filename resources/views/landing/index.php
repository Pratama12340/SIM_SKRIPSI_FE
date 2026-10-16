<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="SIM Skripsi — Sistem Informasi Manajemen Skripsi dan Tugas Akhir Terintegrasi Universitas Muhammadiyah Bengkulu (UMB).">
    <meta name="theme-color" content="#1e40af">
    <title><?= htmlspecialchars($title ?? 'SIM Skripsi — Universitas Muhammadiyah Bengkulu', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        emeraldAccent: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    },
                    boxShadow: {
                        'premium': '0 20px 40px -15px rgba(30, 64, 175, 0.12)',
                        'glow': '0 0 50px -10px rgba(59, 130, 246, 0.35)',
                        'card': '0 4px 20px -2px rgba(15, 23, 42, 0.06)',
                    }
                }
            }
        };
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Hide scrollbar for clean horizontal scrolling */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Background grid pattern */
        .bg-grid-pattern {
            background-size: 36px 36px;
            background-image: 
                linear-gradient(to right, rgba(226, 232, 240, 0.5) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.5) 1px, transparent 1px);
        }

        /* Glassmorphism classes */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .glass-navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Floating animation */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float {
            animation: floatSlow 4s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-glow {
            animation: pulseGlow 6s infinite;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-brand-500 selection:text-white"
      x-data="{
          mobileNav: false,
          scrolled: false,
          quickLoginModal: false,
          activeHeroSlide: 0,
          heroSlides: [
              '<?= url('assets/university/1.jpeg') ?>',
              '<?= url('assets/university/2.jpeg') ?>',
              '<?= url('assets/university/3.jpeg') ?>',
              '<?= url('assets/university/4.jpg') ?>',
              '<?= url('assets/university/5.jpeg') ?>'
          ],
          heroTimer: null,
          activePersonSlide: 0,
          personSlides: [
              '<?= url('assets/person/1.jpeg') ?>',
              '<?= url('assets/person/2.jpeg') ?>',
              '<?= url('assets/person/3.jpeg') ?>'
          ],
          personTimer: null,
          nextPersonSlide() {
              this.activePersonSlide = (this.activePersonSlide + 1) % this.personSlides.length;
          },
          prevPersonSlide() {
              this.activePersonSlide = (this.activePersonSlide - 1 + this.personSlides.length) % this.personSlides.length;
          },
          activeStep: 1,
          selectedCategory: 'semua',
          searchTopic: '',
          calcSks: 110,
          calcTahap: 'judul',
          faqOpen: null,
          toastMsg: '',
          showToast(msg) {
              this.toastMsg = msg;
              setTimeout(() => { this.toastMsg = ''; }, 3000);
          },
          init() {
              this.heroTimer = setInterval(() => {
                  this.activeHeroSlide = (this.activeHeroSlide + 1) % this.heroSlides.length;
              }, 4500);
              this.personTimer = setInterval(() => {
                  this.activePersonSlide = (this.activePersonSlide + 1) % this.personSlides.length;
              }, 4000);
          }
      }"
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- TOAST NOTIFICATION -->
    <div x-cloak x-show="toastMsg" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 bg-slate-900 text-white rounded-xl shadow-2xl border border-slate-700/80 text-sm font-medium">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span x-text="toastMsg"></span>
    </div>

    <!-- FLASH SUCCESS ALERT IF ANY -->
    <?php if (!empty($flashSuccess)): ?>
    <div class="bg-emerald-600 text-white text-xs sm:text-sm py-2.5 px-4 text-center font-medium shadow-sm flex items-center justify-center gap-2">
        <i class="fa-solid fa-circle-info"></i>
        <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <?php endif; ?>

    <!-- ================= NAVBAR ================= -->
    <header class="sticky top-0 z-40 transition-all duration-300 border-b"
            :class="scrolled ? 'glass-navbar border-slate-200/80 shadow-sm py-3' : 'bg-white/90 backdrop-blur-md border-slate-100 py-3.5 sm:py-4'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                
                <!-- Brand / Logo -->
                <a href="<?= url('/') ?>" class="flex items-center gap-3 shrink-0 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white border border-slate-200 shadow-xs flex items-center justify-center p-1.5 transition-transform group-hover:scale-105 duration-200 shrink-0">
                        <img src="<?= url('assets/logoumb.png') ?>" alt="Logo UMB" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 tracking-tight leading-none group-hover:text-brand-700 transition whitespace-nowrap">
                                SIM Skripsi
                            </span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-brand-50 text-brand-700 border border-brand-200 shrink-0">
                                UMB
                            </span>
                        </div>
                        <span class="hidden sm:block text-[11px] font-medium text-slate-500 leading-tight mt-0.5 whitespace-nowrap">
                            Universitas Muhammadiyah Bengkulu
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Hanya 4 Menu Utama) -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="#beranda" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100/80 transition whitespace-nowrap">
                        Beranda
                    </a>
                    <a href="#alur" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100/80 transition whitespace-nowrap">
                        Alur Skripsi
                    </a>
                    <a href="#fitur" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100/80 transition whitespace-nowrap">
                        Fitur Utama
                    </a>
                    <a href="#topik" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100/80 transition whitespace-nowrap">
                        Inspirasi Topik
                    </a>
                </nav>

                <!-- Desktop Action Button Area -->
                <div class="hidden md:flex items-center gap-3 shrink-0">
                    <?php if (!empty($user)): ?>
                        <!-- Logged In User State -->
                        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                            <a href="<?= url('/dashboard') ?>" 
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm shadow-sm hover:shadow transition whitespace-nowrap">
                                <i class="fa-solid fa-gauge-high text-xs"></i>
                                <span>Dashboard Saya</span>
                            </a>
                            <a href="<?= url('/logout') ?>" 
                               title="Keluar dari sistem" 
                               class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Tombol Login Saja -->
                        <a href="<?= url('/login') ?>" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 active:scale-95 text-white font-semibold text-sm shadow-sm hover:shadow-brand-700/20 hover:shadow transition duration-150 whitespace-nowrap">
                            <i class="fa-solid fa-right-to-bracket text-xs"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Action Area (Hamburger & Quick Button) -->
                <div class="flex md:hidden items-center gap-2 shrink-0">
                    <?php if (!empty($user)): ?>
                        <a href="<?= url('/dashboard') ?>" class="px-3 py-1.5 rounded-lg bg-brand-700 text-white text-xs font-semibold whitespace-nowrap">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-xs font-semibold shadow-xs whitespace-nowrap">
                            <i class="fa-solid fa-right-to-bracket text-[11px]"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                    <button type="button"
                            @click="mobileNav = !mobileNav" 
                            aria-label="Toggle navigation menu"
                            class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none transition">
                        <i class="fa-solid" :class="mobileNav ? 'fa-xmark text-lg' : 'fa-bars text-lg'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Nav Drawer -->
        <div x-cloak x-show="mobileNav" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-b border-slate-200 bg-white/95 backdrop-blur-md px-4 pt-3 pb-5 space-y-1.5 shadow-lg">
            <a href="#beranda" @click="mobileNav = false" class="block px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100 transition">
                Beranda
            </a>
            <a href="#alur" @click="mobileNav = false" class="block px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100 transition">
                Alur Skripsi
            </a>
            <a href="#fitur" @click="mobileNav = false" class="block px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100 transition">
                Fitur Utama
            </a>
            <a href="#topik" @click="mobileNav = false" class="block px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:text-brand-700 hover:bg-slate-100 transition">
                Inspirasi Topik
            </a>
            
            <div class="pt-3 border-t border-slate-100">
                <?php if (empty($user)): ?>
                <a href="<?= url('/login') ?>" class="w-full text-center inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-brand-700 hover:bg-brand-800 text-white shadow-sm transition">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Login</span>
                </a>
                <?php else: ?>
                <a href="<?= url('/dashboard') ?>" class="w-full text-center block px-4 py-2.5 rounded-xl text-sm font-semibold bg-brand-700 text-white shadow-sm mb-2">
                    Buka Dashboard (<?= htmlspecialchars($user['nama'] ?? 'User') ?>)
                </a>
                <a href="<?= url('/logout') ?>" class="w-full text-center block px-4 py-2 rounded-xl text-sm font-semibold text-red-600 bg-red-50">
                    Logout
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- ================= HERO SECTION (BACKGROUND SLIDESHOW KAMPUS & OVERLAY BIRU) ================= -->
    <section id="beranda" class="relative pt-16 pb-20 sm:pt-24 sm:pb-28 lg:pt-32 lg:pb-36 overflow-hidden text-white">
        
        <!-- Background Image Slideshow (assets/university: 1.jpeg, 2.jpeg, 3.jpeg, 4.jpg, 5.jpeg) -->
        <div class="absolute inset-0 -z-20 overflow-hidden bg-slate-950">
            <template x-for="(slide, index) in heroSlides" :key="index">
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                     :class="activeHeroSlide === index ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                    <img :src="slide" 
                         alt="Kampus Universitas Muhammadiyah Bengkulu" 
                         class="w-full h-full object-cover transition-transform duration-[6000ms] ease-out"
                         :class="activeHeroSlide === index ? 'scale-105' : 'scale-100'">
                </div>
            </template>
        </div>

        <!-- Overlay Biru Elegan & Transparan (Foto Kampus Tetap Terlihat & Tulisan Jelas Terbaca) -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950/90 via-brand-900/80 to-blue-900/75 mix-blend-multiply"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-slate-950/90 via-brand-950/50 to-brand-950/60"></div>
        <div class="absolute inset-0 -z-10 bg-blue-950/30"></div>

        <!-- Hero Content Container (Terpusat & Bersih) -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            
           
            <!-- Main Headline -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.18] mb-6 font-heading drop-shadow-md">
                Selesaikan Skripsi Anda <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-blue-100 to-emerald-300">
                    Lebih Terstruktur, Cepat & Pasti
                </span>
            </h1>

            <!-- Description -->
            <p class="text-base sm:text-lg lg:text-xl text-blue-100/90 leading-relaxed mb-8 max-w-3xl mx-auto font-normal drop-shadow-sm">
                Platform akademik satu pintu untuk memfasilitasi pengajuan judul, bimbingan logbook daring, penjadwalan seminar proposal, dan ujian sidang skripsi secara transparan di lingkungan Universitas Muhammadiyah Bengkulu.
            </p>

            <!-- Call-to-Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4 mb-10">
                <?php if (!empty($user)): ?>
                    <a href="<?= url('/dashboard') ?>" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:py-4 rounded-xl bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 text-white font-bold text-base shadow-xl shadow-brand-950/40 hover:shadow-brand-500/30 transition-all duration-200">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Lanjutkan ke Dashboard</span>
                    </a>
                    <a href="#alur" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 sm:py-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-base border border-white/25 backdrop-blur-md transition shadow-sm">
                        <i class="fa-solid fa-route text-sky-300"></i>
                        <span>Pelajari 5 Tahap Alur</span>
                    </a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:py-4 rounded-xl bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 text-white font-bold text-base shadow-xl shadow-brand-950/40 hover:shadow-brand-500/30 transition-all duration-200">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Mulai Pengajuan Judul</span>
                    </a>
                    <a href="#alur" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 sm:py-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-base border border-white/25 backdrop-blur-md transition shadow-sm">
                        <i class="fa-solid fa-route text-sky-300"></i>
                        <span>Pelajari 5 Tahap Alur</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mini Trust Badges -->
            <div class="pt-6 border-t border-white/15 flex flex-wrap items-center justify-center gap-y-2 gap-x-6 sm:gap-x-8 text-xs sm:text-sm font-medium text-blue-100">
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> Terintegrasi SSO SIM Mahasiswa
                </span>
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> E-Signature & Rekap Otomatis
                </span>
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> Deteksi Orisinalitas Topik
                </span>
            </div>

            <!-- Slideshow Dot Indicators -->
            <div class="flex items-center justify-center gap-2 pt-8">
                <template x-for="(slide, index) in heroSlides" :key="index">
                    <button type="button" 
                            @click="activeHeroSlide = index"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="activeHeroSlide === index ? 'w-8 bg-sky-300' : 'w-2.5 bg-white/40 hover:bg-white/70'"
                            :aria-label="'Slide Foto Kampus ' + (index + 1)"></button>
                </template>
            </div>

        </div>
    </section>

    <!-- ================= METRIK & STATISTIK SECTION ================= -->
    <section class="py-8 sm:py-12 bg-white border-y border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 lg:gap-8">
                
                <!-- Stat 1 -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-2.5 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-slate-50 transition">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg sm:text-2xl shrink-0">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 font-heading">
                            <?= htmlspecialchars($stats['total_pengajuan'] ?? '1.420+') ?>
                        </h3>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-slate-500 font-medium">Judul Skripsi Disetujui</p>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-2.5 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-slate-50 transition">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-2xl shrink-0">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 font-heading">
                            <?= htmlspecialchars($stats['tingkat_kelulusan'] ?? '98.6%') ?>
                        </h3>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-slate-500 font-medium">Lulus Tepat Waktu</p>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-2.5 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-slate-50 transition">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg sm:text-2xl shrink-0">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 font-heading">
                            <?= htmlspecialchars($stats['dosen_aktif'] ?? '120+') ?>
                        </h3>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-slate-500 font-medium">Dosen Pembimbing Aktif</p>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-2.5 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-slate-50 transition">
                    <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg sm:text-2xl shrink-0">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 font-heading">
                            &lt; 24 Jam
                        </h3>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-slate-500 font-medium">Rerata Verifikasi Berkas</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= ALUR & TAHAPAN SKRIPSI (INTERAKTIF STEPPER) ================= -->
    <section id="alur" class="py-12 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-14">
                
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-heading tracking-tight mb-3">
                    Alur 5 Tahapan Skripsi di UMB
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                    Sistem mengawal seluruh tahapan tugas akhir Anda dari mulai penentuan ide hingga berita acara yudisium. Klik setiap tahapan untuk melihat rincian syarat dan panduan.
                </p>
            </div>

            <!-- Stepper Buttons Grid (Responsif 2 Kolom Mobile, 5 Kolom Desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 sm:gap-3 mb-6 sm:mb-8">
                <!-- Step 1 -->
                <button type="button" 
                        @click="activeStep = 1"
                        :class="activeStep === 1 ? 'bg-brand-700 text-white shadow-md ring-2 ring-brand-700 ring-offset-2' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="p-3 sm:p-4 rounded-xl text-left transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold flex items-center justify-center"
                              :class="activeStep === 1 ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">
                            1
                        </span>
                        <i class="fa-solid fa-book text-xs sm:text-sm" :class="activeStep === 1 ? 'text-brand-200' : 'text-slate-400'"></i>
                    </div>
                    <p class="font-bold text-xs sm:text-sm leading-tight">Pengajuan Judul</p>
                    <span class="text-[10px] sm:text-[11px] opacity-80 block mt-0.5">Review & Plagiasi</span>
                </button>

                <!-- Step 2 -->
                <button type="button" 
                        @click="activeStep = 2"
                        :class="activeStep === 2 ? 'bg-brand-700 text-white shadow-md ring-2 ring-brand-700 ring-offset-2' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="p-3 sm:p-4 rounded-xl text-left transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold flex items-center justify-center"
                              :class="activeStep === 2 ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">
                            2
                        </span>
                        <i class="fa-solid fa-comments text-xs sm:text-sm" :class="activeStep === 2 ? 'text-brand-200' : 'text-slate-400'"></i>
                    </div>
                    <p class="font-bold text-xs sm:text-sm leading-tight">Logbook Daring</p>
                    <span class="text-[10px] sm:text-[11px] opacity-80 block mt-0.5">Bimbingan Bab 1-5</span>
                </button>

                <!-- Step 3 -->
                <button type="button" 
                        @click="activeStep = 3"
                        :class="activeStep === 3 ? 'bg-brand-700 text-white shadow-md ring-2 ring-brand-700 ring-offset-2' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="p-3 sm:p-4 rounded-xl text-left transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold flex items-center justify-center"
                              :class="activeStep === 3 ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">
                            3
                        </span>
                        <i class="fa-solid fa-chalkboard-user text-xs sm:text-sm" :class="activeStep === 3 ? 'text-brand-200' : 'text-slate-400'"></i>
                    </div>
                    <p class="font-bold text-xs sm:text-sm leading-tight">Seminar Proposal</p>
                    <span class="text-[10px] sm:text-[11px] opacity-80 block mt-0.5">Uji Kelayakan Riset</span>
                </button>

                <!-- Step 4 -->
                <button type="button" 
                        @click="activeStep = 4"
                        :class="activeStep === 4 ? 'bg-brand-700 text-white shadow-md ring-2 ring-brand-700 ring-offset-2' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="p-3 sm:p-4 rounded-xl text-left transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold flex items-center justify-center"
                              :class="activeStep === 4 ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">
                            4
                        </span>
                        <i class="fa-solid fa-award text-xs sm:text-sm" :class="activeStep === 4 ? 'text-brand-200' : 'text-slate-400'"></i>
                    </div>
                    <p class="font-bold text-xs sm:text-sm leading-tight">Sidang Skripsi</p>
                    <span class="text-[10px] sm:text-[11px] opacity-80 block mt-0.5">Ujian Komprehensif</span>
                </button>

                <!-- Step 5 -->
                <button type="button" 
                        @click="activeStep = 5"
                        :class="activeStep === 5 ? 'bg-brand-700 text-white shadow-md ring-2 ring-brand-700 ring-offset-2' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="col-span-2 sm:col-span-1 md:col-span-1 p-3 sm:p-4 rounded-xl text-left transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold flex items-center justify-center"
                              :class="activeStep === 5 ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">
                            5
                        </span>
                        <i class="fa-solid fa-graduation-cap text-xs sm:text-sm" :class="activeStep === 5 ? 'text-brand-200' : 'text-slate-400'"></i>
                    </div>
                    <p class="font-bold text-xs sm:text-sm leading-tight">Bebas Revisi & Lulus</p>
                    <span class="text-[10px] sm:text-[11px] opacity-80 block mt-0.5">Tanda Tangan & Repo</span>
                </button>
            </div>

            <!-- Dynamic Stepper Detail Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 border border-slate-200 shadow-card">
                
                <!-- DETAIL STEP 1 -->
                <div x-cloak x-show="activeStep === 1" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tahap 01 • Penentuan Riset</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Pengajuan & Verifikasi Judul Skripsi</h3>
                        </div>
                        <a href="<?= url('/pengajuan') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition w-full sm:w-auto">
                            <span>Buka Formulir Pengajuan</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Mahasiswa mengajukan minimal 1 hingga 2 alternatif topik skripsi lengkap dengan rumusan masalah, ruang lingkup, dan calon dosen pembimbing. Sistem melakukan pengecekan orisinalitas terhadap repositori skripsi yang sudah ada sebelum diteruskan ke Kaprodi untuk verifikasi persetujuan.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Prasyarat Akademik</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Lulus minimal 110 SKS kumulatif</li>
                                <li>IPK minimal 2.50</li>
                                <li>Telah lulus Metodologi Penelitian</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Dokumen Yang Disiapkan</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Transkrip Nilai Sementara</li>
                                <li>Ringkasan Ide / Draf Proposal (PDF)</li>
                                <li>Bukti KRS Semester Berjalan</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Tips dari Kaprodi</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Pastikan studi kasus memiliki objek penelitian yang nyata dan memiliki kontribusi praktis atau akademis yang jelas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DETAIL STEP 2 -->
                <div x-cloak x-show="activeStep === 2" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tahap 02 • Bimbingan Intensif</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Logbook & Konsultasi Bimbingan Daring</h3>
                        </div>
                        <a href="<?= url('/bimbingan') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition w-full sm:w-auto">
                            <span>Buka Modul Bimbingan</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Setelah judul disetujui, mahasiswa dijadwalkan bimbingan berkala dengan Pembimbing 1 dan Pembimbing 2. Setiap sesi konsultasi, unggahan bab, revisi naskah, dan catatan dosen dicatat secara digital di sistem sebagai bukti keabsahan menuju pendaftaran seminar.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Ketentuan Logbook</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Minimal 8x bimbingan per dosen</li>
                                <li>Mencatat ringkasan materi diskusi</li>
                                <li>Status ACC bertahap tiap bab</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Fitur Utama Modul</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Unggah naskah PDF & Word</li>
                                <li>Notifikasi revisi dosen pembimbing</li>
                                <li>Export Lembar Kendali Bimbingan</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Tips Bimbingan</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Selalu sertakan lampiran revisi dengan highlighting agar dosen pembimbing dapat memeriksa perubahan secara cepat.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DETAIL STEP 3 -->
                <div x-cloak x-show="activeStep === 3" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tahap 03 • Uji Kelayakan</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Pendaftaran & Pelaksanaan Seminar Proposal</h3>
                        </div>
                        <a href="<?= url('/seminar') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition w-full sm:w-auto">
                            <span>Jadwal & Pendaftaran Sempro</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Naskah Proposal (Bab I - III) yang telah disetujui kedua pembimbing didaftarkan untuk Seminar Proposal. Sistem secara otomatis mencocokkan ketersediaan jadwal dosen pembimbing dan dosen penguji serta menerbitkan surat undangan seminar.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Syarat Pendaftaran Sempro</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Persetujuan ACC Proposal Pembimbing 1 & 2</li>
                                <li>Hasil Turnitin Bab 1-3 (&lt; 25%)</li>
                                <li>Bukti menghadiri minimal 5 seminar kawan</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Luaran Sempro</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Berita Acara Seminar Proposal</li>
                                <li>Form Rekomendasi / Catatan Penguji</li>
                                <li>Surat Izin Penelitian / Pengambilan Data</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Tips Ujian Sempro</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Kuasai metodologi, metode pengujian, dan urgensi topik. Slide presentasi cukup 10-12 slide to the point.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DETAIL STEP 4 -->
                <div x-cloak x-show="activeStep === 4" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tahap 04 • Ujian Akhir</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Ujian Sidang Skripsi (Komprehensif)</h3>
                        </div>
                        <a href="<?= url('/seminar') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition w-full sm:w-auto">
                            <span>Daftar Sidang Skripsi</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Puncak perjuangan tugas akhir. Mahasiswa mempresentasikan produk, sistem, atau temuan penelitian secara komprehensif di depan dewan penguji. Penilaian dilakukan langsung di aplikasi dengan rubrik standar akreditasi BAN-PT.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Syarat Kelayakan Sidang</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Naskah Lengkap Bab I s/d V disetujui</li>
                                <li>Aplikasi / Prototipe siap didemonstrasikan</li>
                                <li>Sertifikat TOEFL & Surat Bebas Tanggungan</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Komponen Penilaian</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Naskah & Format Ilmiah (30%)</li>
                                <li>Penguasaan Materi & Teori (40%)</li>
                                <li>Hasil Produk & Presentasi (30%)</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Tips Sidang</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Siapkan video demo cadangan (offline) apabila sewaktu-waktu demo langsung mengalami kendala teknis jaringan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DETAIL STEP 5 -->
                <div x-cloak x-show="activeStep === 5" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Tahap 05 • Yudisium & Kelulusan</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">Bebas Revisi, Pengesahan & Publikasi</h3>
                        </div>
                        <a href="<?= url('/nilai') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold transition w-full sm:w-auto">
                            <span>Lihat Lembar Nilai Akhir</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Setelah menyelesaikan perbaikan dari penguji, mahasiswa mengunggah lembar revisi untuk mendapatkan tanda tangan pengesahan elektronik (E-Signature) dari dewan penguji dan Dekan, disusul unggah naskah ke Repositori Perpustakaan UMB.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Langkah Terakhir</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>ACC Lembar Revisi Penguji 1 & 2</li>
                                <li>Unggah Dokumen Final Bebas Plagiasi (&lt; 20%)</li>
                                <li>Unggah Artikel Ilmiah Jurnal</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Dokumen Yudisium</span>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                                <li>Surat Keterangan Bebas Revisi</li>
                                <li>Berita Acara Yudisium Resmi</li>
                                <li>Transkrip Final untuk Ijazah</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block mb-1">Selamat!</span>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Anda dinyatakan LULUS Sarjana (S.Kom / S.T.) dan siap mendaftar ke tahapan Wisuda Universitas Muhammadiyah Bengkulu!
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= FITUR UTAMA ================= -->
    <section id="fitur" class="py-12 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-heading tracking-tight mb-3 sm:mb-4">
                    Fitur Modern yang Mempermudah Seluruh Pihak
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                    Dirancang untuk memenuhi kebutuhan mahasiswa, dosen pembimbing, penguji, kaprodi, hingga staf akademik fakultas dalam satu lingkungan yang terpadu.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                
                <!-- Feature 1 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-brand-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-brand-700 transition">
                        Bimbingan Logbook Realtime
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Catatan revisi dan diskusi tersimpan digital dengan riwayat jelas. Tidak perlu khawatir kertas lembar konsultasi hilang atau tercecer.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-emerald-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-emerald-700 transition">
                        Deteksi Topik Kembar
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem memverifikasi kebaruan judul dan membandingkannya dengan karya ilmiah terdahulu agar tidak terjadi duplikasi penelitian.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-700 transition">
                        Penjadwalan Sidang Bebas Bentrok
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Algoritma pintar mencocokkan ketersediaan ruang ujian serta jadwal mengajar dewan penguji secara otomatis dan efisien.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-amber-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-amber-700 transition">
                        Rubrik Penilaian Objektif
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Form nilai digital terhubung dengan bobot prodi. Skor dan predikat (A, A-, B+) dihitung otomatis tanpa risiko salah rekap.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-purple-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-purple-700 transition">
                        Single Sign-On (SSO) Kampus
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Gunakan akun NIM dan NIDN aktif dari portal SIM Mahasiswa UMB tanpa perlu mendaftar atau mengingat banyak password baru.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="p-5 sm:p-7 rounded-2xl bg-white border border-slate-200 hover:border-rose-300 hover:shadow-premium transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mb-4 sm:mb-5 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 group-hover:text-rose-700 transition">
                        E-Signature & Arsip Cloud
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Lembar pengesahan dan berita acara ditandatangani secara elektronik ber-QR code resmi dan tersimpan aman di cloud repository.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= INSPIRASI TOPIK RISET (INTERAKTIF SEARCH & FILTER) ================= -->
    <section id="topik" class="py-12 sm:py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 mb-8 sm:mb-12">
                <div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-heading tracking-tight mb-2">
                        Inspirasi Topik & Riset Skripsi
                    </h2>
                    <p class="text-slate-600 text-xs sm:text-sm lg:text-base max-w-xl">
                        Cari ide penelitian terkini yang relevan dengan tren industri teknologi dan keilmuan Fakultas Teknik UMB.
                    </p>
                </div>

                <!-- Live Search Box -->
                <div class="w-full md:w-80 shrink-0">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" 
                               x-model="searchTopic"
                               placeholder="Cari topik (misal: AI, Flutter, IoT)..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                    </div>
                </div>
            </div>

            <!-- Category Pills Filter (Responsif Horizontal Scroll pada Layar HP) -->
            <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-2 sm:pb-0 sm:flex-wrap no-scrollbar mb-6 sm:mb-8">
                <button type="button" 
                        @click="selectedCategory = 'semua'"
                        :class="selectedCategory === 'semua' ? 'bg-brand-700 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="shrink-0 sm:shrink px-3.5 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap">
                    Semua Bidang
                </button>
                <button type="button" 
                        @click="selectedCategory = 'ai'"
                        :class="selectedCategory === 'ai' ? 'bg-brand-700 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="shrink-0 sm:shrink px-3.5 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap">
                    Artificial Intelligence & Vision
                </button>
                <button type="button" 
                        @click="selectedCategory = 'webmobile'"
                        :class="selectedCategory === 'webmobile' ? 'bg-brand-700 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="shrink-0 sm:shrink px-3.5 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap">
                    Web & Mobile Engineering
                </button>
                <button type="button" 
                        @click="selectedCategory = 'iot'"
                        :class="selectedCategory === 'iot' ? 'bg-brand-700 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="shrink-0 sm:shrink px-3.5 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap">
                    IoT & Jaringan Sensor
                </button>
                <button type="button" 
                        @click="selectedCategory = 'security'"
                        :class="selectedCategory === 'security' ? 'bg-brand-700 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                        class="shrink-0 sm:shrink px-3.5 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap">
                    Keamanan Informasi & Kriptografi
                </button>
            </div>

            <!-- Topic Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                
                <!-- Topic Card 1 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'ai') && (searchTopic === '' || 'ai computer vision kopi yolo deep learning'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold">Artificial Intelligence</span>
                            <span class="text-xs text-amber-600 font-semibold"><i class="fa-solid fa-star"></i> Populer</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Deteksi Penyakit Daun Sawit Berbasis YOLOv8 dan Computer Vision
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Penerapan arsitektur deep learning untuk mendeteksi hama perkebunan sawit Bengkulu secara cepat lewat kamera ponsel pintar.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: Python, PyTorch, FastAPI</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

                <!-- Topic Card 2 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'webmobile') && (searchTopic === '' || 'mobile flutter pwa sistem informasi rekam medis gis puskesmas'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold">Web & Mobile</span>
                            <span class="text-xs text-emerald-600 font-semibold"><i class="fa-solid fa-chart-line"></i> Kebutuhan Industri</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Sistem Informasi Geografis (GIS) Sebaran UMKM Bengkulu dengan Algoritma Haversine
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Platform pemetaan interaktif lokasi dan perizinan UMKM untuk mempermudah dinas koperasi memantau binaan wilayah.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: Flutter, Leaflet.js, PostgreSQL</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

                <!-- Topic Card 3 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'iot') && (searchTopic === '' || 'iot esp32 smart agriculture hidroponik lora mqtt'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-[11px] font-bold">IoT & Sensor</span>
                            <span class="text-xs text-slate-500 font-semibold">Studi Lapangan</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Otomasi Sistem Pengairan Hidroponik Berbasis IoT & Protokol MQTT
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Monitoring nutrisi PPM dan pH air secara realtime dengan notifikasi Telegram Bot dan aktuator pompa otomatis.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: ESP32, MQTT, Vue.js</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

                <!-- Topic Card 4 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'security') && (searchTopic === '' || 'security kriptografi aes enkripsi e-voting steganografi rsa'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 text-[11px] font-bold">Keamanan Informasi</span>
                            <span class="text-xs text-rose-600 font-semibold">Advanced</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Implementasi Hybrid Cryptography AES-256 & RSA untuk Rekam Medis Pasien
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pengamanan data rekam medis elektronik terhadap ancaman man-in-the-middle attack pada cloud storage rumah sakit.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: Go, AES-256, Docker</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

                <!-- Topic Card 5 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'ai') && (searchTopic === '' || 'ai nlp sentimen analisis twitter ulasan tokopedia bert'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold">Artificial Intelligence</span>
                            <span class="text-xs text-slate-500 font-semibold">NLP</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Analisis Sentimen Opini Publik Pilkada Bengkulu Menggunakan IndoBERT
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pemanfaatan model transformer pra-latih bahasa Indonesia untuk memetakan aspirasi dan sentimen masyarakat di media sosial.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: Python, HuggingFace, Scikit</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

                <!-- Topic Card 6 -->
                <div x-show="(selectedCategory === 'semua' || selectedCategory === 'webmobile') && (searchTopic === '' || 'web sistem pakar forward chaining spk saw topsis beasiswa'.toLowerCase().includes(searchTopic.toLowerCase()))"
                     class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold">Decision Support</span>
                            <span class="text-xs text-slate-500 font-semibold">SPK</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug mb-2">
                            Sistem Pendukung Keputusan Seleksi Penerima Beasiswa Menggunakan Metode TOPSIS & SAW
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Komparasi akurasi perangkingan dua metode pengambil keputusan multi-kriteria pada penentuan beasiswa mahasiswa berprestasi.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Teknologi: Laravel, Tailwind CSS, MySQL</span>
                        <button type="button" 
                                @click="showToast('Referensi topik berhasil disalin!')" 
                                class="text-brand-700 hover:text-brand-800 font-semibold">
                            Salin Ide
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= KALKULATOR & ESTIMASI WAKTU SKRIPSI ================= -->
    <section id="kalkulator" class="py-12 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-12 text-white shadow-2xl relative overflow-hidden">
                
                <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 items-center relative z-10">
                    
                    <!-- Left: Form Input -->
                    <div class="lg:col-span-6 space-y-5 sm:space-y-6">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-bold uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-calculator"></i> Fitur Interaktif
                            </span>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold font-heading leading-tight">
                                Simulasi Target Kelulusan Anda
                            </h2>
                            <p class="text-slate-300 text-xs sm:text-sm mt-2">
                                Pilih kondisi akademik Anda saat ini untuk melihat rekomendasi timeline bimbingan dan estimasi jadwal sidang.
                            </p>
                        </div>

                        <!-- Current Stage Selection -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                Di tahap mana Anda sekarang?
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-2.5 text-xs">
                                <button type="button" 
                                        @click="calcTahap = 'judul'"
                                        :class="calcTahap === 'judul' ? 'bg-brand-600 text-white font-bold border-brand-400' : 'bg-slate-800/80 text-slate-300 border-slate-700 hover:bg-slate-800'"
                                        class="p-3 rounded-xl border text-left transition flex items-center justify-between sm:block">
                                    <div>
                                        <p class="font-bold">1. Baru Mengajukan Judul</p>
                                        <span class="text-[10px] opacity-75">Proposal Bab 1</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-xs opacity-50 sm:hidden"></i>
                                </button>
                                <button type="button" 
                                        @click="calcTahap = 'bimbingan'"
                                        :class="calcTahap === 'bimbingan' ? 'bg-brand-600 text-white font-bold border-brand-400' : 'bg-slate-800/80 text-slate-300 border-slate-700 hover:bg-slate-800'"
                                        class="p-3 rounded-xl border text-left transition flex items-center justify-between sm:block">
                                    <div>
                                        <p class="font-bold">2. Sedang Bimbingan</p>
                                        <span class="text-[10px] opacity-75">Bab 2 s/d Bab 3</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-xs opacity-50 sm:hidden"></i>
                                </button>
                                <button type="button" 
                                        @click="calcTahap = 'sempro'"
                                        :class="calcTahap === 'sempro' ? 'bg-brand-600 text-white font-bold border-brand-400' : 'bg-slate-800/80 text-slate-300 border-slate-700 hover:bg-slate-800'"
                                        class="p-3 rounded-xl border text-left transition flex items-center justify-between sm:block">
                                    <div>
                                        <p class="font-bold">3. Selesai Sempro</p>
                                        <span class="text-[10px] opacity-75">Pengambilan Data Bab 4</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-xs opacity-50 sm:hidden"></i>
                                </button>
                                <button type="button" 
                                        @click="calcTahap = 'sidang'"
                                        :class="calcTahap === 'sidang' ? 'bg-brand-600 text-white font-bold border-brand-400' : 'bg-slate-800/80 text-slate-300 border-slate-700 hover:bg-slate-800'"
                                        class="p-3 rounded-xl border text-left transition flex items-center justify-between sm:block">
                                    <div>
                                        <p class="font-bold">4. Naskah Selesai</p>
                                        <span class="text-[10px] opacity-75">Menuju Sidang Akhir</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-xs opacity-50 sm:hidden"></i>
                                </button>
                            </div>
                        </div>

                        <!-- SKS Slider -->
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-2">
                                <span class="text-slate-300">Total SKS Lulus:</span>
                                <span class="text-brand-400 text-sm font-bold" x-text="calcSks + ' SKS'"></span>
                            </div>
                            <input type="range" min="100" max="144" step="2" x-model="calcSks" 
                                   class="w-full accent-brand-500 cursor-pointer">
                            <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                                <span>100 SKS (Persiapan)</span>
                                <span>110 SKS (Syarat Min.)</span>
                                <span>144 SKS (Lengkap)</span>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Result Card -->
                    <div class="lg:col-span-6 bg-slate-800/90 rounded-2xl p-5 sm:p-7 border border-slate-700/80 backdrop-blur-md">
                        
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-700">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-flag-checkered"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-base">Hasil Kalkulasi & Estimasi</h4>
                                <p class="text-xs text-slate-400">Berdasarkan ritme bimbingan normal UMB</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Milestone 1 -->
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Estimasi Seminar Proposal</p>
                                    <h5 class="text-sm font-bold text-white mt-0.5">
                                        <span x-show="calcTahap === 'judul'">6 - 8 Minggu ke Depan</span>
                                        <span x-show="calcTahap === 'bimbingan'">3 - 4 Minggu ke Depan</span>
                                        <span x-show="calcTahap === 'sempro' || calcTahap === 'sidang'" class="text-emerald-400">✓ Telah Selesai</span>
                                    </h5>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-semibold bg-brand-500/20 text-brand-300">Tahap 1</span>
                            </div>

                            <!-- Milestone 2 -->
                            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Estimasi Ujian Sidang Skripsi</p>
                                    <h5 class="text-sm font-bold text-white mt-0.5">
                                        <span x-show="calcTahap === 'judul'">14 - 18 Minggu (Semester Ini)</span>
                                        <span x-show="calcTahap === 'bimbingan'">8 - 12 Minggu</span>
                                        <span x-show="calcTahap === 'sempro'">4 - 6 Minggu</span>
                                        <span x-show="calcTahap === 'sidang'" class="text-amber-400">1 - 2 Minggu (Siap Daftar)</span>
                                    </h5>
                                </div>
                                <span class="px-2 py-1 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-300">Tahap 2</span>
                            </div>

                            <!-- Syarat SKS alert -->
                            <div class="p-3.5 rounded-xl text-xs flex items-start gap-2.5"
                                 :class="calcSks >= 110 ? 'bg-emerald-950/40 border border-emerald-800/60 text-emerald-200' : 'bg-amber-950/40 border border-amber-800/60 text-amber-200'">
                                <i class="fa-solid mt-0.5" :class="calcSks >= 110 ? 'fa-circle-check text-emerald-400' : 'fa-triangle-exclamation text-amber-400'"></i>
                                <div>
                                    <span class="font-bold" x-text="calcSks >= 110 ? 'Status SKS: Memenuhi Syarat Minimal' : 'Status SKS: Perlu Konsultasi KRS'"></span>
                                    <p class="text-[11px] opacity-80 mt-0.5" 
                                       x-text="calcSks >= 110 ? 'Anda sudah berhak secara akademik mengajukan judul dan menjadwalkan seminar.' : 'Minimal SKS adalah 110. Pastikan nilai semester berjalan sudah diinput dosen wali.'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-700/80">
                            <a href="<?= url('/login') ?>" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-lg transition">
                                <span>Daftarkan Judul Sekarang</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- ================= PUSAT UNDUHAN & TEMPLATE RESMI ================= -->
    <section id="panduan" class="py-12 sm:py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-heading tracking-tight mb-3 sm:mb-4">
                    Pedoman & Template Naskah Skripsi
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                    Unduh format resmi penulisan proposal, lembar pengesahan, dan buku pedoman skripsi edisi terbaru Fakultas Teknik UMB.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                
                <!-- Dok 1 -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-brand-300 hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl mb-4">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Edisi 2026 • PDF</span>
                        <h4 class="font-bold text-slate-900 text-sm mt-1 mb-2">Buku Pedoman Skripsi & Tugas Akhir</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Standar tata tulis, format sitasi IEEE/APA, dan etika penelitian akademik.
                        </p>
                    </div>
                    <button type="button" 
                            @click="showToast('Mengunduh Buku Pedoman Skripsi 2026 (PDF)...')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 text-xs font-bold transition">
                        <i class="fa-solid fa-download"></i> Unduh Pedoman
                    </button>
                </div>

                <!-- Dok 2 -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-brand-300 hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4">
                            <i class="fa-solid fa-file-word"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Template Resmi • DOCX</span>
                        <h4 class="font-bold text-slate-900 text-sm mt-1 mb-2">Template Dokumen Proposal Skripsi</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Template siap pakai lengkap dengan styling judul, daftar isi, tabel, dan gambar.
                        </p>
                    </div>
                    <button type="button" 
                            @click="showToast('Mengunduh Template Proposal Skripsi (DOCX)...')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 text-xs font-bold transition">
                        <i class="fa-solid fa-download"></i> Unduh Template
                    </button>
                </div>

                <!-- Dok 3 -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-brand-300 hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format ACC • DOCX</span>
                        <h4 class="font-bold text-slate-900 text-sm mt-1 mb-2">Lembar Persetujuan Sidang Pembimbing</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Formulir tanda tangan rekomendasi siap sidang dari Pembimbing 1 dan 2.
                        </p>
                    </div>
                    <button type="button" 
                            @click="showToast('Mengunduh Lembar Persetujuan Sidang...')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 text-xs font-bold transition">
                        <i class="fa-solid fa-download"></i> Unduh Formulir
                    </button>
                </div>

                <!-- Dok 4 -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-brand-300 hover:shadow-card transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format Jurnal • DOCX</span>
                        <h4 class="font-bold text-slate-900 text-sm mt-1 mb-2">Template Artikel Ilmiah Jurnal UMB</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Format ringkasan skripsi untuk syarat publikasi jurnal nasional terakreditasi.
                        </p>
                    </div>
                    <button type="button" 
                            @click="showToast('Mengunduh Template Artikel Jurnal...')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 text-xs font-bold transition">
                        <i class="fa-solid fa-download"></i> Unduh Template
                    </button>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= FREQUENTLY ASKED QUESTIONS (FAQ) ================= -->
    <section id="faq" class="py-12 sm:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-10 sm:mb-14">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-heading tracking-tight mb-3 sm:mb-4">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                    Jawaban singkat untuk kendala teknis dan pertanyaan seputar tahapan skripsi di SIM Skripsi UMB.
                </p>
            </div>

            <!-- FAQ List -->
            <div class="space-y-3">
                
                <!-- FAQ 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                    <button type="button" 
                            @click="faqOpen = (faqOpen === 1 ? null : 1)"
                            class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50 transition gap-3">
                        <span>Bagaimana jika judul yang saya ajukan ditolak oleh Kaprodi?</span>
                        <i class="fa-solid text-slate-400 text-sm transition-transform duration-200 shrink-0" 
                           :class="faqOpen === 1 ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-cloak x-show="faqOpen === 1" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-slate-600 text-xs sm:text-sm leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        Anda dapat melihat catatan alasan penolakan langsung di modul Pengajuan Judul. Mahasiswa berhak merevisi draf judul atau mengajukan usulan judul baru tanpa dipungut biaya tambahan.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                    <button type="button" 
                            @click="faqOpen = (faqOpen === 2 ? null : 2)"
                            class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50 transition gap-3">
                        <span>Berapa kali minimal bimbingan sebelum boleh mendaftar Seminar Proposal?</span>
                        <i class="fa-solid text-slate-400 text-sm transition-transform duration-200 shrink-0" 
                           :class="faqOpen === 2 ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-cloak x-show="faqOpen === 2" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-slate-600 text-xs sm:text-sm leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        Sesuai standar operasional Fakultas Teknik UMB, mahasiswa wajib memiliki minimal 8 sesi bimbingan terverifikasi oleh Pembimbing 1 dan minimal 8 sesi oleh Pembimbing 2 yang tercatat pada logbook sistem.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                    <button type="button" 
                            @click="faqOpen = (faqOpen === 3 ? null : 3)"
                            class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50 transition gap-3">
                        <span>Bagaimana cara masuk jika saya lupa password akun SSO saya?</span>
                        <i class="fa-solid text-slate-400 text-sm transition-transform duration-200 shrink-0" 
                           :class="faqOpen === 3 ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-cloak x-show="faqOpen === 3" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-slate-600 text-xs sm:text-sm leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        Karena sistem ini menggunakan Single Sign-On (SSO) terpadu dengan SIM Mahasiswa UMB, silakan lakukan reset kata sandi melalui portal utama SIM Mahasiswa atau hubungi Pusat Teknologi Informasi (BAPSI/ICT) UMB.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                    <button type="button" 
                            @click="faqOpen = (faqOpen === 4 ? null : 4)"
                            class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50 transition gap-3">
                        <span>Berapa batas maksimal nilai similarity Turnitin yang diizinkan?</span>
                        <i class="fa-solid text-slate-400 text-sm transition-transform duration-200 shrink-0" 
                           :class="faqOpen === 4 ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-cloak x-show="faqOpen === 4" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-slate-600 text-xs sm:text-sm leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        Batas toleransi plagiarisme Turnitin untuk Seminar Proposal adalah maksimal 25%, sedangkan untuk Sidang Skripsi dan unggah repositori akhir adalah maksimal 20% (tidak termasuk daftar pustaka dan lampiran).
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                    <button type="button" 
                            @click="faqOpen = (faqOpen === 5 ? null : 5)"
                            class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base hover:bg-slate-50 transition gap-3">
                        <span>Apakah bimbingan tatap muka (luring) masih diperbolehkan?</span>
                        <i class="fa-solid text-slate-400 text-sm transition-transform duration-200 shrink-0" 
                           :class="faqOpen === 5 ? 'fa-chevron-up text-brand-600' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-cloak x-show="faqOpen === 5" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 pt-1 text-slate-600 text-xs sm:text-sm leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        Sangat diperbolehkan. Setelah berkonsultasi luring dengan dosen pembimbing, mahasiswa tetap diwajibkan mencatat poin hasil diskusi ke dalam logbook sistem agar dosen dapat memberikan validasi ACC digital.
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= TIM PENGEMBANGAN (KREATOR DI BALIK SIM MAHASISWA) ================= -->
    <section id="tim" class="py-16 sm:py-24 bg-white border-t border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header (Sesuai Gambar 2) -->
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 font-heading tracking-tight mb-3 sm:mb-4">
                    Kreator di Balik SIM Mahasiswa
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed max-w-2xl mx-auto">
                    Sistem Informasi Manajemen Mahasiswa ini dirancang dan dikembangkan dengan dedikasi tinggi oleh mahasiswa Fakultas Teknik Universitas Muhammadiyah Bengkulu melalui kolaborasi lintas teknologi modern.
                </p>
            </div>

            <!-- Big Rounded Image Showcase with Auto-slideshow (assets/person: 1.jpeg, 2.jpeg, 3.jpeg) -->
            <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-slate-900 h-[380px] sm:h-[480px] lg:h-[540px]">
                
                <!-- Slides Container -->
                <template x-for="(slide, index) in personSlides" :key="index">
                    <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                         :class="activePersonSlide === index ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                        <img :src="slide" 
                             alt="Tim Pengembang SIM Mahasiswa Fakultas Teknik UMB" 
                             class="w-full h-full object-cover object-top sm:object-center transition-transform duration-[6000ms] ease-out"
                             :class="activePersonSlide === index ? 'scale-105' : 'scale-100'">
                    </div>
                </template>

                <!-- Gradient Overlay: menjaga foto tetap jelas dan teks terbaca -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-slate-950/30 pointer-events-none"></div>

                <!-- Slide Navigation Dots (Pojok Kanan Atas) -->
                <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20 flex items-center gap-1.5 bg-slate-950/60 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20 shadow-md">
                    <template x-for="(slide, index) in personSlides" :key="index">
                        <button type="button" 
                                @click="activePersonSlide = index"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="activePersonSlide === index ? 'w-6 bg-emerald-400' : 'w-2 bg-white/50 hover:bg-white/80'"
                                :aria-label="'Slide Tim ' + (index + 1)"></button>
                    </template>
                </div>

                <!-- Manual Navigation Arrows (Kiri & Kanan) -->
                <button type="button" 
                        @click="prevPersonSlide()"
                        aria-label="Foto Sebelumnya"
                        class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-900/40 hover:bg-slate-900/80 text-white border border-white/20 backdrop-blur-md flex items-center justify-center text-xs sm:text-sm transition duration-200">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" 
                        @click="nextPersonSlide()"
                        aria-label="Foto Selanjutnya"
                        class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-900/40 hover:bg-slate-900/80 text-white border border-white/20 backdrop-blur-md flex items-center justify-center text-xs sm:text-sm transition duration-200">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

              

            </div>

            <!-- 3 Floating Explanation Cards Overlapping Bottom (Sesuai Gambar 2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6 lg:gap-8 -mt-16 sm:-mt-24 lg:-mt-28 relative z-20 px-2 sm:px-4">
                
                <!-- Card 1: Egi Putu Wijaya -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/70 border border-slate-100 flex flex-col justify-between text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div>
                        <h4 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900 mb-3">
                            Egi Putu Wijaya
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Bertanggung jawab atas arsitektur sistem, basis data, dan antarmuka pengguna secara keseluruhan.
                        </p>
                    </div>
                    <div class="pt-6 mt-auto">
                        <a href="https://github.com/egiputuwijaya" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           title="Kunjungi GitHub Egi Putu Wijaya"
                           aria-label="GitHub Egi Putu Wijaya"
                           class="w-11 h-11 rounded-full bg-slate-100 hover:bg-slate-900 text-slate-500 hover:text-white border border-slate-200 shadow-xs inline-flex items-center justify-center text-lg transition-all duration-200 hover:scale-110">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Khairul Pratama -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/70 border border-slate-100 flex flex-col justify-between text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div>
                        <h4 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900 mb-3">
                            Khairul Pratama
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Fokus pada perancangan basis data, integrasi sistem, dan manajemen keamanan backend.
                        </p>
                    </div>
                    <div class="pt-6 mt-auto">
                        <a href="https://github.com/khairulpratama12340" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           title="Kunjungi GitHub Khairul Pratama"
                           aria-label="GitHub Khairul Pratama"
                           class="w-11 h-11 rounded-full bg-slate-100 hover:bg-slate-900 text-slate-500 hover:text-white border border-slate-200 shadow-xs inline-flex items-center justify-center text-lg transition-all duration-200 hover:scale-110">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 3: Rahman Badio -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/70 border border-slate-100 flex flex-col justify-between text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div>
                        <h4 class="font-heading font-extrabold text-xl sm:text-2xl text-slate-900 mb-3">
                            Rahman Badio
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Membangun komponen antarmuka yang responsif, modern, dan memberikan pengalaman pengguna yang optimal.
                        </p>
                    </div>
                    <div class="pt-6 mt-auto">
                        <a href="https://github.com/erxanz" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           title="Kunjungi GitHub Rahman Badio"
                           aria-label="GitHub Rahman Badio"
                           class="w-11 h-11 rounded-full bg-slate-100 hover:bg-slate-900 text-slate-500 hover:text-white border border-slate-200 shadow-xs inline-flex items-center justify-center text-lg transition-all duration-200 hover:scale-110">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-slate-900 text-slate-400 text-sm border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-10">
                
                <!-- Col 1: Identity -->
                <div class="sm:col-span-2 lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white p-1.5 flex items-center justify-center shadow">
                            <img src="<?= url('assets/logoumb.png') ?>" alt="Logo UMB" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base font-heading">SIM Skripsi UMB</h3>
                            <p class="text-xs text-slate-400">Universitas Muhammadiyah Bengkulu</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Sistem Informasi Manajemen Skripsi dan Tugas Akhir Fakultas Teknik Universitas Muhammadiyah Bengkulu. Mendukung tata kelola akademik yang transparan, terintegrasi, dan tepat waktu.
                    </p>
                    
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3 sm:mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#beranda" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#alur" class="hover:text-white transition">Alur 5 Tahap</a></li>
                        <li><a href="#fitur" class="hover:text-white transition">Fitur Unggulan</a></li>
                        <li><a href="#topik" class="hover:text-white transition">Inspirasi Riset</a></li>
                        <li><a href="#kalkulator" class="hover:text-white transition">Kalkulator Waktu</a></li>
                        <li><a href="#panduan" class="hover:text-white transition">Unduh Panduan</a></li>
                        <li><a href="#tim" class="hover:text-white transition">Tim Pengembang</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Terkait -->
                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3 sm:mb-4">Layanan Kampus</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="https://umb.ac.id" target="_blank" rel="noreferrer" class="hover:text-white transition">Portal Resmi UMB</a></li>
                        <li><a href="#" class="hover:text-white transition">SIM Mahasiswa Terpadu</a></li>
                        <li><a href="#" class="hover:text-white transition">Perpustakaan & Repositori</a></li>
                        <li><a href="#" class="hover:text-white transition">Lembaga Penjaminan Mutu</a></li>
                        <li><a href="#" class="hover:text-white transition">Jurnal Ilmiah Fakultas</a></li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Alamat -->
                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3 sm:mb-4">Sekretariat</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        Gedung Rektorat Kampus I UMB<br>
                        Jl. Jalan Bali, Kota Bengkulu, 38119
                    </p>
                    <div class="space-y-1.5 text-xs">
                        <p><i class="fa-solid fa-envelope text-slate-500 mr-1.5"></i> ft@umb.ac.id</p>
                        <p><i class="fa-solid fa-phone text-slate-500 mr-1.5"></i> (0736) 22765</p>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="mt-10 sm:mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3 sm:gap-4 text-center sm:text-left">
                <p>&copy; <?= date('Y') ?> SIM Skripsi — Universitas Muhammadiyah Bengkulu. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="#beranda" class="hover:text-slate-300 transition">Kembali ke Atas <i class="fa-solid fa-arrow-up ml-1"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ================= QUICK LOGIN MODAL ================= -->
    <div x-cloak x-show="quickLoginModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="quickLoginModal = false"
             class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 max-w-lg w-full shadow-2xl border border-slate-200 relative overflow-hidden max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-tight">Uji Coba Cepat (Quick Demo)</h3>
                        <p class="text-[11px] sm:text-xs text-slate-500">Pilih peran akun untuk langsung menguji sistem</p>
                    </div>
                </div>
                <button type="button" @click="quickLoginModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600 mb-3.5 leading-relaxed shrink-0">
                Anda dapat mencoba seluruh modul SIM Skripsi tanpa perlu memasukkan password secara manual:
            </p>

            <div class="space-y-2 sm:space-y-2.5 overflow-y-auto max-h-[55vh] pr-1">
                <!-- Role Mahasiswa -->
                <a href="<?= url('/quick-login/mahasiswa') ?>" 
                   class="flex items-center justify-between p-3 sm:p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:bg-brand-50/50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-brand-700 transition">Mahasiswa</h4>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">NIM: 20260001 (Ahmad Fauzan)</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-brand-600 group-hover:translate-x-1 transition-transform">
                        Masuk &rarr;
                    </span>
                </a>

                <!-- Role Dosen -->
                <a href="<?= url('/quick-login/dosen') ?>" 
                   class="flex items-center justify-between p-3 sm:p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:bg-brand-50/50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-brand-700 transition">Dosen Pembimbing</h4>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">Dr. Budi Santoso, M.Kom.</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-brand-600 group-hover:translate-x-1 transition-transform">
                        Masuk &rarr;
                    </span>
                </a>

                <!-- Role Kaprodi -->
                <a href="<?= url('/quick-login/kaprodi') ?>" 
                   class="flex items-center justify-between p-3 sm:p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:bg-brand-50/50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-brand-700 transition">Ketua Program Studi (Kaprodi)</h4>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">Verifikasi judul & penetapan pembimbing</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-brand-600 group-hover:translate-x-1 transition-transform">
                        Masuk &rarr;
                    </span>
                </a>

                <!-- Role Admin -->
                <a href="<?= url('/quick-login/admin') ?>" 
                   class="flex items-center justify-between p-3 sm:p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:bg-brand-50/50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fa-solid fa-gear"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-brand-700 transition">Administrator SIM</h4>
                            <p class="text-[10px] sm:text-[11px] text-slate-500">Akses seluruh modul & konfigurasi</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-brand-600 group-hover:translate-x-1 transition-transform">
                        Masuk &rarr;
                    </span>
                </a>
            </div>

            <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-0 shrink-0">
                <a href="<?= url('/login') ?>" class="text-xs font-semibold text-brand-700 hover:underline">
                    Atau gunakan Form Login SSO biasa
                </a>
                <button type="button" @click="quickLoginModal = false" class="text-xs font-medium text-slate-500 hover:text-slate-800">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</body>
</html>
