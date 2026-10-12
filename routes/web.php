<?php

return [
    // Dashboard & Beranda
    ['method' => 'GET',  'path' => '/',            'controller' => 'DashboardController',      'action' => 'index'],
    ['method' => 'GET',  'path' => '/dashboard',   'controller' => 'DashboardController',      'action' => 'index'],

    // Autentikasi SSO & Quick Login
    ['method' => 'GET',  'path' => '/login',       'controller' => 'AuthController',           'action' => 'login'],
    ['method' => 'POST', 'path' => '/login',       'controller' => 'AuthController',           'action' => 'postLogin'],
    ['method' => 'GET',  'path' => '/logout',      'controller' => 'AuthController',           'action' => 'logout'],
    ['method' => 'GET',  'path' => '/quick-login/([a-zA-Z0-9_-]+)', 'controller' => 'AuthController', 'action' => 'quickLogin'],

    // Modul Pengajuan Judul Skripsi
    ['method' => 'GET',  'path' => '/pengajuan',           'controller' => 'PengajuanJudulController', 'action' => 'index'],
    ['method' => 'GET',  'path' => '/pengajuan/tambah',    'controller' => 'PengajuanJudulController', 'action' => 'create'],
    ['method' => 'POST', 'path' => '/pengajuan/tambah',    'controller' => 'PengajuanJudulController', 'action' => 'store'],
    ['method' => 'GET',  'path' => '/pengajuan/detail/(\d+)', 'controller' => 'PengajuanJudulController', 'action' => 'detail'],
    ['method' => 'POST', 'path' => '/pengajuan/verifikasi/(\d+)', 'controller' => 'PengajuanJudulController', 'action' => 'verifikasi'],

    // Modul Logbook Bimbingan
    ['method' => 'GET',  'path' => '/bimbingan',           'controller' => 'BimbinganController',      'action' => 'index'],
    ['method' => 'GET',  'path' => '/bimbingan/tambah',    'controller' => 'BimbinganController',      'action' => 'create'],
    ['method' => 'POST', 'path' => '/bimbingan/tambah',    'controller' => 'BimbinganController',      'action' => 'store'],
    ['method' => 'POST', 'path' => '/bimbingan/acc/(\d+)', 'controller' => 'BimbinganController',      'action' => 'acc'],

    // Modul Pendaftaran Sidang & Seminar
    ['method' => 'GET',  'path' => '/seminar',             'controller' => 'SeminarController',        'action' => 'index'],
    ['method' => 'GET',  'path' => '/seminar/daftar',      'controller' => 'SeminarController',        'action' => 'create'],
    ['method' => 'POST', 'path' => '/seminar/daftar',      'controller' => 'SeminarController',        'action' => 'store'],
    ['method' => 'POST', 'path' => '/seminar/jadwal/(\d+)', 'controller' => 'SeminarController',       'action' => 'updateJadwal'],

    // Modul Penilaian Sidang
    ['method' => 'GET',  'path' => '/nilai',               'controller' => 'NilaiController',          'action' => 'index'],
    ['method' => 'POST', 'path' => '/nilai/input/(\d+)',   'controller' => 'NilaiController',          'action' => 'inputNilai']
];
