<?php
ob_start();
$userProfile = $_SESSION['user'] ?? [];
$userRoleCode = strtoupper((string)($userProfile['role'] ?? 'MAHASISWA'));
$isDosenOrAdmin = in_array($userRoleCode, ['DOSEN', 'KAPRODI', 'ADMIN_PRODI', 'ADMIN']);
?>

<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Logbook Bimbingan Skripsi</h1>
            <p class="text-xs text-gray-500 mt-1">Dokumentasi konsultasi, arahan revisi berkas, dan kartu kendali bimbingan</p>
        </div>
        <?php if ($userRoleCode === 'MAHASISWA'): ?>
        <a href="<?= url('/bimbingan/tambah') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Catatan Bimbingan</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <?php if (empty($bimbinganList)): ?>
            <div class="p-12 text-center">
                <i class="fa-solid fa-comments text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-bold text-gray-700">Belum ada riwayat bimbingan</p>
                <p class="text-xs text-gray-400 mt-1">Tambahkan catatan konsultasi setiap kali selesai bimbingan dengan dosen pembimbing.</p>
                <?php if ($userRoleCode === 'MAHASISWA'): ?>
                <div class="mt-4">
                    <a href="<?= url('/bimbingan/tambah') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Bimbingan Baru
                    </a>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">No</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Judul Skripsi</th>
                            <th class="py-3.5 px-5">Dosen Tujuan</th>
                            <th class="py-3.5 px-5">Progres & Catatan Mahasiswa</th>
                            <th class="py-3.5 px-5">Respon / Catatan Dosen</th>
                            <th class="py-3.5 px-5">Status ACC</th>
                            <?php if ($isDosenOrAdmin): ?>
                            <th class="py-3.5 px-5 text-center">Aksi Dosen</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($bimbinganList as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-5 text-gray-500 font-semibold"><?= $no++ ?></td>
                            <td class="py-4 px-5 whitespace-nowrap font-bold text-gray-800">
                                <?= date('d M Y', strtotime($row['tanggal_bimbingan'])) ?>
                            </td>
                            <td class="py-4 px-5 font-semibold text-gray-800 max-w-xs">
                                <?= htmlspecialchars($row['judul'] ?? 'Skripsi #' . $row['skripsi_id']) ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap text-gray-700">
                                <strong><?= htmlspecialchars($row['dosen_nama'] ?: $row['dosen_nip']) ?></strong><br>
                                <span class="text-gray-400 font-mono text-[10px]">NIP: <?= htmlspecialchars($row['dosen_nip']) ?></span>
                            </td>
                            <td class="py-4 px-5 max-w-xs text-gray-600">
                                <p class="leading-relaxed"><?= htmlspecialchars($row['catatan_mahasiswa']) ?></p>
                                <?php if (!empty($row['file_revisi'])): ?>
                                    <div class="mt-1.5">
                                        <a href="http://localhost:5000/uploads/<?= htmlspecialchars($row['file_revisi']) ?>" target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:underline">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i> Draf Bab Revisi PDF
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 max-w-xs text-gray-700">
                                <?php if (!empty($row['catatan_dosen'])): ?>
                                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 leading-relaxed text-[11px]">
                                        <?= htmlspecialchars($row['catatan_dosen']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Belum ada respon</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php
                                $accColors = [
                                    'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'revisi'  => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    'acc'     => 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                                ];
                                $bColor = $accColors[$row['status_acc']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= $bColor ?>">
                                    <?= htmlspecialchars($row['status_acc']) ?>
                                </span>
                            </td>

                            <?php if ($isDosenOrAdmin): ?>
                            <td class="py-4 px-5 text-center whitespace-nowrap" x-data="{ openAccModal: false }">
                                <button @click="openAccModal = true" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition flex items-center gap-1 mx-auto">
                                    <i class="fa-solid fa-pen-to-square"></i> Tanggapi
                                </button>

                                <!-- Modal Dialog Tanggapan Dosen -->
                                <div x-show="openAccModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-left space-y-4 shadow-xl" @click.away="openAccModal = false">
                                        <div class="flex items-center justify-between border-b pb-3">
                                            <h4 class="font-bold text-gray-800 text-sm">Beri Tanggapan Bimbingan</h4>
                                            <button @click="openAccModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                                        </div>

                                        <form method="POST" action="<?= url('/bimbingan/acc/' . $row['id']) ?>" class="space-y-4 text-xs">
                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Keputusan Bimbingan</label>
                                                <select name="status_acc" class="w-full p-2.5 rounded-xl border border-gray-200 bg-white font-semibold">
                                                    <option value="acc" <?= $row['status_acc'] === 'acc' ? 'selected' : '' ?>>ACC (Lanjut Bab Berikutnya / Siap Sidang)</option>
                                                    <option value="revisi" <?= $row['status_acc'] === 'revisi' ? 'selected' : '' ?>>Revisi (Perlu Perbaikan)</option>
                                                    <option value="pending" <?= $row['status_acc'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Catatan & Masukan Dosen</label>
                                                <textarea name="catatan_dosen" rows="4" required placeholder="Tuliskan catatan perbaikan atau instruksi untuk mahasiswa..."
                                                          class="w-full p-2.5 rounded-xl border border-gray-200"><?= htmlspecialchars($row['catatan_dosen'] ?? '') ?></textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="openAccModal = false" class="px-4 py-2 rounded-xl border text-gray-600">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700">Simpan Tanggapan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
