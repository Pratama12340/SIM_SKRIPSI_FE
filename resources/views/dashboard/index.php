<?php
ob_start();
$userProfile = $_SESSION['user'] ?? [];
$userName = $userProfile['nama'] ?? 'Pengguna';
$userRoleCode = strtoupper((string)($userProfile['role'] ?? 'MAHASISWA'));
?>

<div class="space-y-6">

    <!-- Welcome Banner (Persis gaya SIM_MAHASISWA_FE) -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold tracking-wide uppercase mb-3 backdrop-blur-sm">
                <i class="fa-solid fa-graduation-cap"></i> Modul SIM Skripsi
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">
                Selamat Datang, <?= htmlspecialchars($userName) ?>! 👋
            </h1>
            <p class="text-blue-100 text-sm leading-relaxed">
                Anda masuk sebagai <strong class="text-white"><?= htmlspecialchars($userRoleCode) ?></strong>. 
                Sistem ini terhubung langsung dengan SSO SIM Mahasiswa untuk memantau progres tahapan skripsi Anda secara berkala.
            </p>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pengajuan</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['total_pengajuan'] ?? 0 ?></h3>
                <span class="text-xs text-blue-600 font-medium">Judul Penelitian</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Disetujui</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1"><?= $stats['disetujui'] ?? 0 ?></h3>
                <span class="text-xs text-emerald-600 font-medium">Siap Bimbingan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Logbook Bimbingan</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['total_bimbingan'] ?? 0 ?></h3>
                <span class="text-xs text-amber-600 font-medium">Konsultasi Dosen</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Seminar & Sidang</p>
                <h3 class="text-2xl font-bold text-purple-600 mt-1"><?= $stats['total_sidang'] ?? 0 ?></h3>
                <span class="text-xs text-purple-600 font-medium">Sempro / Sidang</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Quick Action & Tahapan Skripsi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Tabel Ringkasan Pengajuan Terakhir (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Pengajuan Skripsi Terkini</h3>
                    <p class="text-xs text-gray-500">Status perkembangan draf judul proposal</p>
                </div>
                <a href="<?= url('/pengajuan') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <?php if (empty($recentPengajuan)): ?>
                <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <i class="fa-solid fa-file-circle-question text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm font-semibold text-gray-600">Belum ada pengajuan skripsi</p>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Mulai ajukan draf judul dan unggah proposal Anda sekarang.</p>
                    <?php if ($userRoleCode === 'MAHASISWA'): ?>
                    <a href="<?= url('/pengajuan/tambah') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                        <i class="fa-solid fa-plus"></i> Ajukan Judul
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-100">
                            <tr>
                                <th class="py-3 px-4">Judul Penelitian</th>
                                <th class="py-3 px-4">Mahasiswa / NIM</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($recentPengajuan as $p): ?>
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="py-3.5 px-4 font-semibold text-gray-800 max-w-xs truncate">
                                    <?= htmlspecialchars($p['judul']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                    <?= htmlspecialchars($p['nama_mahasiswa']) ?> <br>
                                    <span class="text-gray-400"><?= htmlspecialchars($p['nim']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <?php
                                    $badgeColors = [
                                        'draft'     => 'bg-gray-100 text-gray-700',
                                        'diajukan'  => 'bg-amber-100 text-amber-800',
                                        'disetujui' => 'bg-emerald-100 text-emerald-800',
                                        'ditolak'   => 'bg-rose-100 text-rose-800'
                                    ];
                                    $badge = $badgeColors[$p['status']] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $badge ?>">
                                        <?= htmlspecialchars($p['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="<?= url('/pengajuan/detail/' . $p['id']) ?>" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold transition">
                                        <span>Detail</span> <i class="fa-solid fa-angle-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan: Panduan Alur Skripsi -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-gray-800 text-base">Alur Layanan Skripsi</h3>
            <p class="text-xs text-gray-500">Tahapan resmi penyelesaian tugas akhir:</p>

            <div class="space-y-3 pt-2">
                <!-- Tahap 1 -->
                <div class="flex items-start gap-3 p-3 rounded-lg bg-blue-50/60 border border-blue-100">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0">1</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-800">Pengajuan Judul Proposal</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Unggah draf naskah proposal PDF & menunggu validasi Kaprodi.</p>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="flex items-start gap-3 p-3 rounded-lg bg-emerald-50/60 border border-emerald-100">
                    <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shrink-0">2</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-800">Bimbingan & Logbook</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Konsultasi rutin dengan Pembimbing 1 & 2 hingga status ACC.</p>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="flex items-start gap-3 p-3 rounded-lg bg-purple-50/60 border border-purple-100">
                    <div class="w-7 h-7 rounded-full bg-purple-600 text-white font-bold flex items-center justify-center text-xs shrink-0">3</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-800">Seminar & Sidang Akhir</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Pendaftaran sempro, ujian kelayakan, dan sidang munaqasyah.</p>
                    </div>
                </div>
            </div>

            <?php if ($userRoleCode === 'MAHASISWA'): ?>
            <div class="pt-2">
                <a href="<?= url('/pengajuan/tambah') ?>" 
                   class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Ajukan Judul Sekarang</span>
                </a>
            </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
