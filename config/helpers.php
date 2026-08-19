<?php
require_once __DIR__ . '/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function asset($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function upload_url($filename) {
    if (!$filename) return '';
    return UPLOAD_URL . '/' . ltrim($filename, '/');
}

function redirect($path = '') {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }

    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    return null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function current_user_id() {
    return $_SESSION['user']['id_user'] ?? null;
}

function current_role() {
    return $_SESSION['user']['role'] ?? null;
}

function auth_required($roles = []) {
    if (!is_logged_in()) {
        redirect('login.php');
    }

    if (!empty($roles) && !in_array(current_role(), $roles)) {
        flash('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        redirect('dashboard.php');
    }
}

function role_redirect_path($role) {
    if ($role === 'admin') return 'admin/dashboard.php';
    if ($role === 'teknisi') return 'teknisi/dashboard.php';
    return 'pelanggan/dashboard.php';
}

function badge_status($status) {
    $map = [
        'Diajukan' => 'badge badge-blue',
        'Diverifikasi' => 'badge badge-purple',
        'Diproses' => 'badge badge-orange',
        'Selesai' => 'badge badge-green',
        'Ditolak' => 'badge badge-red',
    ];

    $class = $map[$status] ?? 'badge';
    return '<span class="' . $class . '">' . e($status) . '</span>';
}

function get_pelanggan_by_user($pdo, $id_user) {
    $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_user = ?");
    $stmt->execute([$id_user]);
    return $stmt->fetch();
}

function generate_kode_pengaduan($pdo) {
    $tanggal = date('Ymd');
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM pengaduan
        WHERE DATE(tanggal_pengaduan) = CURDATE()
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    $urut = ((int)$row['total']) + 1;

    return 'PGD-' . $tanggal . '-' . str_pad($urut, 3, '0', STR_PAD_LEFT);
}

function upload_image($field_name) {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Gagal mengunggah file.');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $original = $_FILES[$field_name]['name'];
    $tmp = $_FILES[$field_name]['tmp_name'];
    $size = $_FILES[$field_name]['size'];

    if ($size > 2 * 1024 * 1024) {
        throw new Exception('Ukuran file maksimal 2MB.');
    }

    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        throw new Exception('Format file harus jpg, jpeg, png, atau webp.');
    }

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }

    $new_name = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $destination = UPLOAD_PATH . '/' . $new_name;

    if (!move_uploaded_file($tmp, $destination)) {
        throw new Exception('File gagal disimpan.');
    }

    return $new_name;
}

function insert_tindak_lanjut($pdo, $id_pengaduan, $id_user, $status_sebelum, $status_sesudah, $keterangan, $foto = null) {
    $stmt = $pdo->prepare("
        INSERT INTO tindak_lanjut
        (id_pengaduan, id_user, status_sebelum, status_sesudah, keterangan, foto_tindak_lanjut, tanggal_tindak_lanjut)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$id_pengaduan, $id_user, $status_sebelum, $status_sesudah, $keterangan, $foto]);
}

function count_data($pdo, $table, $where = '1=1') {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM {$table} WHERE {$where}");
    $row = $stmt->fetch();
    return (int)$row['total'];
}
?>
