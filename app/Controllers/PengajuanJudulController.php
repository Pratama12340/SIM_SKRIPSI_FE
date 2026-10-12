<?php

class PengajuanJudulController extends Controller {

    public function index() {
        $this->requireAuth();

        $params = [];
        if (!empty($_GET['status'])) {
            $params['status'] = $_GET['status'];
        }

        $res = ApiClient::request('GET', '/pengajuan', $params);
        $pengajuanList = $res['data'] ?? [];

        $this->view('pengajuan/index', [
            'title'         => 'Pengajuan Judul Skripsi — SIM Skripsi',
            'pengajuanList' => $pengajuanList
        ]);
    }

    public function create() {
        $this->requireAuth();

        $error = $_SESSION['pengajuan_error'] ?? null;
        unset($_SESSION['pengajuan_error']);

        $this->view('pengajuan/create', [
            'title' => 'Ajukan Judul Skripsi Baru — SIM Skripsi',
            'error' => $error
        ]);
    }

    public function store() {
        $this->requireAuth();

        $postData = [
            'judul'         => trim($_POST['judul'] ?? ''),
            'bidang_kajian' => trim($_POST['bidang_kajian'] ?? ''),
            'abstrak'       => trim($_POST['abstrak'] ?? ''),
            'status'        => $_POST['status'] ?? 'diajukan'
        ];

        $files = [];
        if (!empty($_FILES['file_proposal']['name'])) {
            $files['file_proposal'] = $_FILES['file_proposal'];
        }

        $res = ApiClient::request('POST', '/pengajuan/submit', $postData, null, $files);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Judul skripsi berhasil diajukan!';
            $this->redirect('/pengajuan');
        } else {
            $_SESSION['pengajuan_error'] = $res['message'] ?? 'Gagal menyimpan pengajuan skripsi.';
            $this->redirect('/pengajuan/tambah');
        }
    }

    public function detail($id) {
        $this->requireAuth();

        $res = ApiClient::request('GET', "/pengajuan/{$id}");

        if (empty($res['data'])) {
            $_SESSION['flash_error'] = 'Data pengajuan tidak ditemukan.';
            $this->redirect('/pengajuan');
        }

        $this->view('pengajuan/detail', [
            'title'     => 'Detail Pengajuan Skripsi — SIM Skripsi',
            'pengajuan' => $res['data']
        ]);
    }

    public function verifikasi($id) {
        $this->requireAuth();

        $payload = [
            'status'                  => $_POST['status'] ?? null,
            'dosen_pembimbing_1_nip'  => $_POST['dosen_pembimbing_1_nip'] ?? null,
            'dosen_pembimbing_1_nama' => $_POST['dosen_pembimbing_1_nama'] ?? null,
            'dosen_pembimbing_2_nip'  => $_POST['dosen_pembimbing_2_nip'] ?? null,
            'dosen_pembimbing_2_nama' => $_POST['dosen_pembimbing_2_nama'] ?? null,
            'catatan_verifikasi'      => $_POST['catatan_verifikasi'] ?? null
        ];

        $res = ApiClient::request('PUT', "/pengajuan/{$id}/status", $payload);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Status verifikasi dan dosen pembimbing berhasil disimpan!';
        } else {
            $_SESSION['flash_error'] = $res['message'] ?? 'Gagal memperbarui verifikasi.';
        }

        $this->redirect("/pengajuan/detail/{$id}");
    }
}
