<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

if (is_logged_in()) {
    redirect(role_redirect_path(current_role()));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_pelanggan = trim($_POST['id_pelanggan'] ?? '');

    try {
        if (!$nama || !$email || !$username || !$password || !$no_hp || !$alamat || !$id_pelanggan) {
            throw new Exception('Semua field wajib diisi.');
        }

        $cek = $pdo->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
        $cek->execute([$username, $email]);
        if ($cek->fetch()) {
            throw new Exception('Username atau email sudah terdaftar.');
        }

        $pdo->beginTransaction();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, email, username, password, role, no_hp, alamat, status, created_at)
            VALUES (?, ?, ?, ?, 'pelanggan', ?, ?, 'aktif', NOW())
        ");
        $stmt->execute([$nama, $email, $username, $hash, $no_hp, $alamat]);

        $id_user = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO pelanggan (id_user, id_pelanggan, nama_pelanggan, alamat_pelanggan, no_hp, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$id_user, $id_pelanggan, $nama, $alamat, $no_hp]);

        $pdo->commit();

        flash('success', 'Registrasi berhasil. Silakan login.');
        redirect('login.php');
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('error', $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
    body {
        background:
            linear-gradient(rgba(3, 32, 57, 0.55), rgba(15, 76, 129, 0.45)),
            url("<?= asset('assets/img/backgroundhome2.png'); ?>") !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
    }

    .register-page {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 45px 18px 35px;
    }

    .register-page .auth-wrapper {
        max-width: 470px;
        width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    .register-page .card {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(3, 32, 57, 0.25);
    }
</style>

<main class="register-page">
<div class="auth-wrapper">
    <div class="card">
        <h2>Registrasi Pelanggan</h2>
        <?php require_once __DIR__ . '/includes/alerts.php'; ?>

        <form method="post">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" required>
            </div>

            <div class="form-group">
                <label>ID Pelanggan</label>
                <input type="text" name="id_pelanggan" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" required></textarea>
            </div>

            <button class="btn" type="submit" 
    style="background:#16a34a !important; color:#ffffff !important; border:none !important; font-weight:bold !important;">
    Daftar
</button>
            <a href="<?= asset('login.php'); ?>" 
   class="btn btn-secondary"
   style="background:#0f4c81 !important; color:#ffffff !important; border:none !important; font-weight:bold !important;">
   Kembali Login
</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
