<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['teknisi']);

$id_teknisi = current_user_id();

$stmt = $pdo->prepare("
    SELECT p.*, pl.nama_pelanggan, pl.id_pelanggan, k.nama_kategori
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    WHERE p.id_teknisi = ?
    ORDER BY p.tanggal_pengaduan DESC
");
$stmt->execute([$id_teknisi]);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div style="width:100vw !important; max-width:100vw !important; margin-left:calc(50% - 50vw) !important; margin-right:calc(50% - 50vw) !important; padding:28px 32px !important;">
    <div class="card" style="width:100% !important; max-width:none !important;">
        <h2>Daftar Tugas Pengaduan</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Isi Pengaduan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= e($row['kode_pengaduan']); ?></td>
                            <td><?= e($row['id_pelanggan']); ?><br>
                            <td><?= e($row['nama_pelanggan']); ?><br>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= e($row['isi_pengaduan']); ?></td>
                            <td><?= badge_status($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                            <td><a class="btn btn-sm" href="<?= asset('teknisi/tugas_detail.php?id=' . $row['id_pengaduan']); ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$data): ?>
                        <tr><td colspan="7">Belum ada tugas pengaduan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
