<?php

class ApiClient {
    /**
     * Base URL Backend REST API SIM Skripsi (Node.js)
     */
    public static function getBaseUrl() {
        return getenv('SIM_SKRIPSI_API_URL') ?: ($_ENV['SIM_SKRIPSI_API_URL'] ?? 'https://sim-skripsi-be.vercel.app/api');
    }

    /**
     * Base URL SSO SIM Mahasiswa (sim-mhs-api)
     */
    public static function getSsoUrl() {
        return getenv('SSO_API_URL') ?: ($_ENV['SSO_API_URL'] ?? 'https://sim-mhs-api.vercel.app/api/v1');
    }

    /**
     * Base URL file uploads pada backend
     */
    public static function getUploadsUrl() {
        $base = self::getBaseUrl();
        return preg_replace('#/api/?$#', '', $base);
    }

    /**
     * Melakukan HTTP Request ke Backend SIM Skripsi atau SSO API.
     * Mendukung cURL dan fallback ke PHP Stream jika modul cURL belum diaktifkan di webserver.
     */
    public static function request($method, $endpoint, $data = [], $token = null, $files = []) {
        $url = rtrim(self::getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        if (!$token && isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
        }

        if (function_exists('curl_init')) {
            return self::requestViaCurl($method, $url, $data, $token, $files);
        } else {
            return self::requestViaStream($method, $url, $data, $token, $files);
        }
    }

    /**
     * Eksekusi HTTP request menggunakan cURL
     */
    private static function requestViaCurl($method, $url, $data = [], $token = null, $files = []) {
        $headers = [
            'Accept: application/json'
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();

        if ($method === 'GET') {
            if (!empty($data)) {
                $query = http_build_query($data);
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $query;
            }
        } elseif (!empty($files)) {
            $postFields = $data;
            foreach ($files as $field => $fileData) {
                if (isset($fileData['tmp_name']) && file_exists($fileData['tmp_name'])) {
                    $mime = $fileData['type'] ?: 'application/pdf';
                    $postFields[$field] = new CURLFile($fileData['tmp_name'], $mime, $fileData['name']);
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
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
                'message' => 'Gagal terhubung ke Backend API: ' . $error
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
     * Fallback eksekusi HTTP request menggunakan PHP stream wrappers
     */
    private static function requestViaStream($method, $url, $data = [], $token = null, $files = []) {
        $headers = [
            'Accept: application/json',
            'User-Agent: SIM_SKRIPSI_FE/1.0'
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $content = null;

        if ($method === 'GET') {
            if (!empty($data)) {
                $query = http_build_query($data);
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $query;
            }
        } elseif (!empty($files)) {
            $boundary = '----WebKitFormBoundary' . md5(time() . microtime());
            $headers[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
            $body = '';

            foreach ($data as $name => $value) {
                $body .= "--$boundary\r\n";
                $body .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
                $body .= "$value\r\n";
            }

            foreach ($files as $name => $file) {
                if (isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
                    $filename = basename($file['name']);
                    $mime = $file['type'] ?: 'application/octet-stream';
                    $fileContent = file_get_contents($file['tmp_name']);
                    $body .= "--$boundary\r\n";
                    $body .= "Content-Disposition: form-data; name=\"$name\"; filename=\"$filename\"\r\n";
                    $body .= "Content-Type: $mime\r\n\r\n";
                    $body .= $fileContent . "\r\n";
                }
            }
            $body .= "--$boundary--\r\n";
            $content = $body;
        } else {
            $headers[] = 'Content-Type: application/json';
            $content = json_encode($data);
        }

        $opts = [
            'http' => [
                'method'        => $method,
                'header'        => implode("\r\n", $headers) . "\r\n",
                'content'       => in_array($method, ['POST', 'PUT', 'PATCH']) ? $content : null,
                'ignore_errors' => true,
                'timeout'       => 30
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ];

        $context = stream_context_create($opts);
        $level = error_reporting(0);
        $response = file_get_contents($url, false, $context);
        error_reporting($level);

        $httpCode = 0;
        if (isset($http_response_header) && is_array($http_response_header)) {
            if (preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $matches)) {
                $httpCode = (int)$matches[1];
            }
        }

        if ($response === false) {
            $err = error_get_last();
            return [
                'success' => false,
                'status'  => false,
                'message' => 'Gagal terhubung ke Backend API via Stream: ' . ($err['message'] ?? 'Unknown network error')
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
        $payload = ['username' => $username, 'password' => $password];

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
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
        } else {
            $opts = [
                'http' => [
                    'method'        => 'POST',
                    'header'        => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content'       => json_encode($payload),
                    'ignore_errors' => true,
                    'timeout'       => 15
                ],
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false
                ]
            ];
            $context = stream_context_create($opts);
            $level = error_reporting(0);
            $response = file_get_contents($url, false, $context);
            error_reporting($level);

            if ($response === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghubungi server SSO via Stream'
                ];
            }

            return json_decode($response, true);
        }
    }
}
