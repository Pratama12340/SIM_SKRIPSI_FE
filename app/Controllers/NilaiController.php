<?php

class NilaiController extends Controller {

    public function index() {
        $this->requireAuth();

        $params = [];
        if (!empty($_GET['status'])) $params['status'] = $_GET['status'];
        if (!empty($_GET['prodi_id'])) $params['prodi_id'] = $_GET['prodi_id'];

        $res = ApiClient::request('GET', '/seminar/jadwal', $params);
        $sidangList = $res['data'] ?? [];

        $this->view('nilai/index', [
            'title'      => 'Penilaian Sidang Skripsi — SIM Skripsi',
            'sidangList' => $sidangList
        ]);
    }

    public function inputNilai($id) {
        $this->requireAuth();

        $payload = [
            'nilai_akhir'    => $_POST['nilai_akhir'] ?? null,
            'catatan_sidang' => trim($_POST['catatan_sidang'] ?? ''),
            'status'         => $_POST['status'] ?? 'lulus'
        ];

        $res = ApiClient::request('PUT', "/nilai/{$id}", $payload);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Nilai dan status kelulusan sidang berhasil disimpan!';
        } else {
            $_SESSION['flash_error'] = $res['message'] ?? 'Gagal menyimpan nilai sidang.';
        }

        $this->redirect('/nilai');
    }
}
