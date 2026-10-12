<?php

class ApiClient {
    /**
     * Base URL Backend REST API SIM Skripsi (Node.js)
     */
    public static function getBaseUrl() {
        return getenv('SIM_SKRIPSI_API_URL') ?: 'http://127.0.0.1:5000/api';
    }

    /**
     * Base URL SSO SIM Mahasiswa (sim-mhs-api)
     */
    public static function getSsoUrl() {
        return getenv('SSO_API_URL') ?: 'https://sim-mhs-api.vercel.app/api/v1';
    }

    /**
     * Melakukan HTTP Request ke Backend SIM Skripsi atau SSO API.
     */
    public static function request($method, $endpoint, $data = [], $token = null, $files = []) {
        $url = self::getBaseUrl() . $endpoint;
        $method = strtoupper($method);

        if (!$token && isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
        }

        $headers = [
            'Accept: application/json'
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();

        // Penanganan GET Query Parameters
        if ($method === 'GET') {
            if (!empty($data)) {
                $query = http_build_query($data);
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $query;
            }
        } 
        // Penanganan Multipart File Upload (PDF naskah / proposal)
        elseif (!empty($files)) {
            $postFields = $data;
            foreach ($files as $field => $fileData) {
                if (isset($fileData['tmp_name']) && file_exists($fileData['tmp_name'])) {
                    $mime = $fileData['type'] ?: 'application/pdf';
                    $postFields[$field] = new CURLFile($fileData['tmp_name'], $mime, $fileData['name']);
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } 
        // Penanganan JSON Payload biasa
        else {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'status'  => false,
                'message' => 'Gagal terhubung ke Backend API (http://localhost:5000): ' . $error
            ];
        }

        $decoded = json_decode($response, true);
        if ($decoded === null && !empty($response)) {
            return [
                'success'  => false,
                'httpCode' => $httpCode,
                'message'  => 'Format respons bukan JSON yang valid',
                'raw'      => $response
            ];
        }

        return $decoded ?: [
            'success'  => ($httpCode >= 200 && $httpCode < 300),
            'httpCode' => $httpCode
        ];
    }

    /**
     * Request khusus ke SSO SIM Mahasiswa untuk login pengguna.
     */
    public static function ssoLogin($username, $password) {
        $url = self::getSsoUrl() . '/auth/login';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => $username,
            'password' => $password
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server SSO: ' . $error
            ];
        }

        return json_decode($response, true);
    }
}
