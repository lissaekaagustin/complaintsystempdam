<?php $user = current_user(); ?>
<nav class="navbar" style="background: #0F4C81; !important;">
    <div class="nav-brand">
        <a href="<?= asset('dashboard.php'); ?>"><?= e(APP_SHORT_NAME); ?></a>
    </div>

    <?php if ($user): ?>
        <div class="nav-menu">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="<?= asset('admin/dashboard.php'); ?>">Dashboard</a>
                <a href="<?= asset('admin/pengaduan.php'); ?>">Pengaduan</a>
                <a href="<?= asset('admin/kategori.php'); ?>">Kategori</a>
                <a href="<?= asset('admin/pelanggan.php'); ?>">Pelanggan</a>
                <a href="<?= asset('admin/teknisi.php'); ?>">Teknisi</a>
                <a href="<?= asset('admin/laporan.php'); ?>">Laporan</a>
            <?php elseif ($user['role'] === 'teknisi'): ?>
                <a href="<?= asset('teknisi/dashboard.php'); ?>">Dashboard</a>
                <a href="<?= asset('teknisi/tugas.php'); ?>">Tugas</a>
                <a href="<?= asset('teknisi/riwayat_penugasan.php'); ?>">Riwayat Penugasan</a>
            <?php else: ?>
                <a href="<?= asset('pelanggan/dashboard.php'); ?>">Dashboard</a>
                <a href="<?= asset('pelanggan/pengaduan_buat.php'); ?>">Buat Pengaduan</a>
                <a href="<?= asset('pelanggan/pengaduan_saya.php'); ?>">Riwayat</a>
                <a href="<?= asset('pelanggan/profil.php'); ?>">Profil</a>
            <?php endif; ?>

            <span class="nav-user"><?= e($user['nama']); ?> (<?= e($user['role']); ?>)</span>
            <a href="<?= asset('logout.php'); ?>" class="btn-logout">Logout</a>
        </div>
    <?php endif; ?>
</nav>
