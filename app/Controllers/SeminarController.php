<?php

class SeminarController extends Controller {

    public function index() {
        $this->requireAuth();

        $params = [];
        if (!empty($_GET['jenis_sidang'])) $params['jenis_sidang'] = $_GET['jenis_sidang'];
        if (!empty($_GET['status'])) $params['status'] = $_GET['status'];
        if (!empty($_GET['prodi_id'])) $params['prodi_id'] = $_GET['prodi_id'];

        $res = ApiClient::request('GET', '/seminar/jadwal', $params);
        $seminarList = $res['data'] ?? [];

        $this->view('seminar/index', [
            'title'       => 'Seminar & Sidang Skripsi — SIM Skripsi',
            'seminarList' => $seminarList
        ]);
    }

    public function create() {
        $this->requireAuth();

        $pengajuanRes = ApiClient::request('GET', '/pengajuan', ['status' => 'disetujui']);
        $mySkripsiList = $pengajuanRes['data'] ?? [];

        $error = $_SESSION['seminar_error'] ?? null;
        unset($_SESSION['seminar_error']);

        $this->view('seminar/create', [
            'title'         => 'Daftar Seminar / Sidang — SIM Skripsi',
            'mySkripsiList' => $mySkripsiList,
            'error'         => $error
        ]);
    }

    public function store() {
        $this->requireAuth();

        $postData = [
            'skripsi_id'   => $_POST['skripsi_id'] ?? '',
            'jenis_sidang' => $_POST['jenis_sidang'] ?? 'sempro'
        ];

        $files = [];
        if (!empty($_FILES['file_naskah']['name'])) {
            $files['file_naskah'] = $_FILES['file_naskah'];
        }

        $res = ApiClient::request('POST', '/seminar/daftar', $postData, null, $files);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Pendaftaran ujian berhasil dikirimkan!';
            $this->redirect('/seminar');
        } else {
            $_SESSION['seminar_error'] = $res['message'] ?? 'Gagal mendaftar ujian sidang.';
            $this->redirect('/seminar/daftar');
        }
    }

    public function updateJadwal($id) {
        $this->requireAuth();

        $payload = [
            'jadwal_sidang' => !empty($_POST['jadwal_sidang']) ? date('Y-m-d H:i:s', strtotime($_POST['jadwal_sidang'])) : null,
            'ruangan'       => trim($_POST['ruangan'] ?? ''),
            'status'        => $_POST['status'] ?? 'terjadwal'
        ];

        $res = ApiClient::request('PUT', "/seminar/{$id}/jadwal", $payload);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Jadwal dan status ujian sidang berhasil diperbarui!';
        } else {
            $_SESSION['flash_error'] = $res['message'] ?? 'Gagal memperbarui jadwal.';
        }

        $this->redirect('/seminar');
    }
}
