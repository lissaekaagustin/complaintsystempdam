<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['teknisi']);

$id = $_GET['id'] ?? null;
$id_teknisi = current_user_id();

$stmt = $pdo->prepare("
    SELECT p.*, pl.nama_pelanggan, pl.id_pelanggan, pl.no_hp, pl.alamat_pelanggan,
           pl.id_user AS id_user_pelanggan, k.nama_kategori
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    WHERE p.id_pengaduan = ? AND p.id_teknisi = ?
");
$stmt->execute([$id, $id_teknisi]);
$pengaduan = $stmt->fetch();

if (!$pengaduan) {
    flash('error', 'Tugas pengaduan tidak ditemukan.');
    redirect('teknisi/tugas.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $keterangan = trim($_POST['keterangan'] ?? '');
        $status_baru = $_POST['status_pengaduan'] ?? 'Diproses';

        if (!$keterangan) {
            throw new Exception('Keterangan tindak lanjut wajib diisi.');
        }

        if (!in_array($status_baru, ['Diproses', 'Selesai'])) {
            throw new Exception('Status tidak valid.');
        }

        $foto = upload_image('foto_tindak_lanjut');
        $status_sebelum = $pengaduan['status_pengaduan'];

        $stmt = $pdo->prepare("
            UPDATE pengaduan
            SET status_pengaduan=?, updated_at=NOW()
            WHERE id_pengaduan=?
        ");
        $stmt->execute([$status_baru, $id]);

        insert_tindak_lanjut($pdo, $id, current_user_id(), $status_sebelum, $status_baru, $keterangan, $foto);
        create_notifikasi($pdo, $pengaduan['id_user_pelanggan'], $id, 'Pengaduan ' . $pengaduan['kode_pengaduan'] . ' diperbarui oleh teknisi.');

        flash('success', 'Tindak lanjut berhasil disimpan.');
        redirect('teknisi/tugas_detail.php?id=' . $id);
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        redirect('teknisi/tugas_detail.php?id=' . $id);
    }
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
        <h2>Detail Tugas Pengaduan</h2>

        <div class="detail-list">
            <strong>Kode Pengaduan</strong><span><?= e($pengaduan['kode_pengaduan']); ?></span>
            <strong>Nama Pelanggan</strong><span><?= e($pengaduan['nama_pelanggan']); ?></span> 
            <strong>ID Pelanggan</strong><span><?= e($pengaduan['id_pelanggan']); ?></span>
            <strong>No HP</strong><span><?= e($pengaduan['no_hp']); ?></span>
            <strong>Kategori</strong><span><?= e($pengaduan['nama_kategori']); ?></span>
            <strong>Isi Pengaduan</strong><span><?= nl2br(e($pengaduan['isi_pengaduan'])); ?></span>
            <strong>Lokasi</strong><span><?= nl2br(e($pengaduan['alamat_lokasi'])); ?></span>
            <strong>Status</strong><span><?= badge_status($pengaduan['status_pengaduan']); ?></span>
            <strong>Tanggal</strong><span><?= e($pengaduan['tanggal_pengaduan']); ?></span>
        </div>

        <?php if ($pengaduan['foto_pengaduan']): ?>
            <p><b>Foto Pengaduan:</b></p>
            <img class="image-preview" src="<?= upload_url($pengaduan['foto_pengaduan']); ?>" alt="Foto Pengaduan">
        <?php endif; ?>
    </div>

    <?php if ($pengaduan['status_pengaduan'] !== 'Selesai'): ?>
        <div class="card">
            <h3>Isi Tindak Lanjut</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Keterangan Penanganan</label>
                    <textarea name="keterangan" placeholder="Contoh: Pipa distribusi diperiksa dan sudah dilakukan perbaikan." required></textarea>
                </div>

                <div class="form-group">
                    <label>Status Pengaduan</label>
                    <select name="status_pengaduan" required>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Foto Bukti Penanganan</label>
                    <input type="file" name="foto_tindak_lanjut" accept=".jpg,.jpeg,.png,.webp">
                     <small>Format: jpg, jpeg, png, webp. Maksimal 50MB.</small>
                </div>

                <button class="btn" type="submit">Simpan Tindak Lanjut</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Riwayat Tindak Lanjut</h3>
        <div class="timeline">
            <?php foreach ($timeline as $tl): ?>
                <div class="timeline-item">
                    <b><?= e($tl['nama']); ?> (<?= e($tl['role']); ?>)</b><br>
                    <small><?= e($tl['tanggal_tindak_lanjut']); ?> | <?= e($tl['status_sebelum']); ?> → <?= e($tl['status_sesudah']); ?></small>
                    <p><?= nl2br(e($tl['keterangan'])); ?></p>
                    <?php if ($tl['foto_tindak_lanjut']): ?>
                        <img class="image-preview" src="<?= upload_url($tl['foto_tindak_lanjut']); ?>" alt="Foto Tindak Lanjut">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <a class="btn btn-secondary" href="<?= asset('teknisi/tugas.php'); ?>">Kembali</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
