
DROP TABLE IF EXISTS tindak_lanjut;
DROP TABLE IF EXISTS pengaduan;
DROP TABLE IF EXISTS kategori_pengaduan;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('pelanggan','admin','teknisi') NOT NULL,
    no_hp VARCHAR(20),
    alamat TEXT,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    no_sambungan VARCHAR(50) NOT NULL,
    nama_pelanggan VARCHAR(100) NOT NULL,
    alamat_pelanggan TEXT NOT NULL,
    no_hp VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pelanggan_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE kategori_pengaduan (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pengaduan (
    id_pengaduan INT AUTO_INCREMENT PRIMARY KEY,
    kode_pengaduan VARCHAR(30) NOT NULL UNIQUE,
    id_pelanggan INT NOT NULL,
    id_kategori INT NOT NULL,
    id_teknisi INT NULL,
    judul_pengaduan VARCHAR(150) NOT NULL,
    isi_pengaduan TEXT NOT NULL,
    alamat_lokasi TEXT NOT NULL,
    foto_pengaduan VARCHAR(255) NULL,
    status_pengaduan ENUM('Diajukan','Diverifikasi','Diproses','Selesai','Ditolak') DEFAULT 'Diajukan',
    alasan_penolakan TEXT NULL,
    tanggal_pengaduan DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pengaduan_pelanggan
        FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_pengaduan_kategori
        FOREIGN KEY (id_kategori) REFERENCES kategori_pengaduan(id_kategori)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_pengaduan_teknisi
        FOREIGN KEY (id_teknisi) REFERENCES users(id_user)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tindak_lanjut (
    id_tindak_lanjut INT AUTO_INCREMENT PRIMARY KEY,
    id_pengaduan INT NOT NULL,
    id_user INT NOT NULL,
    status_sebelum VARCHAR(50),
    status_sesudah VARCHAR(50),
    keterangan TEXT NOT NULL,
    foto_tindak_lanjut VARCHAR(255) NULL,
    tanggal_tindak_lanjut DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tindak_pengaduan
        FOREIGN KEY (id_pengaduan) REFERENCES pengaduan(id_pengaduan)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_tindak_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Password semua akun contoh adalah: password
-- Hash berikut adalah hash bcrypt untuk kata "password".
INSERT INTO users (nama, email, username, password, role, no_hp, alamat, status, created_at) VALUES
('Admin PDAM', 'admin@pdam.test', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081111111111', 'Kantor PDAM Tirta Gemilang', 'aktif', NOW()),
('Teknisi PDAM', 'teknisi@pdam.test', 'teknisi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teknisi', '082222222222', 'Pasaman Barat', 'aktif', NOW()),
('Pelanggan Contoh', 'pelanggan@pdam.test', 'pelanggan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pelanggan', '083333333333', 'Simpang Empat, Pasaman Barat', 'aktif', NOW());

INSERT INTO pelanggan (id_user, no_sambungan, nama_pelanggan, alamat_pelanggan, no_hp, created_at) VALUES
(3, 'PDAM-0001', 'Pelanggan Contoh', 'Simpang Empat, Pasaman Barat', '083333333333', NOW());

INSERT INTO kategori_pengaduan (nama_kategori, keterangan, created_at) VALUES
('Air tidak mengalir', 'Pengaduan ketika air pelanggan tidak mengalir.', NOW()),
('Air keruh atau berbau', 'Pengaduan terkait kualitas air yang keruh, berwarna, atau berbau.', NOW()),
('Tekanan air rendah', 'Pengaduan ketika tekanan air kecil atau tidak stabil.', NOW()),
('Kebocoran pipa', 'Pengaduan terkait kebocoran jaringan pipa.', NOW()),
('Gangguan meteran', 'Pengaduan terkait kerusakan atau kendala meteran air.', NOW()),
('Permasalahan tagihan', 'Pengaduan terkait administrasi atau tagihan air.', NOW()),
('Lainnya', 'Kategori pengaduan lainnya.', NOW());
