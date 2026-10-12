<?php
ob_start();
?>

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= url('/bimbingan') ?>" class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Tambah Catatan Bimbingan</h1>
                <p class="text-xs text-gray-500">Catat hasil diskusi dan serahkan berkas bab perbaikan kepada dosen</p>
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
            <p class="mt-1">Bimbingan hanya dapat dicatat setelah judul proposal Anda disetujui oleh program studi.</p>
            <div class="mt-4">
                <a href="<?= url('/pengajuan') ?>" class="px-4 py-2 bg-amber-600 text-white rounded-lg font-bold">Lihat Pengajuan Judul</a>
            </div>
        </div>
        <?php else: ?>

        <form method="POST" action="<?= url('/bimbingan/tambah') ?>" enctype="multipart/form-data" class="space-y-6">
            
            <!-- Pilih Skripsi -->
            <div>
                <label for="skripsi_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Skripsi yang Dikonsultasikan <span class="text-red-500">*</span>
                </label>
                <select id="skripsi_id" name="skripsi_id" required class="w-full p-3.5 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white font-semibold">
                    <?php foreach ($mySkripsiList as $s): ?>
                        <option value="<?= $s['id'] ?>" 
                                data-p1-nip="<?= htmlspecialchars($s['dosen_pembimbing_1_nip'] ?? '') ?>"
                                data-p1-nama="<?= htmlspecialchars($s['dosen_pembimbing_1_nama'] ?? '') ?>"
                                data-p2-nip="<?= htmlspecialchars($s['dosen_pembimbing_2_nip'] ?? '') ?>"
                                data-p2-nama="<?= htmlspecialchars($s['dosen_pembimbing_2_nama'] ?? '') ?>"
                                <?= (isset($_GET['skripsi_id']) && $_GET['skripsi_id'] == $s['id']) ? 'selected' : '' ?>>
                            #<?= $s['id'] ?> - <?= htmlspecialchars($s['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pilih Dosen Pembimbing & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="dosen_select" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Dosen Pembimbing yang Dituju <span class="text-red-500">*</span>
                    </label>
                    <select id="dosen_select" class="w-full p-3.5 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition bg-white font-semibold" onchange="updateDosenFields()">
                        <!-- Pilihan otomatis diupdate via JS -->
                    </select>
                    <!-- Hidden inputs untuk dikirim ke API -->
                    <input type="hidden" id="dosen_nip" name="dosen_nip" value="">
                    <input type="hidden" id="dosen_nama" name="dosen_nama" value="">
                </div>

                <div>
                    <label for="tanggal_bimbingan" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Tanggal Pertemuan / Konsultasi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_bimbingan" name="tanggal_bimbingan" required
                           value="<?= date('Y-m-d') ?>"
                           class="w-full p-3.5 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                </div>
            </div>

            <!-- Catatan Mahasiswa -->
            <div>
                <label for="catatan_mahasiswa" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Materi Bahasan & Progres yang Dikerjakan <span class="text-red-500">*</span>
                </label>
                <textarea id="catatan_mahasiswa" name="catatan_mahasiswa" rows="4" required
                          placeholder="Jelaskan progres bab atau poin revisi yang dikonsultasikan pada bimbingan kali ini..."
                          class="w-full p-3.5 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"></textarea>
            </div>

            <!-- Upload File Draf Revisi PDF -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Unggah Draf Naskah Bab / Revisi (Opsional, PDF)
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 text-center bg-gray-50/50 hover:border-blue-400 transition">
                    <i class="fa-solid fa-file-pdf text-2xl text-red-500 mb-1"></i>
                    <p class="text-xs font-bold text-gray-700">Pilih file PDF draf revisi</p>
                    <p class="text-[11px] text-gray-400 mb-2">Maksimum ukuran: 15MB</p>
                    <input type="file" name="file_revisi" accept="application/pdf"
                           class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="<?= url('/bimbingan') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Simpan Log Bimbingan</span>
                </button>
            </div>

        </form>

        <script>
        function updateDosenFields() {
            var select = document.getElementById('dosen_select');
            var selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt) {
                document.getElementById('dosen_nip').value = selectedOpt.dataset.nip || '';
                document.getElementById('dosen_nama').value = selectedOpt.dataset.nama || '';
            }
        }

        function populateDosenList() {
            var skripsiSelect = document.getElementById('skripsi_id');
            var selectedSkripsi = skripsiSelect.options[skripsiSelect.selectedIndex];
            var dosenSelect = document.getElementById('dosen_select');
            dosenSelect.innerHTML = '';

            if (!selectedSkripsi) return;

            var p1Nip = selectedSkripsi.dataset.p1Nip;
            var p1Nama = selectedSkripsi.dataset.p1Nama;
            var p2Nip = selectedSkripsi.dataset.p2Nip;
            var p2Nama = selectedSkripsi.dataset.p2Nama;

            if (p1Nip) {
                var opt1 = document.createElement('option');
                opt1.value = p1Nip;
                opt1.dataset.nip = p1Nip;
                opt1.dataset.nama = p1Nama;
                opt1.textContent = 'Pembimbing 1: ' + (p1Nama || p1Nip);
                dosenSelect.appendChild(opt1);
            }

            if (p2Nip) {
                var opt2 = document.createElement('option');
                opt2.value = p2Nip;
                opt2.dataset.nip = p2Nip;
                opt2.dataset.nama = p2Nama;
                opt2.textContent = 'Pembimbing 2: ' + (p2Nama || p2Nip);
                dosenSelect.appendChild(opt2);
            }

            if (!p1Nip && !p2Nip) {
                var optEmpty = document.createElement('option');
                optEmpty.value = '198501012010121001';
                optEmpty.dataset.nip = '198501012010121001';
                optEmpty.dataset.nama = 'Dosen Pembimbing';
                optEmpty.textContent = 'Dosen Pembimbing (Default)';
                dosenSelect.appendChild(optEmpty);
            }

            updateDosenFields();
        }

        document.getElementById('skripsi_id').addEventListener('change', populateDosenList);
        populateDosenList();
        </script>

        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
