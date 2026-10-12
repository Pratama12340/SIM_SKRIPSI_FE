<?php

class Controller {
    /**
     * Memuat file view dengan passing data variabel.
     */
    protected function view($viewPath, $data = []) {
        extract($data);
        require_once __DIR__ . '/../../resources/views/' . $viewPath . '.php';
    }

    /**
     * Mengarahkan URL halaman.
     */
    protected function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Memastikan sesi login aktif sebelum mengakses halaman.
     */
    protected function requireAuth() {
        if (empty($_SESSION['token']) || empty($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Mendapatkan data user yang sedang aktif di sesi.
     */
    protected function getUser() {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Mendapatkan kode peran pengguna (MAHASISWA, DOSEN, dll).
     */
    protected function getRole() {
        $user = $this->getUser();
        if (!$user) return null;
        if (is_array($user['role'] ?? null)) {
            return strtoupper($user['role']['kodeRole'] ?? $user['role']['namaRole'] ?? 'MAHASISWA');
        }
        return strtoupper((string)($user['role'] ?? 'MAHASISWA'));
    }
}
