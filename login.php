<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

if (is_logged_in()) {
    redirect(role_redirect_path(current_role()));
}

$role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $roleDiizinkan = ['admin', 'pelanggan', 'teknisi'];

    if (!in_array($role, $roleDiizinkan, true)) {
        flash('error', 'Silakan pilih hak akses terlebih dahulu.');
    } else {
        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE (username = ? OR email = ?)
            AND role = ?
            AND status = 'aktif'
            LIMIT 1
        ");

        $stmt->execute([$username, $username, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];

            flash(
                'success',
                'Login berhasil. Selamat datang, ' . $user['nama'] . '.'
            );

            redirect(role_redirect_path($user['role']));
        } else {
            flash(
                'error',
                'Hak akses, username/email, atau password salah.'
            );
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<style>
    body {
        background:
            linear-gradient(
                rgba(3, 32, 57, 0.70),
                rgba(15, 76, 129, 0.55)
            ),
            url("<?= asset('assets/img/backgroundhome2.png'); ?>") !important;

        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
    }

    .auth-wrapper {
    width: 100% !important;
    max-width: 500px !important;
    margin: 35px auto 0 !important;
    padding: 0 10px !important;
}

.auth-wrapper .login-card {
    width: 100% !important;
    max-width: 500px !important;
    margin: 0 auto !important;
    padding: 22px 28px !important;
    border-radius: 15px !important;
}

.auth-wrapper .role-catalog {
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 10px !important;
}

.auth-wrapper .role-card {
    height: 78px !important;
    min-height: 78px !important;
}

.auth-wrapper .login-card input {
    height: 42px !important;
}

.auth-wrapper .login-card form .btn {
    width: 190px !important;
    margin: 16px auto 0 !important;
}

    .login-card {
        width: 100% !important;
        max-width: 620px !important;
        margin: 0 auto !important;
        padding: 24px 30px !important;
        border-radius: 18px !important;
    }

    .login-card h2 {
        margin-top: 0 !important;
        margin-bottom: 10px !important;
        font-size: 32px !important;
    }

    .login-card p {
        margin-top: 0 !important;
        margin-bottom: 12px !important;
        line-height: 1.25 !important;
        font-size: 18px !important;
    }

    .role-title {
        margin: 8px 0 8px !important;
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #1f2937 !important;
    }

    .role-catalog {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 12px !important;
        margin-bottom: 18px !important;
    }

    .role-option {
        cursor: pointer !important;
        width: 100% !important;
    }

    .role-option input {
        position: absolute !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .role-card {
        width: 100% !important;
        height: 78px !important;
        min-height: 78px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        padding: 8px 6px !important;
        background: #ffffff !important;
        border: 2px solid #c7dcf3 !important;
        border-radius: 12px !important;
        color: #155a91 !important;
        font-weight: 700 !important;
        text-align: center !important;
        font-size: 15px !important;
        box-shadow: 0 3px 8px rgba(15, 76, 129, 0.08) !important;
        transition: 0.2s ease !important;
    }

    .role-card svg {
        width: 22px !important;
        height: 22px !important;
        fill: currentColor !important;
    }

    .role-card:hover {
        border-color: #155a91 !important;
        background: #eef7ff !important;
    }

    .role-option input:checked + .role-card {
        color: #ffffff !important;
        background: #155a91 !important;
        border-color: #155a91 !important;
        box-shadow: 0 5px 12px rgba(21, 90, 145, 0.28) !important;
    }

    .form-group {
        margin-bottom: 10px !important;
    }

    .form-group label {
        margin-bottom: 6px !important;
        font-size: 18px !important;
    }

    .login-card input {
        height: 42px !important;
        padding: 9px 12px !important;
        font-size: 16px !important;
    }

    .login-card form .btn {
        display: block !important;
        width: 190px !important;
        margin: 16px auto 0 !important;
        text-align: center !important;
        padding: 10px 14px !important;
        font-size: 16px !important;
    }

    @media (max-width: 650px) {
        .auth-wrapper {
            max-width: 94% !important;
        }

        .role-catalog {
            grid-template-columns: 1fr !important;
        }

        .role-card {
            height: 72px !important;
            min-height: 72px !important;
        }
    }
    html,
body {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}
.password-wrapper {
    position: relative !important;
    width: 100% !important;
}

.password-wrapper input {
    width: 100% !important;
    padding-right: 48px !important;
}

.toggle-password {
    position: absolute !important;
    right: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    border: none !important;
    background: transparent !important;
    cursor: pointer !important;
    font-size: 18px !important;
    color: #155a91 !important;
    padding: 0 !important;
}
</style>

<div class="auth-wrapper">
    <div class="card login-card">
        <h2>Sign In</h2>

        <p>
            Masuk sesuai akun pelanggan, admin/petugas, atau teknisi.
        </p>

        <?php require_once __DIR__ . '/includes/alerts.php'; ?>

        <form method="post">
            <div class="role-catalog">

                <!-- Admin/Petugas -->
                <label class="role-option">
                    <input
                        type="radio"
                        name="role"
                        value="admin"
                        <?= $role === 'admin' ? 'checked' : ''; ?>
                        required
                    >

                    <span class="role-card">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 12c-5.33 0-8 2.67-8 5v2h11.1a7 7 0 0 1-.1-1.2c0-2.1.9-4 2.4-5.3A13.8 13.8 0 0 0 12 14zm7-1 4 2v3c0 2.8-1.7 5.2-4 6-2.3-.8-4-3.2-4-6v-3l4-2z"/>
                        </svg>

                        Admin/Petugas
                    </span>
                </label>

                <!-- Pelanggan -->
                <label class="role-option">
                    <input
                        type="radio"
                        name="role"
                        value="pelanggan"
                        <?= $role === 'pelanggan' ? 'checked' : ''; ?>
                        required
                    >

                    <span class="role-card">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-5.33 0-8 2.67-8 5v3h16v-3c0-2.33-2.67-5-8-5z"/>
                        </svg>

                        Pelanggan
                    </span>
                </label>

                <!-- Teknisi -->
                <label class="role-option">
                    <input
                        type="radio"
                        name="role"
                        value="teknisi"
                        <?= $role === 'teknisi' ? 'checked' : ''; ?>
                        required
                    >

                    <span class="role-card">
                        <svg viewBox="0 0 24 24">
                            <path d="M22.7 19.3l-6.4-6.4a6.5 6.5 0 0 1-8.2-8.2l3.7 3.7 2.6-.7.7-2.6-3.7-3.7a6.5 6.5 0 0 1 8.2 8.2l6.4 6.4a2.4 2.4 0 0 1-3.3 3.3zM5 14l-3 3v4h4l3-3-4-4z"/>
                        </svg>

                        Teknisi
                    </span>
                </label>
            </div>

            <div class="form-group">
                <label>Username atau Email</label>
                <input
                    type="text"
                    name="username"
                    value="<?= e($_POST['username'] ?? ''); ?>"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
             <div class="form-group">
    <label>Password</label>

    <div class="password-wrapper">
        <input
            type="password"
            name="password"
            id="password"
            required
        >

        <button type="button" class="toggle-password" onclick="togglePassword()">
            👁
        </button>
    </div>
</div>
            <button class="btn" type="submit">
                Login
            </button>
        </form>
    </div>
</div>

</main>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = '👁';
        }
    }
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>