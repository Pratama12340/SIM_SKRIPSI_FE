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

        // 1. Coba login melalui Backend API SIM Skripsi (/auth/login)
        $backendRes = ApiClient::request('POST', '/auth/login', [
            'username' => $username,
            'password' => $password
        ]);

        if (!empty($backendRes['success']) && (!empty($backendRes['data']['token']) || !empty($backendRes['data']['accessToken']))) {
            $token = $backendRes['data']['token'] ?? $backendRes['data']['accessToken'];
            $user = $backendRes['data']['user'] ?? [];
            $roleCode = is_array($user['role'] ?? null) 
                ? ($user['role']['kodeRole'] ?? 'MAHASISWA') 
                : ($user['role'] ?? 'MAHASISWA');

            $_SESSION['token'] = $token;
            $_SESSION['user'] = [
                'id'       => $user['id'] ?? 1,
                'username' => $user['username'] ?? $username,
                'nama'     => $user['namaLengkap'] ?? ($user['nama'] ?? $username),
                'role'     => strtoupper($roleCode),
                'prodi_id' => $user['prodi_id'] ?? 1,
                'scope'    => $user['scope'] ?? null
            ];
            $this->redirect('/dashboard');
        }

        // 2. Fallback langsung ke SSO SIM Mahasiswa API (sim-mhs-api)
        $ssoRes = ApiClient::ssoLogin($username, $password);
        $ssoToken = $ssoRes['data']['accessToken'] ?? ($ssoRes['data']['token'] ?? null);

        if (!empty($ssoRes['success']) && !empty($ssoToken)) {
            $user = $ssoRes['data']['user'] ?? [];
            $roleCode = is_array($user['role'] ?? null) 
                ? ($user['role']['kodeRole'] ?? 'MAHASISWA') 
                : ($user['role'] ?? 'MAHASISWA');

            $_SESSION['token'] = $ssoToken;
            $_SESSION['user'] = [
                'id'       => $user['id'] ?? 1,
                'username' => $user['username'] ?? $username,
                'nama'     => $user['namaLengkap'] ?? ($user['nama'] ?? $username),
                'role'     => strtoupper($roleCode),
                'prodi_id' => $user['idProgramStudi'] ?? 1,
                'scope'    => $user['scope'] ?? null
            ];
            $this->redirect('/dashboard');
        }

        // 3. Fallback demo login jika offline atau akun seeder default
        $knownUsers = [
            'admin'        => ['id' => 1,  'nama' => 'Administrator Sistem', 'role' => 'ADMIN', 'fakultas_id' => null, 'prodi_id' => null],
            'adm_prodi_ti' => ['id' => 2,  'nama' => 'Operator Prodi TI', 'role' => 'ADMIN_PRODI', 'fakultas_id' => 1, 'prodi_id' => 1],
            'kaprodi_ti'   => ['id' => 3,  'nama' => 'Dr. Ratna Sari, M.Kom.', 'role' => 'KAPRODI', 'fakultas_id' => 1, 'prodi_id' => 1],
            'kaprodi'      => ['id' => 3,  'nama' => 'Kaprodi Teknik Informatika', 'role' => 'KAPRODI', 'fakultas_id' => 1, 'prodi_id' => 1],
            'adm_dekanat'  => ['id' => 13, 'nama' => 'Staff Administrasi FTI', 'role' => 'ADMIN_DEKANAT', 'fakultas_id' => 1, 'prodi_id' => null],
            'dosen01'      => ['id' => 10, 'nama' => 'Dr. Budi Santoso, M.Kom.', 'role' => 'DOSEN', 'fakultas_id' => 1, 'prodi_id' => 1],
            'dosen02'      => ['id' => 11, 'nama' => 'Ahmad Fauzi, M.T.', 'role' => 'DOSEN', 'fakultas_id' => 1, 'prodi_id' => 1],
            'mhs001'       => ['id' => 6,  'nama' => 'Andi Pratama', 'role' => 'MAHASISWA', 'fakultas_id' => 1, 'prodi_id' => 1],
            '20260001'     => ['id' => 5,  'nama' => 'Contoh Mahasiswa', 'role' => 'MAHASISWA', 'fakultas_id' => 1, 'prodi_id' => 1],
        ];

        $lowerUser = strtolower($username);
        if (isset($knownUsers[$lowerUser])) {
            $u = $knownUsers[$lowerUser];
            $_SESSION['token'] = $this->generateDummyToken($u['id'], $username, $u['nama'], $u['role'], $u['fakultas_id'], $u['prodi_id']);
            $_SESSION['user'] = [
                'id'          => $u['id'],
                'username'    => $username,
                'nama'        => $u['nama'],
                'role'        => $u['role'],
                'fakultas_id' => $u['fakultas_id'],
                'prodi_id'    => $u['prodi_id']
            ];
            $this->redirect('/dashboard');
        }

        $errorMsg = $backendRes['message'] ?? ($ssoRes['message'] ?? 'Kredensial login tidak cocok dengan data SSO SIM Mahasiswa!');
        $_SESSION['login_error'] = $errorMsg;
        $this->redirect('/login');
    }

    public function quickLogin($role) {
        $role = strtolower($role);

        if ($role === 'admin') {
            $user = ['id' => 1, 'username' => 'admin', 'nama' => 'Administrator Sistem', 'role' => 'ADMIN', 'fakultas_id' => null, 'prodi_id' => null];
        } elseif ($role === 'admin_prodi' || $role === 'operator_prodi') {
            $user = ['id' => 2, 'username' => 'adm_prodi_ti', 'nama' => 'Operator Prodi TI', 'role' => 'ADMIN_PRODI', 'fakultas_id' => 1, 'prodi_id' => 1];
        } elseif ($role === 'kaprodi') {
            $user = ['id' => 3, 'username' => 'kaprodi_ti', 'nama' => 'Dr. Ratna Sari, M.Kom.', 'role' => 'KAPRODI', 'fakultas_id' => 1, 'prodi_id' => 1];
        } elseif ($role === 'admin_dekanat' || $role === 'dekanat') {
            $user = ['id' => 13, 'username' => 'adm_dekanat', 'nama' => 'Staff Administrasi FTI', 'role' => 'ADMIN_DEKANAT', 'fakultas_id' => 1, 'prodi_id' => null];
        } elseif ($role === 'dosen') {
            $user = ['id' => 10, 'username' => 'dosen01', 'nama' => 'Dr. Budi Santoso, M.Kom.', 'role' => 'DOSEN', 'fakultas_id' => 1, 'prodi_id' => 1];
        } else {
            // Default Mahasiswa
            $user = ['id' => 6, 'username' => 'mhs001', 'nama' => 'Andi Pratama', 'role' => 'MAHASISWA', 'fakultas_id' => 1, 'prodi_id' => 1];
        }

        $_SESSION['token'] = $this->generateDummyToken($user['id'], $user['username'], $user['nama'], $user['role'], $user['fakultas_id'], $user['prodi_id']);
        $_SESSION['user'] = $user;
        $_SESSION['flash_success'] = "Berhasil masuk sebagai {$user['role']} ({$user['nama']})";

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

    private function generateDummyToken($id, $username, $nama, $role, $fakultas_id = null, $prodi_id = null) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'sub'      => (string)$id,
            'username' => $username,
            'nama'     => $nama,
            'role'     => strtoupper($role),
            'scope'    => [
                'idUniversitas'  => '1',
                'idFakultas'     => $fakultas_id !== null ? (string)$fakultas_id : null,
                'idProgramStudi' => $prodi_id !== null ? (string)$prodi_id : null
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
