<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['teknisi']);

$id_teknisi = current_user_id();

$stmt = $pdo->prepare("
    SELECT 
        p.*,
        k.nama_kategori,
        pl.nama_pelanggan,
        pl.id_pelanggan
    FROM pengaduan p
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    WHERE p.id_teknisi = ?
    ORDER BY p.updated_at DESC, p.tanggal_pengaduan DESC
");
$stmt->execute([$id_teknisi]);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div style="width:100vw !important; max-width:100vw !important; margin-left:calc(50% - 50vw) !important; margin-right:calc(50% - 50vw) !important; padding:28px 32px !important;">
    <div class="card" style="width:100% !important; max-width:none !important;">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card">
        <h2>Riwayat Penugasan Teknisi</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode Pengaduan</th>
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Tanggal Pengaduan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= e($row['kode_pengaduan']); ?></td>
                            <td><?= e($row['id_pelanggan']); ?></td>
                            <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= nl2br(e($row['alamat_lokasi'])); ?></td>
                            <td><?= badge_status($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                            <td>
                                <a class="btn btn-sm" href="<?= asset('teknisi/tugas_detail.php?id=' . $row['id_pengaduan']); ?>">Detail</a>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$data): ?>
                        <tr>
                            <td colspan="8">Belum ada penugasan untuk teknisi ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>