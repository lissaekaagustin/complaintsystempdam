<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$id = $_GET['id'] ?? null;
$data = [
    'nama' => '',
    'email' => '',
    'username' => '',
    'no_hp' => '',
    'alamat' => '',
    'status' => 'aktif'
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ? AND role = 'teknisi'");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if (!$data) {
        flash('error', 'Data teknisi tidak ditemukan.');
        redirect('admin/teknisi.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $status = $_POST['status'] ?? 'aktif';

    try {
        if (!$nama || !$email || !$username || !$no_hp || !$alamat) {
            throw new Exception('Data teknisi belum lengkap.');
        }

        if ($id) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users SET nama=?, email=?, username=?, password=?, no_hp=?, alamat=?, status=?
                    WHERE id_user=? AND role='teknisi'
                ");
                $stmt->execute([$nama, $email, $username, $hash, $no_hp, $alamat, $status, $id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users SET nama=?, email=?, username=?, no_hp=?, alamat=?, status=?
                    WHERE id_user=? AND role='teknisi'
                ");
                $stmt->execute([$nama, $email, $username, $no_hp, $alamat, $status, $id]);
            }
            flash('success', 'Data teknisi berhasil diperbarui.');
        } else {
            if (!$password) throw new Exception('Password wajib diisi untuk teknisi baru.');

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (nama, email, username, password, role, no_hp, alamat, status, created_at)
                VALUES (?, ?, ?, ?, 'teknisi', ?, ?, ?, NOW())
            ");
            $stmt->execute([$nama, $email, $username, $hash, $no_hp, $alamat, $status]);

            flash('success', 'Data teknisi berhasil ditambahkan.');
        }

        redirect('admin/teknisi.php');
    } catch (Exception $e) {
        flash('error', $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="card">
        <h2><?= $id ? 'Edit' : 'Tambah'; ?> Teknisi</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <form method="post">
            <div class="grid grid-2">
                <div class="form-group">
                    <label>Nama Teknisi</label>
                    <input type="text" name="nama" value="<?= e($data['nama']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= e($data['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= e($data['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Password <?= $id ? '(kosongkan jika tidak diganti)' : ''; ?></label>
                    <input type="password" name="password" <?= $id ? '' : 'required'; ?>>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" value="<?= e($data['no_hp']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="aktif" <?= $data['status'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?= $data['status'] === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" required><?= e($data['alamat']); ?></textarea>
            </div>

            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="<?= asset('admin/teknisi.php'); ?>">Kembali</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
