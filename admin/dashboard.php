<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$totalPengaduan = count_data($pdo, 'pengaduan');
$totalDiajukan = count_data($pdo, 'pengaduan', "status_pengaduan = 'Diajukan'");
$totalDiproses = count_data($pdo, 'pengaduan', "status_pengaduan = 'Diproses'");
$totalSelesai = count_data($pdo, 'pengaduan', "status_pengaduan = 'Selesai'");

$stmt = $pdo->query("
    SELECT p.*, 
           u.nama AS nama_pelanggan,
           k.nama_kategori
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN users u ON pl.id_user = u.id_user
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    ORDER BY p.tanggal_pengaduan DESC
    LIMIT 5
");
$recent = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<style>
    main {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .admin-dashboard-full {
        width: 100% !important;
        max-width: none !important;
        margin: 28px 0 !important;
        padding: 0 40px !important;
    }

    .admin-dashboard-full .hero,
    .admin-dashboard-full .card,
    .admin-dashboard-full .grid {
        width: 100% !important;
        max-width: none !important;
    }

    .admin-dashboard-full > .hero {
        background: #ffffff !important;
        color: #0f172a !important;
    }

    .admin-dashboard-full > .hero h1,
    .admin-dashboard-full > .hero p {
        color: #0f172a !important;
    }

    .admin-dashboard-full .grid-3 {
    width: 100% !important;
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 18px !important;
    margin-bottom: 28px !important;
}

    .admin-dashboard-full .stat-card {
        width: 100% !important;
    }
</style>

<div class="admin-dashboard-full">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="hero">
        <h1>Dashboard Admin/Petugas</h1>
        <p>Memantau pengaduan pelanggan, melakukan verifikasi, menugaskan teknisi, dan laporan.</p>
    </div>

    <div class="grid grid-4"></div>
    <div class="grid grid-3">
        <div class="stat-card">
            <h3>Total Pengaduan</h3>
            <div class="number"><?= $totalPengaduan; ?></div>
        </div>
        <div class="stat-card">
            <h3>Pengaduan Baru</h3>
            <div class="number"><?= $totalDiajukan; ?></div>
        </div>
        <div class="stat-card">
            <h3>Diproses</h3>
            <div class="number"><?= $totalDiproses; ?></div>
        </div>
        <div class="stat-card">
            <h3>Selesai</h3>
            <div class="number"><?= $totalSelesai; ?></div>
        </div>
    </div>
    <div class="card">
        <h3>Pengaduan Terbaru</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
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
                            <td><?= e($row['id_pelanggan']); ?></td>
                            <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= badge_status($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                            <td><a class="btn btn-sm" href="<?= asset('admin/pengaduan_detail.php?id=' . $row['id_pengaduan']); ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <tr><td colspan="6">Belum ada pengaduan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
