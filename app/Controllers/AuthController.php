<?php

class AuthController extends Controller {

    public function login() {
        if (!empty($_SESSION['token']) && !empty($_SESSION['user'])) {
            $this->redirect('/dashboard');
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        $this->view('auth/login', [
            'title' => 'Login — SIM Skripsi UMB',
            'error' => $error
        ]);
    }

    public function postLogin() {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Username dan password wajib diisi!';
            $this->redirect('/login');
        }

        // Coba login via SSO SIM Mahasiswa API
        $ssoRes = ApiClient::ssoLogin($username, $password);

        if (!empty($ssoRes['success']) && !empty($ssoRes['data']['token'])) {
            $_SESSION['token'] = $ssoRes['data']['token'];
            $_SESSION['user'] = $ssoRes['data']['user'] ?? [
                'username' => $username,
                'nama'     => $ssoRes['data']['user']['namaLengkap'] ?? $username,
                'role'     => $ssoRes['data']['user']['role'] ?? 'MAHASISWA'
            ];
            $this->redirect('/dashboard');
        }

        // Fallback demo login jika offline atau akun seeder default
        $knownUsers = [
            '20260001' => ['id' => 5,  'nama' => 'Contoh Mahasiswa', 'role' => 'MAHASISWA', 'prodi_id' => 1],
            'dosen01'  => ['id' => 10, 'nama' => 'Dr. Budi Santoso, M.Kom.', 'role' => 'DOSEN', 'prodi_id' => 1],
            'dosen02'  => ['id' => 11, 'nama' => 'Ahmad Fauzi, M.T.', 'role' => 'DOSEN', 'prodi_id' => 1],
            'kaprodi'  => ['id' => 2,  'nama' => 'Kaprodi Teknik Informatika', 'role' => 'KAPRODI', 'prodi_id' => 1],
            'admin'    => ['id' => 1,  'nama' => 'Administrator SIM', 'role' => 'ADMIN', 'prodi_id' => 1],
        ];

        $lowerUser = strtolower($username);
        if (isset($knownUsers[$lowerUser])) {
            $u = $knownUsers[$lowerUser];
            $_SESSION['token'] = $this->generateDummyToken($u['id'], $username, $u['nama'], $u['role'], $u['prodi_id']);
            $_SESSION['user'] = [
                'id'       => $u['id'],
                'username' => $username,
                'nama'     => $u['nama'],
                'role'     => $u['role'],
                'prodi_id' => $u['prodi_id']
            ];
            $this->redirect('/dashboard');
        }

        $_SESSION['login_error'] = 'Kredensial login tidak cocok dengan SSO maupun data demo!';
        $this->redirect('/login');
    }

    public function quickLogin($role) {
        $role = strtolower($role);

        if ($role === 'admin') {
            $user = ['id' => 1, 'username' => 'admin', 'nama' => 'Administrator SIM Skripsi', 'role' => 'ADMIN', 'prodi_id' => 1];
        } elseif ($role === 'dosen') {
            $user = ['id' => 10, 'username' => '198501012010121001', 'nama' => 'Dr. Budi Santoso, M.Kom.', 'role' => 'DOSEN', 'prodi_id' => 1];
        } elseif ($role === 'kaprodi') {
            $user = ['id' => 2, 'username' => 'kaprodi.ti', 'nama' => 'Ketua Prodi Teknik Informatika', 'role' => 'KAPRODI', 'prodi_id' => 1];
        } else {
            // Default Mahasiswa
            $user = ['id' => 5, 'username' => '20260001', 'nama' => 'Contoh Mahasiswa', 'role' => 'MAHASISWA', 'prodi_id' => 1];
        }

        $_SESSION['token'] = $this->generateDummyToken($user['id'], $user['username'], $user['nama'], $user['role'], $user['prodi_id']);
        $_SESSION['user'] = $user;
        $_SESSION['flash_success'] = "Berhasil beralih akun sebagai {$user['role']} ({$user['nama']})";

        $this->redirect('/dashboard');
    }

    public function logout() {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();
        $_SESSION['login_error'] = 'Anda telah berhasil keluar dari sistem.';
        $this->redirect('/login');
    }

    private function generateDummyToken($id, $username, $nama, $role, $prodi_id = 1) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'sub'      => (string)$id,
            'username' => $username,
            'nama'     => $nama,
            'role'     => strtoupper($role),
            'scope'    => [
                'idUniversitas'  => '1',
                'idFakultas'     => '1',
                'idProgramStudi' => (string)$prodi_id
            ],
            'typ'      => 'access',
            'iss'      => 'sim-mhs-api',
            'iat'      => time(),
            'exp'      => time() + (7 * 24 * 3600)
        ]));

        $secret = 'rahasia_bersama_sim_mahasiswa_sso';
        $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
        $signature = base64_encode($signature);

        // JWT URL Safe format
        $header = str_replace(['+', '/', '='], ['-', '_', ''], $header);
        $payload = str_replace(['+', '/', '='], ['-', '_', ''], $payload);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $signature);

        return "$header.$payload.$signature";
    }
}
