<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIM Skripsi — Universitas Muhammadiyah Bengkulu' ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1d4ed8', // Corporate Blue
                        secondary: '#f3f4f6',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        [x-cloak] { display: none !important; }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover {
            color: #1d4ed8;
            background-color: #eff6ff;
        }
        .sidebar-item.active { 
            color: #1d4ed8; 
            background-color: #eff6ff;
            font-weight: 500;
        }
        .sidebar-icon {
            color: #6b7280;
        }
        .sidebar-item:hover .sidebar-icon, .sidebar-item.active .sidebar-icon {
            color: #1d4ed8;
        }
    </style>
</head>
<body class="text-gray-800 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

<?php
$userProfile = $_SESSION['user'] ?? [];
$userName = $userProfile['nama'] ?? $userProfile['namaLengkap'] ?? $userProfile['nama_lengkap'] ?? $userProfile['username'] ?? 'Pengguna';
$userRole = is_array($userProfile['role'] ?? null) ? ($userProfile['role']['namaRole'] ?? $userProfile['role']['kodeRole'] ?? 'Mahasiswa') : ($userProfile['role'] ?? 'Mahasiswa');
$userRoleCode = is_array($userProfile['role'] ?? null) ? strtoupper($userProfile['role']['kodeRole'] ?? $userProfile['role']['namaRole'] ?? 'MAHASISWA') : strtoupper((string)$userRole);
$userInitial = strtoupper(substr($userName, 0, 2));

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = basename($currentPath);
if ($currentPath == 'public' || $currentPath == 'index.php' || $currentPath == '') $currentPath = 'dashboard';
?>

    <!-- Sidebar -->
    <aside class="bg-white border-r border-gray-200 flex flex-col shrink-0 transition-all duration-300 relative z-20" :class="sidebarOpen ? 'w-64' : 'w-20'">
        <!-- Toggle button -->
        <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-6 bg-white border border-gray-200 text-gray-400 rounded-full w-6 h-6 flex items-center justify-center hover:text-blue-600 shadow-sm z-50">
            <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right text-xs'"></i>
        </button>

        <div class="h-20 flex items-center border-b border-gray-100 transition-all duration-300" :class="sidebarOpen ? 'px-6' : 'justify-center px-0'">
            <div class="flex items-center gap-3">
                <!-- Logo Circle -->
                <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm shrink-0 p-1">
                    <img src="<?= url('assets/logoumb.png') ?>" alt="Logo UMB" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'w-auto opacity-100' : 'w-0 opacity-0'">
                    <span class="text-[15px] font-bold text-gray-800 leading-tight">SIM Skripsi</span>
                    <span class="text-[11px] font-medium text-blue-600 leading-tight">Universitas Muhammadiyah</span>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-4 space-y-1.5 overflow-x-hidden">
            <!-- Dashboard -->
            <a href="<?= url('/dashboard') ?>" class="sidebar-item <?= strpos($currentPath, 'dashboard') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-gauge w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Pengajuan Judul -->
            <a href="<?= url('/pengajuan') ?>" class="sidebar-item <?= strpos($currentPath, 'pengajuan') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-book-bookmark w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Pengajuan Judul</span>
            </a>

            <!-- Logbook Bimbingan -->
            <a href="<?= url('/bimbingan') ?>" class="sidebar-item <?= strpos($currentPath, 'bimbingan') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-comments w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Logbook Bimbingan</span>
            </a>

            <!-- Seminar & Sidang -->
            <a href="<?= url('/seminar') ?>" class="sidebar-item <?= strpos($currentPath, 'seminar') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-calendar-check w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Seminar & Sidang</span>
            </a>

            <!-- Penilaian Sidang (Dosen, Kaprodi, Admin) -->
            <?php if (in_array($userRoleCode, ['DOSEN', 'KAPRODI', 'ADMIN_PRODI', 'ADMIN'])): ?>
            <a href="<?= url('/nilai') ?>" class="sidebar-item <?= strpos($currentPath, 'nilai') !== false ? 'active' : '' ?> flex items-center py-2.5 rounded-lg text-gray-600 text-[14px]" :class="sidebarOpen ? 'px-3 mx-3 gap-3' : 'justify-center mx-2'">
                <i class="fas fa-award w-5 text-center sidebar-icon text-sm shrink-0"></i>
                <span :class="{'hidden': !sidebarOpen}" class="whitespace-nowrap">Penilaian Sidang</span>
            </a>
            <?php endif; ?>

            <!-- Pemisah Role / Info Cepat -->
            <div class="pt-4 px-4" :class="{'hidden': !sidebarOpen}">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Peralihan Peran Uji</p>
                <div class="grid grid-cols-3 gap-1 text-[11px]">
                    <a href="<?= url('/quick-login/mahasiswa') ?>" class="px-1.5 py-1.5 rounded bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 text-center transition">Mhs</a>
                    <a href="<?= url('/quick-login/dosen') ?>" class="px-1.5 py-1.5 rounded bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 text-center transition">Dosen</a>
                    <a href="<?= url('/quick-login/admin') ?>" class="px-1.5 py-1.5 rounded bg-purple-50 text-purple-700 font-bold hover:bg-purple-100 text-center transition">Admin</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden bg-[#f4f7fb]">
        <!-- Topbar -->
        <header class="bg-white h-20 flex items-center justify-between px-8 border-b border-gray-200 z-10 shrink-0 shadow-sm">
            <!-- Breadcrumb / Title Area -->
            <div class="flex flex-col">
                <h2 class="text-lg font-bold text-gray-800"><?= $title ?? 'Dashboard' ?></h2>
                <p class="text-xs text-gray-500 mt-0.5">SIM Skripsi — Universitas Muhammadiyah Bengkulu</p>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 bg-gray-50 rounded-full pr-4 p-1 border border-gray-100">
                    <div class="w-9 h-9 rounded-full bg-[#1e88e5] text-white flex items-center justify-center text-sm font-semibold shadow-sm">
                        <?= htmlspecialchars($userInitial) ?>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-[13px] font-bold text-gray-800"><?= htmlspecialchars($userName) ?></span>
                        <span class="text-[11px] text-blue-600 font-medium"><?= htmlspecialchars($userRole) ?></span>
                    </div>
                </div>
                
                <a href="<?= url('/logout') ?>" class="bg-[#d32f2f] hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </header>

        <!-- Flash Message Notification -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mx-8 mt-4 p-4 rounded-xl border bg-green-50 border-green-200 text-green-800 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
            <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <?php unset($_SESSION['flash_success']); endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mx-8 mt-4 p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-lg"></i>
            <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <!-- Main Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            <div class="max-w-full mx-auto">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

</body>
</html>
