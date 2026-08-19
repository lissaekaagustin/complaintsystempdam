<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$stmt = $pdo->query("SELECT * FROM kategori_pengaduan ORDER BY id_kategori DESC");
$kategori = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card">
        <div class="actions" style="justify-content: space-between;">
            <h2>Data Kategori Pengaduan</h2>
            <a class="btn" href="<?= asset('admin/kategori_form.php'); ?>">Tambah Kategori</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= e($row['nama_kategori']); ?></td>
                            <td><?= e($row['keterangan']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-warning" href="<?= asset('admin/kategori_form.php?id=' . $row['id_kategori']); ?>">Edit</a>
                                <a class="btn btn-sm btn-danger" data-confirm="Yakin hapus kategori ini?" href="<?= asset('admin/kategori_hapus.php?id=' . $row['id_kategori']); ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$kategori): ?>
                        <tr><td colspan="4">Belum ada data kategori.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
