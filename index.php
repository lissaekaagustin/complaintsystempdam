<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

if (is_logged_in()) {
    redirect(role_redirect_path(current_role()));
}

$homeBackground = asset(HOME_BACKGROUND_IMAGE);

require_once __DIR__ . '/includes/header.php';
?>

<style>
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #0f4c81 !important;
    }

    .home-bg-page {
        min-height: auto !important;
        background-size: cover !important;
        background-position: center top !important;
        background-repeat: no-repeat !important;
    }

    .home-overlay {
    min-height: auto !important;
    background: linear-gradient(
        90deg,
        rgba(3, 32, 57, 0.82),
        rgba(15, 76, 129, 0.58),
        rgba(14, 165, 233, 0.18)
    ) !important;
    color: #ffffff !important;
    padding-bottom: 30px !important;
}
    .home-content {
    padding: 35px 7% 90px !important;
}

    .home-feature-grid {
        margin-top: 28px !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .footer {
        margin-top: 0 !important;
        background: #eaf4ff !important;
        color: #0f4c81 !important;
        font-weight: bold !important;
    }
</style>

<div class="home-bg-page" style="background-image: url('<?= e($homeBackground); ?>');">
    <div class="home-overlay">
        <div class="home-navbar">
            <div class="home-logo" style="display: flex; align-items: center; gap: 10px;">
                <img 
                    src="<?= asset('assets/img/logopdam.jpeg'); ?>" 
                    alt="Logo PDAM"
                    style="width: 55px; height: 55px; object-fit: contain; background: #ffffff; padding: 3px; border-radius: 50%; display: block;"
                >

                <span style="color: #ffffff; font-size: 18px; font-weight: 700; line-height: 1;">
                    PDAM TIRTA GEMILANG PASAMAN BARAT
                </span>
            </div>

            <div class="home-nav-links">
                <a href="<?= asset('login.php'); ?>">Login</a>
                <a href="<?= asset('register.php'); ?>">Registrasi</a>
            </div>
        </div>

        <main class="home-content">
            <section class="home-hero-card">
                <h1>PENGADUAN PELANGGAN</h1>
                <p>
                    Membantu pelanggan dalam menyampaikan keluhan, memantau status pengaduan, serta membantu admin dan teknisi dalam menindaklanjuti laporan.
                </p>

                <div class="home-buttons">
                    <a href="<?= asset('login.php'); ?>" class="btn-home btn-home-primary">Login</a>
                    <a href="<?= asset('register.php'); ?>" class="btn-home btn-home-secondary">Registrasi Pelanggan</a>
                </div>
            </section>

            <section class="home-feature-grid">
                <div class="home-feature-card">
                    <h3>Pelanggan</h3>
                    <p>Mengirim pengaduan, upload bukti, dan melihat status penanganan.</p>
                </div>

                <div class="home-feature-card">
                    <h3>Admin/Petugas</h3>
                    <p>Mengelola pengaduan masuk, verifikasi laporan, dan penugasan teknisi.</p>
                </div>

                <div class="home-feature-card">
                    <h3>Teknisi</h3>
                    <p>Melihat tugas pengaduan dan mengisi hasil tindak lanjut penanganan.</p>
                </div>
            </section>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>