<?php
ob_start();
$userProfile = $_SESSION['user'] ?? [];
$userRoleCode = strtoupper((string)($userProfile['role'] ?? 'MAHASISWA'));
$isDosenOrAdmin = in_array($userRoleCode, ['DOSEN', 'KAPRODI', 'ADMIN_PRODI', 'ADMIN']);
?>

<div class="space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= url('/pengajuan') ?>" class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Detail Pengajuan Skripsi</h1>
                <p class="text-xs text-gray-500">ID Pengajuan #<?= $pengajuan['id'] ?> — Terdaftar pada <?= date('d M Y, H:i', strtotime($pengajuan['created_at'])) ?></p>
            </div>
        </div>

        <div>
            <?php
            $badgeColors = [
                'draft'     => 'bg-gray-100 text-gray-700',
                'diajukan'  => 'bg-amber-100 text-amber-800 border border-amber-200',
                'disetujui' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                'ditolak'   => 'bg-rose-100 text-rose-800 border border-rose-200'
            ];
            $badge = $badgeColors[$pengajuan['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider <?= $badge ?>">
                Status: <?= htmlspecialchars($pengajuan['status']) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri & Tengah: Detail Skripsi (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card Informasi Utama -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-5">
                <div>
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Judul Penelitian</span>
                    <h2 class="text-lg font-extrabold text-gray-800 mt-1 leading-snug">
                        <?= htmlspecialchars($pengajuan['judul']) ?>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3 border-y border-gray-100 text-xs">
                    <div>
                        <span class="text-gray-400 font-medium">Mahasiswa Pengusul:</span>
                        <p class="font-bold text-gray-800 mt-0.5"><?= htmlspecialchars($pengajuan['nama_mahasiswa']) ?> (<?= htmlspecialchars($pengajuan['nim']) ?>)</p>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium">Bidang Kajian:</span>
                        <p class="font-bold text-gray-800 mt-0.5"><?= htmlspecialchars($pengajuan['bidang_kajian'] ?: '-') ?></p>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Abstrak & Latar Belakang</span>
                    <div class="mt-2 p-4 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($pengajuan['abstrak'] ?: 'Tidak ada ringkasan abstrak yang disertakan.') ?>
                    </div>
                </div>

                <!-- Berkas Proposal -->
                <div>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Dokumen Naskah Proposal</span>
                    <div class="mt-2 flex items-center justify-between p-4 rounded-xl bg-blue-50/50 border border-blue-100">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-pdf text-2xl text-red-500"></i>
                            <div>
                                <p class="text-xs font-bold text-gray-800">
                                    <?= htmlspecialchars($pengajuan['file_proposal'] ?: 'Belum ada file diunggah') ?>
                                </p>
                                <span class="text-[11px] text-gray-400">Format PDF Resmi</span>
                            </div>
                        </div>
                        <?php if (!empty($pengajuan['file_proposal'])): ?>
                        <a href="http://localhost:5000/uploads/<?= htmlspecialchars($pengajuan['file_proposal']) ?>" target="_blank"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-arrow-down"></i>
                            <span>Buka / Unduh</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Catatan Verifikasi -->
                <?php if (!empty($pengajuan['catatan_verifikasi'])): ?>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                    <span class="font-bold text-amber-900 block mb-1"><i class="fa-solid fa-triangle-exclamation"></i> Catatan Verifikator Prodi:</span>
                    <p class="text-amber-800 leading-relaxed"><?= htmlspecialchars($pengajuan['catatan_verifikasi']) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Card Riwayat Logbook Terkait -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Logbook Bimbingan Skripsi Ini</h3>
                        <p class="text-xs text-gray-500">Total <?= count($pengajuan['logbooks'] ?? []) ?> konsultasi tercatat</p>
                    </div>
                    <?php if ($pengajuan['status'] === 'disetujui' && $userRoleCode === 'MAHASISWA'): ?>
                    <a href="<?= url('/bimbingan/tambah?skripsi_id=' . $pengajuan['id']) ?>" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Bimbingan
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($pengajuan['logbooks'])): ?>
                    <p class="text-xs text-gray-400 italic py-4 text-center">Belum ada riwayat bimbingan untuk skripsi ini.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pengajuan['logbooks'] as $b): ?>
                        <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 flex items-start justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-gray-800"><?= date('d F Y', strtotime($b['tanggal_bimbingan'])) ?></span> — Dosen: <?= htmlspecialchars($b['dosen_nama'] ?: $b['dosen_nip']) ?>
                                <p class="text-gray-600 mt-1"><?= htmlspecialchars($b['catatan_mahasiswa']) ?></p>
                                <?php if ($b['catatan_dosen']): ?>
                                    <div class="mt-2 text-emerald-700 bg-emerald-50 p-2 rounded-lg font-medium">
                                        <strong>Catatan Dosen:</strong> <?= htmlspecialchars($b['catatan_dosen']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 <?= $b['status_acc'] === 'acc' ? 'bg-emerald-100 text-emerald-800' : ($b['status_acc'] === 'revisi' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') ?>">
                                <?= $b['status_acc'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Kolom Kanan: Dosen Pembimbing & Form Verifikasi (1 Kolom) -->
        <div class="space-y-6">

            <!-- Card Dosen Pembimbing Terplot -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 text-xs">
                <h3 class="font-bold text-gray-800 text-sm">Dosen Pembimbing</h3>
                
                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase">Pembimbing Utama (1)</span>
                    <p class="font-bold text-gray-800 mt-0.5 text-sm">
                        <?= htmlspecialchars($pengajuan['dosen_pembimbing_1_nama'] ?: ($pengajuan['dosen_pembimbing_1_nip'] ?: 'Belum ditetapkan')) ?>
                    </p>
                    <?php if ($pengajuan['dosen_pembimbing_1_nip']): ?>
                        <span class="text-gray-500 font-mono text-[11px]">NIP: <?= htmlspecialchars($pengajuan['dosen_pembimbing_1_nip']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase">Pembimbing Pendamping (2)</span>
                    <p class="font-bold text-gray-800 mt-0.5 text-sm">
                        <?= htmlspecialchars($pengajuan['dosen_pembimbing_2_nama'] ?: ($pengajuan['dosen_pembimbing_2_nip'] ?: 'Belum ditetapkan')) ?>
                    </p>
                    <?php if ($pengajuan['dosen_pembimbing_2_nip']): ?>
                        <span class="text-gray-500 font-mono text-[11px]">NIP: <?= htmlspecialchars($pengajuan['dosen_pembimbing_2_nip']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel Verifikasi (Hanya Dosen / Kaprodi / Admin) -->
            <?php if ($isDosenOrAdmin): ?>
            <div class="bg-white rounded-2xl border border-blue-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 text-blue-600 font-bold text-sm">
                    <i class="fa-solid fa-stamp"></i>
                    <span>Verifikasi Judul & Plotting Pembimbing</span>
                </div>
                <p class="text-xs text-gray-500">Tentukan persetujuan judul proposal dan tunjuk dosen pembimbing resmi.</p>

                <form method="POST" action="<?= url('/pengajuan/verifikasi/' . $pengajuan['id']) ?>" class="space-y-4 text-xs">
                    
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1.5">Keputusan Status</label>
                        <select name="status" class="w-full p-2.5 rounded-xl border border-gray-200 font-semibold bg-white focus:border-blue-500">
                            <option value="disetujui" <?= $pengajuan['status'] === 'disetujui' ? 'selected' : '' ?>>Disetujui (ACC Judul)</option>
                            <option value="ditolak" <?= $pengajuan['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            <option value="diajukan" <?= $pengajuan['status'] === 'diajukan' ? 'selected' : '' ?>>Menunggu Verifikasi (Pending)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1.5">Dosen Pembimbing 1</label>
                        <input type="text" name="dosen_pembimbing_1_nama" placeholder="Nama Lengkap & Gelar Pembimbing 1"
                               value="<?= htmlspecialchars($pengajuan['dosen_pembimbing_1_nama'] ?? '') ?>"
                               class="w-full p-2.5 rounded-xl border border-gray-200 mb-1.5">
                        <input type="text" name="dosen_pembimbing_1_nip" placeholder="NIP Dosen Pembimbing 1"
                               value="<?= htmlspecialchars($pengajuan['dosen_pembimbing_1_nip'] ?? '') ?>"
                               class="w-full p-2.5 rounded-xl border border-gray-200">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1.5">Dosen Pembimbing 2</label>
                        <input type="text" name="dosen_pembimbing_2_nama" placeholder="Nama Lengkap & Gelar Pembimbing 2"
                               value="<?= htmlspecialchars($pengajuan['dosen_pembimbing_2_nama'] ?? '') ?>"
                               class="w-full p-2.5 rounded-xl border border-gray-200 mb-1.5">
                        <input type="text" name="dosen_pembimbing_2_nip" placeholder="NIP Dosen Pembimbing 2"
                               value="<?= htmlspecialchars($pengajuan['dosen_pembimbing_2_nip'] ?? '') ?>"
                               class="w-full p-2.5 rounded-xl border border-gray-200">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1.5">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" rows="3" placeholder="Masukkan arahan revisi atau pertimbangan judul..."
                                  class="w-full p-2.5 rounded-xl border border-gray-200"><?= htmlspecialchars($pengajuan['catatan_verifikasi'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Verifikasi</span>
                    </button>
                </form>
            </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
