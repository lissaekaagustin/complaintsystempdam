<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($tgl_awal) {
    $where[] = "DATE(p.tanggal_pengaduan) >= ?";
    $params[] = $tgl_awal;
}

if ($tgl_akhir) {
    $where[] = "DATE(p.tanggal_pengaduan) <= ?";
    $params[] = $tgl_akhir;
}

if ($status) {
    $where[] = "p.status_pengaduan = ?";
    $params[] = $status;
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("
    SELECT p.*, pl.nama_pelanggan, pl.id_pelanggan, k.nama_kategori, t.nama AS nama_teknisi
    FROM pengaduan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN kategori_pengaduan k ON p.id_kategori = k.id_kategori
    LEFT JOIN users t ON p.id_teknisi = t.id_user
    {$whereSql}
    ORDER BY p.tanggal_pengaduan DESC
");
$stmt->execute($params);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div style="width:100vw !important; max-width:100vw !important; margin-left:calc(50% - 50vw) !important; margin-right:calc(50% - 50vw) !important; padding:28px 32px !important;">
    <div class="card" style="width:100% !important; max-width:none !important;">
        <h2>Laporan Pengaduan</h2>

        <form method="get" class="filter-box no-print">
            <div class="form-group">
                <label>Tanggal Awal</label>
                <input type="date" name="tgl_awal" value="<?= e($tgl_awal); ?>">
            </div>

            <div class="form-group">
                <label>Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" value="<?= e($tgl_akhir); ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <?php foreach (['Diajukan','Diproses','Selesai','Ditolak'] as $s): ?>
                        <option value="<?= e($s); ?>" <?= $status === $s ? 'selected' : ''; ?>><?= e($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn" type="submit">Tampilkan</button>
            <button class="btn btn-success" type="button" onclick="window.print()">Cetak</button>
        </form>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= e($row['kode_pengaduan']); ?></td>
                            <td><?= e($row['id_pelanggan']); ?><br><small>
                            <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= e($row['nama_teknisi'] ?? '-'); ?></td>
                            <td><?= e($row['status_pengaduan']); ?></td>
                            <td><?= e($row['tanggal_pengaduan']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$data): ?>
                        <tr><td colspan="7">Tidak ada data laporan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .footer, .no-print {
        display: none !important;
    }
    body {
        background: #fff;
    }
    .card {
        box-shadow: none;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
