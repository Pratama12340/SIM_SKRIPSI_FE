<?php
ob_start();
$userProfile = $_SESSION['user'] ?? [];
$userRoleCode = strtoupper((string)($userProfile['role'] ?? 'MAHASISWA'));
$currentProdiId = $_GET['prodi_id'] ?? '';
$currentStatus = $_GET['status'] ?? '';

$prodiMap = [];
foreach ($prodiList ?? [] as $pr) {
    $prodiMap[$pr['id']] = $pr;
}

$scopeUnitName = 'Universitas Muhammadiyah Bengkulu';
$scopeBadge = 'Akses Kampus';
if (!empty($scopeInfo['programStudi']['namaProgramStudi'])) {
    $scopeUnitName = $scopeInfo['programStudi']['namaProgramStudi'];
    $scopeBadge = 'Prodi ' . ($scopeInfo['programStudi']['kodeProgramStudi'] ?? '');
} elseif (!empty($scopeInfo['fakultas']['namaFakultas'])) {
    $scopeUnitName = $scopeInfo['fakultas']['namaFakultas'];
    $scopeBadge = 'Fakultas ' . ($scopeInfo['fakultas']['kodeFakultas'] ?? '');
}
?>

<div class="space-y-6">

    <!-- Header Section with Scope Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fa-solid fa-shield-halved mr-1"></i><?= htmlspecialchars($scopeBadge) ?>
                </span>
                <span class="text-xs text-gray-400 font-semibold">• <?= htmlspecialchars($scopeUnitName) ?></span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">Daftar Pengajuan Judul Skripsi</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola dan pantau proses verifikasi usulan proposal tugas akhir mahasiswa sesuai cakupan wewenang</p>
        </div>
        <?php if ($userRoleCode === 'MAHASISWA'): ?>
        <a href="<?= url('/pengajuan/tambah') ?>" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Ajukan Judul Baru</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="text-gray-500 font-semibold mr-1">Status:</span>
                <?php
                $statusBaseUrl = url('/pengajuan') . (!empty($currentProdiId) ? '?prodi_id=' . urlencode($currentProdiId) : '');
                $sep = !empty($currentProdiId) ? '&' : '?';
                ?>
                <a href="<?= $statusBaseUrl ?>" class="px-3 py-1.5 rounded-lg <?= empty($currentStatus) ? 'bg-blue-600 text-white font-bold' : 'bg-white text-gray-600 border border-gray-200' ?> transition">Semua</a>
                <a href="<?= $statusBaseUrl . $sep . 'status=diajukan' ?>" class="px-3 py-1.5 rounded-lg <?= $currentStatus === 'diajukan' ? 'bg-blue-600 text-white font-bold' : 'bg-white text-gray-600 border border-gray-200' ?> transition">Diajukan</a>
                <a href="<?= $statusBaseUrl . $sep . 'status=disetujui' ?>" class="px-3 py-1.5 rounded-lg <?= $currentStatus === 'disetujui' ? 'bg-blue-600 text-white font-bold' : 'bg-white text-gray-600 border border-gray-200' ?> transition">Disetujui</a>
                <a href="<?= $statusBaseUrl . $sep . 'status=ditolak' ?>" class="px-3 py-1.5 rounded-lg <?= $currentStatus === 'ditolak' ? 'bg-blue-600 text-white font-bold' : 'bg-white text-gray-600 border border-gray-200' ?> transition">Ditolak</a>

                <?php if (!empty($prodiList) && count($prodiList) > 1): ?>
                <span class="text-gray-300 mx-1">|</span>
                <form method="GET" action="<?= url('/pengajuan') ?>" class="inline-flex items-center gap-1.5">
                    <?php if (!empty($currentStatus)): ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
                    <?php endif; ?>
                    <label for="filter_prodi" class="text-gray-500 font-semibold"><i class="fa-solid fa-graduation-cap text-blue-600 mr-0.5"></i> Prodi:</label>
                    <select id="filter_prodi" name="prodi_id" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-gray-700 font-semibold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Semua Prodi (<?= count($prodiList) ?>)</option>
                        <?php foreach ($prodiList as $prodi): ?>
                            <option value="<?= $prodi['id'] ?>" <?= ((string)$currentProdiId === (string)$prodi['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prodi['kodeProgramStudi'] . ' - ' . $prodi['namaProgramStudi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
            </div>
            <div class="text-xs text-gray-500 font-medium">
                Total: <strong class="text-gray-800"><?= count($pengajuanList ?? []) ?></strong> data
            </div>
        </div>

        <?php if (empty($pengajuanList)): ?>
            <div class="p-12 text-center">
                <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-bold text-gray-700">Tidak ada data pengajuan</p>
                <p class="text-xs text-gray-400 mt-1">Belum ada pengajuan judul yang sesuai dengan filter yang dipilih.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-100 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">No</th>
                            <th class="py-3.5 px-5">Mahasiswa</th>
                            <th class="py-3.5 px-5">Judul Penelitian</th>
                            <th class="py-3.5 px-5">Bidang Kajian</th>
                            <th class="py-3.5 px-5">Pembimbing</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($pengajuanList as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-5 text-gray-500 font-semibold"><?= $no++ ?></td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_mahasiswa']) ?></span><br>
                                <span class="text-gray-400 font-medium font-mono text-[11px]"><?= htmlspecialchars($row['nim']) ?></span>
                                <?php if (!empty($row['prodi_id']) && isset($prodiMap[$row['prodi_id']])): ?>
                                    <span class="inline-block ml-1 px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-100">
                                        <?= htmlspecialchars($prodiMap[$row['prodi_id']]['kodeProgramStudi']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 font-semibold text-gray-800 max-w-sm">
                                <?= htmlspecialchars($row['judul']) ?>
                                <?php if (!empty($row['file_proposal'])): ?>
                                    <div class="mt-1">
                                        <a href="<?= upload_url(htmlspecialchars($row['file_proposal'])) ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline font-semibold">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i> Proposal PDF
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap text-gray-600">
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 font-medium">
                                    <?= htmlspecialchars($row['bidang_kajian'] ?: 'Umum') ?>
                                </span>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap text-gray-600">
                                <?php if ($row['dosen_pembimbing_1_nip']): ?>
                                    <div class="text-[11px] leading-tight">
                                        <strong>P1:</strong> <?= htmlspecialchars($row['dosen_pembimbing_1_nama'] ?: $row['dosen_pembimbing_1_nip']) ?><br>
                                        <?php if ($row['dosen_pembimbing_2_nip']): ?>
                                            <strong>P2:</strong> <?= htmlspecialchars($row['dosen_pembimbing_2_nama'] ?: $row['dosen_pembimbing_2_nip']) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Belum diplot</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <?php
                                $badgeMap = [
                                    'draft'     => 'bg-gray-100 text-gray-700',
                                    'diajukan'  => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'disetujui' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'ditolak'   => 'bg-rose-100 text-rose-800 border border-rose-200'
                                ];
                                $bColor = $badgeMap[$row['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $bColor ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                <a href="<?= url('/pengajuan/detail/' . $row['id']) ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
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
