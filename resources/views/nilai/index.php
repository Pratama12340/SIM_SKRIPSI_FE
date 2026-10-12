<?php
ob_start();
?>

<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Penilaian Sidang Skripsi</h1>
            <p class="text-xs text-gray-500 mt-1">Input penilaian dewan penguji, keputusan kelulusan, dan catatan revisi munaqasyah</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <?php if (empty($sidangList)): ?>
            <div class="p-12 text-center">
                <i class="fa-solid fa-award text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-bold text-gray-700">Belum ada sidang yang terjadwal</p>
                <p class="text-xs text-gray-400 mt-1">Daftar mahasiswa yang siap dinilai akan muncul setelah program studi memplot jadwal ujian.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">No</th>
                            <th class="py-3.5 px-5">Mahasiswa</th>
                            <th class="py-3.5 px-5">Tahapan Sidang</th>
                            <th class="py-3.5 px-5">Judul Skripsi</th>
                            <th class="py-3.5 px-5">Nilai Akhir</th>
                            <th class="py-3.5 px-5">Status Kelulusan</th>
                            <th class="py-3.5 px-5">Catatan Penguji</th>
                            <th class="py-3.5 px-5 text-center">Input Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($sidangList as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-5 text-gray-500 font-semibold"><?= $no++ ?></td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_mahasiswa']) ?></span><br>
                                <span class="text-gray-400 font-medium"><?= htmlspecialchars($row['nim']) ?></span>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800">
                                    <?= htmlspecialchars($row['jenis_sidang']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-5 font-semibold text-gray-800 max-w-xs">
                                <?= htmlspecialchars($row['judul']) ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap font-bold text-base text-gray-800">
                                <?= $row['nilai_akhir'] !== null ? number_format($row['nilai_akhir'], 1) : '<span class="text-gray-400 font-normal text-xs italic">Belum dinilai</span>' ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php
                                $statusBadge = [
                                    'lulus'           => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'revisi'          => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    'terjadwal'       => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'menunggu_jadwal' => 'bg-amber-100 text-amber-800 border border-amber-200'
                                ];
                                $sb = $statusBadge[$row['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= $sb ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-5 max-w-xs text-gray-600">
                                <?= htmlspecialchars($row['catatan_sidang'] ?: '-') ?>
                            </td>
                            <td class="py-4 px-5 text-center whitespace-nowrap" x-data="{ openNilaiModal: false }">
                                <button @click="openNilaiModal = true" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition flex items-center gap-1 mx-auto">
                                    <i class="fa-solid fa-award"></i> Beri Nilai
                                </button>

                                <!-- Modal Dialog Input Nilai -->
                                <div x-show="openNilaiModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-left space-y-4 shadow-xl" @click.away="openNilaiModal = false">
                                        <div class="flex items-center justify-between border-b pb-3">
                                            <h4 class="font-bold text-gray-800 text-sm">Penilaian Ujian Sidang</h4>
                                            <button @click="openNilaiModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                                        </div>

                                        <form method="POST" action="<?= url('/nilai/input/' . $row['id']) ?>" class="space-y-4 text-xs">
                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Nilai Akhir (Skala 0 - 100)</label>
                                                <input type="number" step="0.1" min="0" max="100" name="nilai_akhir" required
                                                       value="<?= $row['nilai_akhir'] ?? '85' ?>"
                                                       placeholder="Contoh: 85.5"
                                                       class="w-full p-2.5 rounded-xl border border-gray-200 font-bold text-sm">
                                            </div>

                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Keputusan Dewan Penguji</label>
                                                <select name="status" class="w-full p-2.5 rounded-xl border border-gray-200 bg-white font-semibold">
                                                    <option value="lulus" <?= $row['status'] === 'lulus' ? 'selected' : '' ?>>Lulus (Ujian Diterima)</option>
                                                    <option value="revisi" <?= $row['status'] === 'revisi' ? 'selected' : '' ?>>Revisi (Wajib Perbaikan Naskah)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Catatan & Masukan Dewan Penguji</label>
                                                <textarea name="catatan_sidang" rows="3" placeholder="Tuliskan catatan perbaikan atau pertimbangan hasil ujian..."
                                                          class="w-full p-2.5 rounded-xl border border-gray-200"><?= htmlspecialchars($row['catatan_sidang'] ?? '') ?></textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="openNilaiModal = false" class="px-4 py-2 rounded-xl border text-gray-600">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700">Simpan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
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
