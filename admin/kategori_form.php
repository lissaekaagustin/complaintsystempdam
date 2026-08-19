<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['admin']);

$id = $_GET['id'] ?? null;
$data = [
    'nama_kategori' => '',
    'keterangan' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM kategori_pengaduan WHERE id_kategori = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if (!$data) {
        flash('error', 'Data kategori tidak ditemukan.');
        redirect('admin/kategori.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (!$nama) {
        flash('error', 'Nama kategori wajib diisi.');
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE kategori_pengaduan SET nama_kategori = ?, keterangan = ? WHERE id_kategori = ?");
            $stmt->execute([$nama, $keterangan, $id]);
            flash('success', 'Kategori berhasil diperbarui.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO kategori_pengaduan (nama_kategori, keterangan, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$nama, $keterangan]);
            flash('success', 'Kategori berhasil ditambahkan.');
        }
        redirect('admin/kategori.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="card">
        <h2><?= $id ? 'Edit' : 'Tambah'; ?> Kategori Pengaduan</h2>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <form method="post">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" value="<?= e($data['nama_kategori']); ?>" required>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"><?= e($data['keterangan']); ?></textarea>
            </div>

            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="<?= asset('admin/kategori.php'); ?>">Kembali</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
