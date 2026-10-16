<?php
ob_start();
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= url('/pengajuan') ?>" class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Ajukan Judul Skripsi Baru</h1>
                <p class="text-xs text-gray-500">Isi formulir pengusulan topik dan unggah berkas naskah proposal Anda</p>
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

        <form method="POST" action="<?= url('/pengajuan/tambah') ?>" enctype="multipart/form-data" class="space-y-6">

            <!-- Program Studi -->
            <?php
            $userProdiId = $_SESSION['user']['prodi_id'] ?? null;
            $matchedProdi = null;
            if (!empty($userProdiId) && !empty($prodiList)) {
                foreach ($prodiList as $p) {
                    if ((string)$p['id'] === (string)$userProdiId) {
                        $matchedProdi = $p;
                        break;
                    }
                }
            }
            ?>
            <div>
                <label for="prodi_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Program Studi Mahasiswa <span class="text-red-500">*</span>
                </label>
                <?php if ($matchedProdi): ?>
                    <input type="hidden" name="prodi_id" value="<?= $matchedProdi['id'] ?>">
                    <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 flex items-center justify-between text-sm">
                        <span class="font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                            <?= htmlspecialchars($matchedProdi['kodeProgramStudi'] . ' - ' . $matchedProdi['namaProgramStudi'] . (!empty($matchedProdi['namaFakultas']) ? ' (' . $matchedProdi['namaFakultas'] . ')' : '')) ?>
                        </span>
                        <span class="text-[11px] font-semibold text-blue-600 bg-white px-2.5 py-1 rounded-lg border border-blue-200">Sesuai Profil SSO</span>
                    </div>
                <?php else: ?>
                    <select id="prodi_id" name="prodi_id" required class="w-full p-3.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white">
                        <option value="">-- Pilih Program Studi --</option>
                        <?php foreach ($prodiList ?? [] as $prodi): ?>
                            <option value="<?= $prodi['id'] ?>" <?= ((string)$userProdiId === (string)$prodi['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prodi['kodeProgramStudi'] . ' - ' . $prodi['namaProgramStudi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Judul Skripsi -->
            <div>
                <label for="judul" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Judul Penelitian Skripsi <span class="text-red-500">*</span>
                </label>
                <textarea id="judul" name="judul" rows="3" required
                          placeholder="Contoh: Rancang Bangun Sistem Informasi Manajemen Skripsi Terintegrasi SSO Menggunakan Arsitektur RESTful API"
                          class="w-full p-3.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"><?= htmlspecialchars($_POST['judul'] ?? '') ?></textarea>
                <p class="text-[11px] text-gray-400 mt-1">Gunakan huruf kapital di awal setiap kata utama sesuai kaidah penulisan ilmiah.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Bidang Kajian -->
                <div>
                    <label for="bidang_kajian" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Bidang Kajian / Konsentrasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="bidang_kajian" name="bidang_kajian" required
                           value="<?= htmlspecialchars($_POST['bidang_kajian'] ?? '') ?>"
                           placeholder="Contoh: Rekayasa Perangkat Lunak, Jaringan, AI"
                           class="w-full p-3.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                </div>

                <!-- Status Pengajuan -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Status Saat Disimpan
                    </label>
                    <select id="status" name="status" class="w-full p-3.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white">
                        <option value="diajukan" selected>Langsung Diajukan ke Prodi (Siap Diverifikasi)</option>
                        <option value="draft">Simpan Sebagai Draft Terlebih Dahulu</option>
                    </select>
                </div>
            </div>

            <!-- Abstrak Proposal -->
            <div>
                <label for="abstrak" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Ringkasan Masalah & Abstrak Proposal
                </label>
                <textarea id="abstrak" name="abstrak" rows="4"
                          placeholder="Jelaskan latar belakang singkat, rumusan masalah, dan tujuan penelitian yang akan dilakukan..."
                          class="w-full p-3.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"><?= htmlspecialchars($_POST['abstrak'] ?? '') ?></textarea>
            </div>

            <!-- Upload File Proposal PDF -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Berkas Naskah Proposal (Format PDF)
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-blue-400 transition bg-gray-50/50">
                    <i class="fa-solid fa-file-pdf text-3xl text-red-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-700 mb-1">Pilih berkas PDF proposal penelitian Anda</p>
                    <p class="text-[11px] text-gray-400 mb-3">Maksimum ukuran file: 15 Megabytes (.pdf)</p>
                    <input type="file" id="file_proposal" name="file_proposal" accept="application/pdf"
                           class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="<?= url('/pengajuan') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pengajuan Judul</span>
                </button>
            </div>

        </form>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
