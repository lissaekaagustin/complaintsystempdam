<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['teknisi']);

$id_teknisi = current_user_id();

$total = count_data($pdo, 'pengaduan', "id_teknisi = {$id_teknisi}");
$tugas = count_data($pdo, 'pengaduan', "id_teknisi = {$id_teknisi} AND status_pengaduan = 'Diajukan'");
$diproses = count_data($pdo, 'pengaduan', "id_teknisi = {$id_teknisi} AND status_pengaduan = 'Diproses'");
$selesai = count_data($pdo, 'pengaduan', "id_teknisi = {$id_teknisi} AND status_pengaduan = 'Selesai'");

$stmt = $pdo->prepare("
    SELECT p.*, pl.nama_pelanggan, pl.id_pelanggan, k.nama_kategori
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    WHERE p.id_teknisi = ?
    ORDER BY p.tanggal_pengaduan DESC
    LIMIT 5
");
$stmt->execute([$id_teknisi]);
$recent = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<style>
    .teknisi-dashboard-full {
        width: 100% !important;
        max-width: none !important;
        margin: 28px 0 !important;
        padding: 0 40px !important;
    }

    .teknisi-dashboard-full .hero {
        background: #ffffff !important;
        color: #0f172a !important;
        width: 100% !important;
        max-width: none !important;
    }

    .teknisi-dashboard-full .hero h1,
    .teknisi-dashboard-full .hero p {
        color: #0f172a !important;
    }

    .teknisi-dashboard-full .grid-3 {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 18px !important;
        margin-bottom: 28px !important;
    }

    .teknisi-dashboard-full .card,
    .teknisi-dashboard-full .stat-card {
        width: 100% !important;
        max-width: none !important;
    }
</style>
<div class="teknisi-dashboard-full">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="hero">
        <h1>Dashboard Teknisi</h1>
        <p>Melihat tugas pengaduan dan mengisi hasil tindak lanjut setelah melakukan penanganan.</p>
    </div>

    <div class="grid grid-3">
        <div class="stat-card"><h3>Total Tugas</h3><div class="number"><?= $total; ?></div></div>
        <div class="stat-card"><h3>Diproses</h3><div class="number"><?= $diproses; ?></div></div>
        <div class="stat-card"><h3>Selesai</h3><div class="number"><?= $selesai; ?></div></div>
    </div>

    <div class="card">
        <h3>Data Tugas</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $row): ?>
                        <tr>
                            <td><?= e($row['kode_pengaduan']); ?></td>
                            <td><?= e($row['id_pelanggan']); ?></small></td>
                            <td><?= e($row['nama_pelanggan']); ?><br><small>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= badge_status($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                            <td><a class="btn btn-sm" href="<?= asset('teknisi/tugas_detail.php?id=' . $row['id_pengaduan']); ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <tr><td colspan="6">Belum ada tugas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
