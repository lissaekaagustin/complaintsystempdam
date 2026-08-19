<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$id = $_GET['id'] ?? null;
if (!$id) redirect('admin/pengaduan.php');

$stmt = $pdo->prepare("
    SELECT p.*, pl.nama_pelanggan, pl.id_pelanggan, pl.no_hp, pl.alamat_pelanggan,
           k.nama_kategori, u.id_user AS id_user_pelanggan, t.nama AS nama_teknisi
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN users u ON pl.id_user = u.id_user
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    LEFT JOIN users t ON p.id_teknisi = t.id_user
    WHERE p.id_pengaduan = ?
");
$stmt->execute([$id]);
$pengaduan = $stmt->fetch();

if (!$pengaduan) {
    flash('error', 'Pengaduan tidak ditemukan.');
    redirect('admin/pengaduan.php');
}

$teknisi = $pdo->query("SELECT * FROM users WHERE role='teknisi' AND status='aktif' ORDER BY nama ASC")->fetchAll();

$stmt = $pdo->prepare("
    SELECT tl.*, u.nama, u.role
    FROM tindak_lanjut tl
    JOIN users u ON tl.id_user = u.id_user
    WHERE tl.id_pengaduan = ?
    ORDER BY tl.tanggal_tindak_lanjut ASC
");
$stmt->execute([$id]);
$timeline = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    try {
        $status_sebelum = $pengaduan['status_pengaduan'];

        if ($aksi === 'tolak') {
            $alasan = trim($_POST['alasan_penolakan'] ?? '');
            if (!$alasan) throw new Exception('Alasan penolakan wajib diisi.');

            $stmt = $pdo->prepare("
                UPDATE pengaduan
                SET status_pengaduan='Ditolak', alasan_penolakan=?, updated_at=NOW()
                WHERE id_pengaduan=?
            ");
            $stmt->execute([$alasan, $id]);

            insert_tindak_lanjut($pdo, $id, current_user_id(), $status_sebelum, 'Ditolak', 'Pengaduan ditolak. Alasan: ' . $alasan);
        
            flash('success', 'Pengaduan berhasil ditolak.');
        }

        if ($aksi === 'tugaskan') {
            $id_teknisi = $_POST['id_teknisi'] ?? '';
            $catatan = trim($_POST['catatan'] ?? '');

            if (!$id_teknisi) throw new Exception('Teknisi wajib dipilih.');

            $stmt = $pdo->prepare("
                UPDATE pengaduan
                SET id_teknisi=?, status_pengaduan='Diproses', updated_at=NOW()
                WHERE id_pengaduan=?
            ");
            $stmt->execute([$id_teknisi, $id]);

            insert_tindak_lanjut($pdo, $id, current_user_id(), $status_sebelum, 'Diproses', 'Pengaduan ditugaskan kepada teknisi. ' . $catatan);
            flash('success', 'Pengaduan berhasil ditugaskan kepada teknisi.');
        }

        if ($aksi === 'selesai') {
            $stmt = $pdo->prepare("
                UPDATE pengaduan SET status_pengaduan='Selesai', updated_at=NOW()
                WHERE id_pengaduan=?
            ");
            $stmt->execute([$id]);

            insert_tindak_lanjut($pdo, $id, current_user_id(), $status_sebelum, 'Selesai', 'Pengaduan dinyatakan selesai oleh admin/petugas.');
    
            flash('success', 'Status pengaduan berhasil diubah menjadi selesai.');
        }

        redirect('admin/pengaduan_detail.php?id=' . $id);
    } catch (Exception $e) {
        flash('error', $e->getMessage());
        redirect('admin/pengaduan_detail.php?id=' . $id);
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card">
        <h2>Detail Pengaduan</h2>

        <div class="detail-list">
            <strong>Kode Pengaduan</strong><span><?= e($pengaduan['kode_pengaduan']); ?></span>
            <strong>ID Pelanggan</strong><span><?= e($pengaduan['id_pelanggan']); ?></span>
            <strong>No HP</strong><span><?= e($pengaduan['no_hp']); ?></span>
            <strong>Kategori</strong><span><?= e($pengaduan['nama_kategori']); ?></span>
            <strong>Isi Pengaduan</strong><span><?= nl2br(e($pengaduan['isi_pengaduan'])); ?></span>
            <strong>Lokasi</strong><span><?= nl2br(e($pengaduan['alamat_lokasi'])); ?></span>
            <strong>Status</strong><span><?= badge_status($pengaduan['status_pengaduan']); ?></span>
            <strong>Teknisi</strong><span><?= e($pengaduan['nama_teknisi'] ?? '-'); ?></span>
            <strong>Tanggal</strong><span><?= e($pengaduan['tanggal_pengaduan']); ?></span>
            <?php if ($pengaduan['alasan_penolakan']): ?>
                <strong>Alasan Penolakan</strong><span><?= nl2br(e($pengaduan['alasan_penolakan'])); ?></span>
            <?php endif; ?>
        </div>

        <?php if ($pengaduan['foto_pengaduan']): ?>
            <p><b>Foto Pengaduan:</b></p>
            <img class="image-preview" src="<?= upload_url($pengaduan['foto_pengaduan']); ?>" alt="Foto Pengaduan">
        <?php endif; ?>
    </div>

    <?php if ($pengaduan['status_pengaduan'] !== 'Selesai' && $pengaduan['status_pengaduan'] !== 'Ditolak'): ?>
        <div class="grid grid-2">
            <div class="card">
                <h3>Tugaskan Teknisi</h3>
                <form method="post">
                    <input type="hidden" name="aksi" value="tugaskan">

                    <div class="form-group">
                        <label>Pilih Teknisi</label>
                        <select name="id_teknisi" required>
                            <option value="">-- Pilih Teknisi --</option>
                            <?php foreach ($teknisi as $t): ?>
                                <option value="<?= $t['id_user']; ?>" <?= $pengaduan['id_teknisi'] == $t['id_user'] ? 'selected' : ''; ?>>
                                    <?= e($t['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan Admin/Petugas</label>
                        <textarea name="catatan" placeholder="Contoh: Mohon cek ke lokasi pelanggan hari ini."></textarea>
                    </div>

                    <button class="btn" type="submit">Simpan Penugasan</button>
                </form>
            </div>

            <div class="card">
                <h3>Tolak Pengaduan</h3>
                <form method="post">
                    <input type="hidden" name="aksi" value="tolak">

                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea name="alasan_penolakan" placeholder="................" required></textarea>
                    </div>

                    <button class="btn btn-danger" type="submit">Tolak Pengaduan</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($pengaduan['status_pengaduan'] === 'Diproses'): ?>
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
            <?php if (!$timeline): ?>
                <p>Belum ada tindak lanjut.</p>
            <?php endif; ?>
        </div>
    </div>

    <a class="btn btn-secondary" href="<?= asset('admin/pengaduan.php'); ?>">Kembali</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
