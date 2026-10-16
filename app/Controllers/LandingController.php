<?php

class LandingController extends Controller {

    public function index() {
        $user = $this->getUser();
        $role = $this->getRole();

        // Cek apakah ada flash message atau status dari sesi
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        // Data statistik dinamis/fallback realistis untuk landing page
        $stats = [
            'total_pengajuan'  => '1.420+',
            'tingkat_kelulusan' => '98.6%',
            'dosen_aktif'      => '120+',
            'rerata_bimbingan' => '14 Hari'
        ];

        $this->view('landing/index', [
            'title'        => 'SIM Skripsi — Universitas Muhammadiyah Bengkulu',
            'user'         => $user,
            'role'         => $role,
            'flashSuccess' => $flashSuccess,
            'stats'        => $stats
        ]);
    }
}
