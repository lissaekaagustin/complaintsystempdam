<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
auth_required(['pelanggan']);

$pelanggan = get_pelanggan_by_user($pdo, current_user_id());
$kategori = $pdo->query("SELECT * FROM kategori_pengaduan ORDER BY nama_kategori ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_kategori = $_POST['id_kategori'] ?? '';
        $isi = trim($_POST['isi_pengaduan'] ?? '');
        $lokasi = trim($_POST['alamat_lokasi'] ?? '');

        if (!$id_kategori || !$isi || !$lokasi) {
            throw new Exception('Semua data pengaduan wajib diisi.');
        }

        $foto = upload_image('foto_pengaduan');
        $kode = generate_kode_pengaduan($pdo);

        $stmt = $pdo->prepare("
            INSERT INTO pengaduan
            (kode_pengaduan, id_pelanggan, id_kategori, isi_pengaduan, alamat_lokasi, foto_pengaduan, status_pengaduan, tanggal_pengaduan, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Diajukan', NOW(), NOW())
        ");
        $stmt->execute([$kode, $pelanggan['id_pelanggan'], $id_kategori, $isi, $lokasi, $foto]);

        $id_pengaduan = $pdo->lastInsertId();

        insert_tindak_lanjut($pdo, $id_pengaduan, current_user_id(), '-', 'Diajukan', 'Pengaduan dikirim oleh pelanggan.');
        flash('success', 'Pengaduan berhasil dikirim');
        redirect('pelanggan/pengaduan_detail.php?id=' . $id_pengaduan);
    } catch (Exception $e) {
        flash('error', $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<style>
    .buat-pengaduan-page {
        width: 50% !important;
        max-width: 1350px !important;
        margin: 40px auto !important;
        padding: 0 !important;
    }
    
</style>
<div class="container buat-pengaduan-page">
    <div class="card">
        <h2>Buat Pengaduan</h2>
        <p>Isi form berikut sesuai keluhan yang dialami.</p>
        <?php require_once __DIR__ . '/../includes/alerts.php'; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Kategori Pengaduan</label>
                <select name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id_kategori']; ?>"><?= e($k['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
        <label>Isi Pengaduan</label>
        <textarea name="isi_pengaduan" placeholder="Jelaskan keluhan secara lengkap." required></textarea>
    </div>

            <div class="form-group">
                <label>Alamat/Lokasi Masalah</label>
                <textarea name="alamat_lokasi" required><?= e($pelanggan['alamat_pelanggan']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Foto Pendukung</label>
                <input type="file" name="foto_pengaduan" accept=".jpg,.jpeg,.png,.webp">
                <small>Format: jpg, jpeg, png, webp. Maksimal 20MB.</small>
            </div>

            <button class="btn" type="submit">Kirim Pengaduan</button>
            <a class="btn btn-secondary" href="<?= asset('pelanggan/dashboard.php'); ?>">Kembali</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
