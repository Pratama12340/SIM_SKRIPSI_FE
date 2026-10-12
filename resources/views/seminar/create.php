<?php
ob_start();
?>

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= url('/seminar') ?>" class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Daftar Seminar / Sidang Skripsi</h1>
                <p class="text-xs text-gray-500">Ajukan permohonan penjadwalan ujian Seminar Proposal, Hasil, atau Sidang Munaqasyah</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        
        <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-lg"></i>
            <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <?php if (empty($mySkripsiList)): ?>
        <div class="p-8 text-center bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-xs">
            <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-600 mb-2"></i>
            <p class="font-bold">Anda belum memiliki judul skripsi yang berstatus disetujui!</p>
            <p class="mt-1">Pendaftaran seminar/sidang hanya dapat dilakukan jika skripsi Anda telah berstatus disetujui.</p>
        </div>
        <?php else: ?>

        <form method="POST" action="<?= url('/seminar/daftar') ?>" enctype="multipart/form-data" class="space-y-6">
            
            <!-- Pilih Skripsi -->
            <div>
                <label for="skripsi_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Skripsi yang Didaftarkan <span class="text-red-500">*</span>
                </label>
                <select id="skripsi_id" name="skripsi_id" required class="w-full p-3.5 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white font-semibold">
                    <?php foreach ($mySkripsiList as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            #<?= $s['id'] ?> - <?= htmlspecialchars($s['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pilih Jenis Sidang -->
            <div>
                <label for="jenis_sidang" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Tahapan / Jenis Ujian <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-4 rounded-xl border border-gray-200 hover:border-blue-500 cursor-pointer flex flex-col items-center text-center gap-2 bg-gray-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 transition">
                        <input type="radio" name="jenis_sidang" value="sempro" checked class="text-blue-600">
                        <span class="font-bold text-gray-800 text-xs">Seminar Proposal</span>
                        <span class="text-[10px] text-gray-400">Ujian Bab 1 s.d. Bab 3</span>
                    </label>

                    <label class="p-4 rounded-xl border border-gray-200 hover:border-blue-500 cursor-pointer flex flex-col items-center text-center gap-2 bg-gray-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 transition">
                        <input type="radio" name="jenis_sidang" value="semhas" class="text-blue-600">
                        <span class="font-bold text-gray-800 text-xs">Seminar Hasil</span>
                        <span class="text-[10px] text-gray-400">Ujian Hasil Penelitian</span>
                    </label>

                    <label class="p-4 rounded-xl border border-gray-200 hover:border-blue-500 cursor-pointer flex flex-col items-center text-center gap-2 bg-gray-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 transition">
                        <input type="radio" name="jenis_sidang" value="sidang_akhir" class="text-blue-600">
                        <span class="font-bold text-gray-800 text-xs">Sidang Akhir</span>
                        <span class="text-[10px] text-gray-400">Munaqasyah Skripsi</span>
                    </label>
                </div>
            </div>

            <!-- Upload File Naskah Lengkap PDF -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Unggah Naskah Lengkap Ujian (Format PDF) <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center bg-gray-50/50 hover:border-blue-400 transition">
                    <i class="fa-solid fa-file-pdf text-3xl text-red-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-700 mb-1">Pilih berkas naskah lengkap yang telah di-ACC Pembimbing</p>
                    <p class="text-[11px] text-gray-400 mb-3">Maksimum ukuran: 15MB (.pdf)</p>
                    <input type="file" name="file_naskah" accept="application/pdf" required
                           class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="<?= url('/seminar') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pendaftaran Sidang</span>
                </button>
            </div>

        </form>

        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
