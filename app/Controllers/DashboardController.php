<?php

class DashboardController extends Controller {

    public function index() {
        $this->requireAuth();

        // Ambil data pengajuan dari API
        $res = ApiClient::request('GET', '/pengajuan');
        $pengajuanList = $res['data'] ?? [];

        // Ambil data seminar dari API
        $semRes = ApiClient::request('GET', '/seminar/jadwal');
        $seminarList = $semRes['data'] ?? [];

        // Hitung statistik
        $totalPengajuan = count($pengajuanList);
        $disetujuiCount = 0;
        $totalBimbinganCount = 0;

        foreach ($pengajuanList as $p) {
            if (($p['status'] ?? '') === 'disetujui') {
                $disetujuiCount++;
            }
            $totalBimbinganCount += (int)($p['total_bimbingan'] ?? 0);
        }

        $stats = [
            'total_pengajuan' => $totalPengajuan,
            'disetujui'       => $disetujuiCount,
            'total_bimbingan' => $totalBimbinganCount,
            'total_sidang'    => count($seminarList)
        ];

        // 5 data pengajuan terbaru
        $recentPengajuan = array_slice($pengajuanList, 0, 5);

        $this->view('dashboard/index', [
            'title'           => 'Dashboard — SIM Skripsi',
            'stats'           => $stats,
            'recentPengajuan' => $recentPengajuan
        ]);
    }
}
