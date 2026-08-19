<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['pelanggan']);

$pelanggan = get_pelanggan_by_user($pdo, current_user_id());
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_pelanggan = trim($_POST['id_pelanggan'] ?? '');

    try {
        if (!$nama || !$email || !$no_hp || !$alamat || !$id_pelanggan) {
            throw new Exception('Semua data wajib diisi.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE users SET nama=?, email=?, no_hp=?, alamat=? WHERE id_user=?");
        $stmt->execute([$nama, $email, $no_hp, $alamat, current_user_id()]);

        $stmt = $pdo->prepare("
            UPDATE pelanggan
            SET id_pelanggan=?, nama_pelanggan=?, alamat_pelanggan=?, no_hp=?
            WHERE id_pelanggan=?
        ");
        $stmt->execute([$id_pelanggan, $nama, $alamat, $no_hp, $pelanggan['id_pelanggan']]);

        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['email'] = $email;

        $pdo->commit();

        flash('success', 'Profil berhasil diperbarui.');
        redirect('pelanggan/profil.php');
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('error', $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div style="width:100vw !important; max-width:100vw !important; margin-left:calc(50% - 50vw) !important; margin-right:calc(50% - 50vw) !important; padding:28px 32px !important;">
    <div class="card" style="width:100% !important; max-width:none !important;">
        <h2>Profil Pelanggan</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <form method="post">
            <div class="grid grid-2">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?= e($pelanggan['nama_pelanggan']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= e($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" value="<?= e($pelanggan['no_hp']); ?>" required>
                </div>

                <div class="form-group">
                    <label>ID Pelanggan</label>
                    <input type="text" name="no_sambungan" value="<?= e($pelanggan['id_pelanggan']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" required><?= e($pelanggan['alamat_pelanggan']); ?></textarea>
            </div>

            <button class="btn" type="submit">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
