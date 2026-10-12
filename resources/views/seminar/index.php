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
            <h1 class="text-xl font-bold text-gray-800">Seminar & Sidang Skripsi</h1>
            <p class="text-xs text-gray-500 mt-1">Jadwal pelaksanaan Seminar Proposal (Sempro), Seminar Hasil (Semhas), dan Sidang Akhir Munaqasyah</p>
        </div>
        <?php if ($userRoleCode === 'MAHASISWA'): ?>
        <a href="<?= url('/seminar/daftar') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
            <i class="fa-solid fa-calendar-plus"></i>
            <span>Daftar Seminar / Sidang</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <?php if (empty($seminarList)): ?>
            <div class="p-12 text-center">
                <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-bold text-gray-700">Belum ada agenda seminar atau sidang</p>
                <p class="text-xs text-gray-400 mt-1">Jadwal akan muncul setelah mahasiswa mendaftar dan program studi memplot jadwal.</p>
                <?php if ($userRoleCode === 'MAHASISWA'): ?>
                <div class="mt-4">
                    <a href="<?= url('/seminar/daftar') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-plus"></i> Daftar Sekarang
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
                            <th class="py-3.5 px-5">Mahasiswa</th>
                            <th class="py-3.5 px-5">Jenis Sidang</th>
                            <th class="py-3.5 px-5">Judul Skripsi</th>
                            <th class="py-3.5 px-5">Jadwal & Ruangan</th>
                            <th class="py-3.5 px-5">Berkas Naskah</th>
                            <th class="py-3.5 px-5">Status</th>
                            <?php if ($isDosenOrAdmin): ?>
                            <th class="py-3.5 px-5 text-center">Aksi Plotting</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($seminarList as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-5 text-gray-500 font-semibold"><?= $no++ ?></td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_mahasiswa']) ?></span><br>
                                <span class="text-gray-400 font-medium"><?= htmlspecialchars($row['nim']) ?></span>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php
                                $jenisLabel = [
                                    'sempro'       => ['Sempro', 'bg-blue-100 text-blue-800'],
                                    'semhas'       => ['Semhas', 'bg-indigo-100 text-indigo-800'],
                                    'sidang_akhir' => ['Sidang Akhir', 'bg-purple-100 text-purple-800']
                                ];
                                $j = $jenisLabel[$row['jenis_sidang']] ?? [$row['jenis_sidang'], 'bg-gray-100 text-gray-800'];
                                ?>
                                <span class="px-2.5 py-1 rounded-full font-extrabold text-[10px] uppercase <?= $j[1] ?>">
                                    <?= $j[0] ?>
                                </span>
                            </td>
                            <td class="py-4 px-5 font-semibold text-gray-800 max-w-xs">
                                <?= htmlspecialchars($row['judul']) ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap text-gray-700">
                                <?php if (!empty($row['jadwal_sidang'])): ?>
                                    <div class="font-bold text-gray-800">
                                        <i class="fa-regular fa-clock text-blue-600 mr-1"></i>
                                        <?= date('d M Y, H:i', strtotime($row['jadwal_sidang'])) ?> WIB
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        <i class="fa-solid fa-location-dot text-red-500 mr-1"></i>
                                        <?= htmlspecialchars($row['ruangan'] ?: 'Ruang Sidang Utama') ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-amber-600 font-semibold italic bg-amber-50 px-2 py-0.5 rounded">
                                        Belum Dijadwalkan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php if (!empty($row['file_naskah'])): ?>
                                    <a href="http://localhost:5000/uploads/<?= htmlspecialchars($row['file_naskah']) ?>" target="_blank"
                                       class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-bold hover:underline">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i> Naskah PDF
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php
                                $statusBadge = [
                                    'menunggu_jadwal' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'terjadwal'       => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'lulus'           => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'revisi'          => 'bg-rose-100 text-rose-800 border border-rose-200'
                                ];
                                $sb = $statusBadge[$row['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= $sb ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>

                            <?php if ($isDosenOrAdmin): ?>
                            <td class="py-4 px-5 text-center whitespace-nowrap" x-data="{ openJadwalModal: false }">
                                <button @click="openJadwalModal = true" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition flex items-center gap-1 mx-auto">
                                    <i class="fa-solid fa-calendar-pen"></i> Plot Jadwal
                                </button>

                                <!-- Modal Dialog Plotting Jadwal -->
                                <div x-show="openJadwalModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                                    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-left space-y-4 shadow-xl" @click.away="openJadwalModal = false">
                                        <div class="flex items-center justify-between border-b pb-3">
                                            <h4 class="font-bold text-gray-800 text-sm">Plotting Jadwal & Ruangan Sidang</h4>
                                            <button @click="openJadwalModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                                        </div>

                                        <form method="POST" action="<?= url('/seminar/jadwal/' . $row['id']) ?>" class="space-y-4 text-xs">
                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Waktu Pelaksanaan Ujian</label>
                                                <input type="datetime-local" name="jadwal_sidang" required
                                                       value="<?= !empty($row['jadwal_sidang']) ? date('Y-m-d\TH:i', strtotime($row['jadwal_sidang'])) : '' ?>"
                                                       class="w-full p-2.5 rounded-xl border border-gray-200">
                                            </div>

                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Ruangan / Link Online</label>
                                                <input type="text" name="ruangan" placeholder="Contoh: Ruang Sidang FTI 204 atau Zoom Link" required
                                                       value="<?= htmlspecialchars($row['ruangan'] ?? 'Ruang Sidang Utama') ?>"
                                                       class="w-full p-2.5 rounded-xl border border-gray-200">
                                            </div>

                                            <div>
                                                <label class="block font-bold text-gray-700 uppercase mb-1">Status Pendaftaran</label>
                                                <select name="status" class="w-full p-2.5 rounded-xl border border-gray-200 bg-white font-semibold">
                                                    <option value="terjadwal" <?= $row['status'] === 'terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                                                    <option value="menunggu_jadwal" <?= $row['status'] === 'menunggu_jadwal' ? 'selected' : '' ?>>Menunggu Jadwal</option>
                                                    <option value="lulus" <?= $row['status'] === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                                                    <option value="revisi" <?= $row['status'] === 'revisi' ? 'selected' : '' ?>>Revisi</option>
                                                </select>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="openJadwalModal = false" class="px-4 py-2 rounded-xl border text-gray-600">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">Simpan Jadwal</button>
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
