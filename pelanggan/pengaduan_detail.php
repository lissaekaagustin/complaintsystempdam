<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['pelanggan']);

$pelanggan = get_pelanggan_by_user($pdo, current_user_id());

if (!$pelanggan) {
    flash('error', 'Data pelanggan tidak ditemukan.');
    redirect('logout.php');
}

$id = $_GET['id'] ?? null;

if (!$id) {
    flash('error', 'ID pengaduan tidak ditemukan.');
    redirect('pelanggan/pengaduan_saya.php');
}

$stmt = $pdo->prepare("
    SELECT p.*, k.nama_kategori, t.nama AS nama_teknisi
    FROM pengaduan p
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    LEFT JOIN users t ON p.id_teknisi = t.id_user
    WHERE p.id_pengaduan = ? AND p.id_pelanggan = ?
");
$stmt->execute([$id, $pelanggan['id_pelanggan']]);
$pengaduan = $stmt->fetch();

if (!$pengaduan) {
    flash('error', 'Pengaduan tidak ditemukan.');
    redirect('pelanggan/pengaduan_saya.php');
}

$stmt = $pdo->prepare("
    SELECT tl.*, u.nama, u.role
    FROM tindak_lanjut tl
    JOIN users u ON tl.id_user = u.id_user
    WHERE tl.id_pengaduan = ?
    ORDER BY tl.tanggal_tindak_lanjut ASC
");
$stmt->execute([$id]);
$timeline = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card">
        <h2>Detail Pengaduan Saya</h2>

        <div class="detail-list">
            <strong>Kode Pengaduan</strong><span><?= e($pengaduan['kode_pengaduan']); ?></span>
            <strong>ID Pelanggan</strong><span><?= e($pelanggan['id_pelanggan'] ?? '-'); ?></span>
            <strong>Kategori</strong><span><?= e($pengaduan['nama_kategori']); ?></span>
            <strong>Isi Pengaduan</strong><span><?= nl2br(e($pengaduan['isi_pengaduan'])); ?></span>
            <strong>Lokasi</strong><span><?= nl2br(e($pengaduan['alamat_lokasi'])); ?></span>
            <strong>Status</strong><span><?= badge_status($pengaduan['status_pengaduan']); ?></span>
            <strong>Teknisi</strong><span><?= e($pengaduan['nama_teknisi'] ?? '-'); ?></span>
            <strong>Tanggal</strong><span><?= e($pengaduan['tanggal_pengaduan']); ?></span>

            <?php if (!empty($pengaduan['alasan_penolakan'])): ?>
                <strong>Alasan Penolakan</strong><span><?= nl2br(e($pengaduan['alasan_penolakan'])); ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($pengaduan['foto_pengaduan'])): ?>
            <p><b>Foto Pengaduan:</b></p>
            <img class="image-preview" src="<?= upload_url($pengaduan['foto_pengaduan']); ?>" alt="Foto Pengaduan">
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Riwayat Status / Tindak Lanjut</h3>

        <div class="timeline">
            <?php foreach ($timeline as $tl): ?>
                <div class="timeline-item">
                    <b><?= e($tl['nama']); ?> (<?= e($tl['role']); ?>)</b><br>
                    <small>
                        <?= e($tl['tanggal_tindak_lanjut']); ?> |
                        <?= e($tl['status_sebelum']); ?> → <?= e($tl['status_sesudah']); ?>
                    </small>
                    <p><?= nl2br(e($tl['keterangan'])); ?></p>

                    <?php if (!empty($tl['foto_tindak_lanjut'])): ?>
                        <img class="image-preview" src="<?= upload_url($tl['foto_tindak_lanjut']); ?>" alt="Foto Tindak Lanjut">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (!$timeline): ?>
                <p>Belum ada riwayat tindak lanjut.</p>
            <?php endif; ?>
        </div>
    </div>

    <a class="btn btn-secondary" href="<?= asset('pelanggan/pengaduan_saya.php'); ?>">Kembali</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>