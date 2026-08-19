<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$status = $_GET['status'] ?? '';
$where = '';
$params = [];

if ($status) {
    $where = "WHERE p.status_pengaduan = ?";
    $params[] = $status;
}

$stmt = $pdo->prepare("
    SELECT 
        p.*,
        p.id_pelanggan AS id_pelanggan_tampil,
        pl.nama_pelanggan,
        k.nama_kategori,
        t.nama AS nama_teknisi
    FROM pengaduan p
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    LEFT JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    LEFT JOIN users t ON p.id_teknisi = t.id_user
    {$where}
    ORDER BY p.tanggal_pengaduan DESC
");
$stmt->execute($params);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div style="width:100vw !important; max-width:100vw !important; margin-left:calc(50% - 50vw) !important; margin-right:calc(50% - 50vw) !important; padding:28px 32px !important;">
    <div class="card" style="width:100% !important; max-width:none !important;">
        <h2>Data Pengaduan Masuk</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <form method="get" class="filter-box">
            <div class="form-group">
                <label>Filter Status</label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <?php foreach (['Diajukan','Diverifikasi','Diproses','Selesai','Ditolak'] as $s): ?>
                        <option value="<?= e($s); ?>" <?= $status === $s ? 'selected' : ''; ?>><?= e($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn" type="submit">Tampilkan</button>
            <a class="btn btn-secondary" href="<?= asset('admin/pengaduan.php'); ?>">Reset</a>
        </form>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= e($row['kode_pengaduan']); ?></td>
                            <td><?= e($row['id_pelanggan_tampil']); ?></td>
                             <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= e($row['nama_teknisi'] ?? '-'); ?></td>
                            <td><?= badge_status($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                            <td><a class="btn btn-sm" href="<?= asset('admin/pengaduan_detail.php?id=' . $row['id_pengaduan']); ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$data): ?>
                        <tr><td colspan="8">Belum ada pengaduan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
