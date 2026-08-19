<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$stmt = $pdo->query("
    SELECT p.*, u.email, u.username, u.status
    FROM pelanggan p
    JOIN users u ON p.id_user = u.id_user
    ORDER BY p.id_pelanggan DESC
");
$data = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="card">
        <h2>Data Pelanggan</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Status Akun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= e($row['id_pelanggan']); ?></td>
                            <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['email']); ?></td>
                            <td><?= e($row['no_hp']); ?></td>
                            <td><?= e($row['alamat_pelanggan']); ?></td>
                            <td><?= e($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$data): ?>
                        <tr><td colspan="7">Belum ada pelanggan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
