<?php
// ==========================================================
// Konfigurasi Aplikasi
// ==========================================================
// Jika nama folder project di htdocs diganti,
// ubah BASE_URL sesuai nama folder tersebut.
// Contoh: http://localhost/pdam_complaint_system

define('APP_NAME', 'Web-Based Customer Complaint System PDAM');
define('APP_SHORT_NAME', 'Pengaduan PDAM Tirta Gemilang');
define('BASE_URL', '/pdam_complaint_system');

define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Gambar background untuk halaman awal.
// Cara mengganti: letakkan gambar baru di folder assets/img,
// lalu ubah nama file di bawah ini sesuai nama gambar tersebut.
define('HOME_BACKGROUND_IMAGE', 'assets/img/backgroundhome.png');

date_default_timezone_set('Asia/Jakarta');
?>
