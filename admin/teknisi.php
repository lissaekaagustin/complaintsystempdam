<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'teknisi' ORDER BY id_user DESC");
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card">
        <div class="actions" style="justify-content: space-between;">
            <h2>Data Teknisi</h2>
            <a class="btn" href="<?= asset('admin/teknisi_form.php'); ?>">Tambah Teknisi</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Teknisi</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= e($row['nama']); ?></td>
                            <td><?= e($row['email']); ?></td>
                            <td><?= e($row['username']); ?></td>
                            <td><?= e($row['no_hp']); ?></td>
                            <td><?= e($row['alamat']); ?></td>
                            <td><?= e($row['status']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-warning" href="<?= asset('admin/teknisi_form.php?id=' . $row['id_user']); ?>">Edit</a>
                                <a class="btn btn-sm btn-danger" data-confirm="Yakin nonaktifkan/hapus teknisi ini?" href="<?= asset('admin/teknisi_hapus.php?id=' . $row['id_user']); ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$data): ?>
                        <tr><td colspan="8">Belum ada teknisi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
