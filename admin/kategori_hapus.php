<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$id = $_GET['id'] ?? null;

try {
    if (!$id) throw new Exception('ID kategori tidak valid.');

    $stmt = $pdo->prepare("DELETE FROM kategori_pengaduan WHERE id_kategori = ?");
    $stmt->execute([$id]);

    flash('success', 'Kategori berhasil dihapus.');
} catch (Exception $e) {
    flash('error', 'Kategori tidak bisa dihapus karena sudah dipakai pada data pengaduan.');
}

redirect('admin/kategori.php');
?>
