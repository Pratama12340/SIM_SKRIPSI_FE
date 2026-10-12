<?php

class BimbinganController extends Controller {

    public function index() {
        $this->requireAuth();

        // Ambil skripsi untuk mendapatkan daftar bimbingan
        $pengajuanRes = ApiClient::request('GET', '/pengajuan');
        $skripsiList = $pengajuanRes['data'] ?? [];

        $bimbinganList = [];
        foreach ($skripsiList as $s) {
            $bimRes = ApiClient::request('GET', "/bimbingan/{$s['id']}");
            if (!empty($bimRes['data'])) {
                foreach ($bimRes['data'] as $b) {
                    $bimbinganList[] = $b;
                }
            }
        }

        // Urutkan berdasarkan tanggal terbaru
        usort($bimbinganList, function($a, $b) {
            return strtotime($b['tanggal_bimbingan']) - strtotime($a['tanggal_bimbingan']);
        });

        $this->view('bimbingan/index', [
            'title'         => 'Logbook Bimbingan Skripsi — SIM Skripsi',
            'bimbinganList' => $bimbinganList
        ]);
    }

    public function create() {
        $this->requireAuth();

        $pengajuanRes = ApiClient::request('GET', '/pengajuan', ['status' => 'disetujui']);
        $mySkripsiList = $pengajuanRes['data'] ?? [];

        $error = $_SESSION['bimbingan_error'] ?? null;
        unset($_SESSION['bimbingan_error']);

        $this->view('bimbingan/create', [
            'title'         => 'Tambah Catatan Bimbingan — SIM Skripsi',
            'mySkripsiList' => $mySkripsiList,
            'error'         => $error
        ]);
    }

    public function store() {
        $this->requireAuth();

        $postData = [
            'skripsi_id'        => $_POST['skripsi_id'] ?? '',
            'dosen_nip'         => $_POST['dosen_nip'] ?? '',
            'dosen_nama'        => $_POST['dosen_nama'] ?? '',
            'tanggal_bimbingan' => $_POST['tanggal_bimbingan'] ?? '',
            'catatan_mahasiswa' => trim($_POST['catatan_mahasiswa'] ?? '')
        ];

        $files = [];
        if (!empty($_FILES['file_revisi']['name'])) {
            $files['file_revisi'] = $_FILES['file_revisi'];
        }

        $res = ApiClient::request('POST', '/bimbingan/tambah', $postData, null, $files);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Catatan bimbingan berhasil ditambahkan!';
            $this->redirect('/bimbingan');
        } else {
            $_SESSION['bimbingan_error'] = $res['message'] ?? 'Gagal menambahkan catatan bimbingan.';
            $this->redirect('/bimbingan/tambah');
        }
    }

    public function acc($id) {
        $this->requireAuth();

        $payload = [
            'status_acc'    => $_POST['status_acc'] ?? 'acc',
            'catatan_dosen' => trim($_POST['catatan_dosen'] ?? '')
        ];

        $res = ApiClient::request('PUT', "/bimbingan/{$id}/acc", $payload);

        if (!empty($res['success'])) {
            $_SESSION['flash_success'] = 'Tanggapan dan status bimbingan berhasil diperbarui!';
        } else {
            $_SESSION['flash_error'] = $res['message'] ?? 'Gagal memperbarui tanggapan bimbingan.';
        }

        $this->redirect('/bimbingan');
    }
}
