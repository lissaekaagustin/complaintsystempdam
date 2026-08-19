<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE users SET status = 'nonaktif' WHERE id_user = ? AND role = 'teknisi'");
    $stmt->execute([$id]);
    flash('success', 'Akun teknisi berhasil dinonaktifkan.');
}

redirect('admin/teknisi.php');
?>
