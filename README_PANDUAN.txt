PANDUAN MENJALANKAN PROJECT
WEB-BASED CUSTOMER COMPLAINT SYSTEM PDAM
============================================================

A. APLIKASI YANG DIBUTUHKAN
1. XAMPP
   Digunakan untuk menjalankan Apache dan MySQL.
2. Sublime Text
   Digunakan untuk membuka dan mengedit coding.
3. Browser
   Contoh: Google Chrome atau Microsoft Edge.

B. CARA MEMASANG PROJECT DI XAMPP
1. Ekstrak file ZIP project ini.
2. Copy folder "pdam_complaint_system".
3. Paste ke folder:
   C:\xampp\htdocs\
4. Jadi lokasi foldernya:
   C:\xampp\htdocs\pdam_complaint_system

C. CARA MEMBUKA PROJECT DI SUBLIME TEXT
1. Buka Sublime Text.
2. Klik menu File.
3. Pilih Open Folder.
4. Pilih folder:
   C:\xampp\htdocs\pdam_complaint_system
5. Setelah itu semua file coding akan terlihat di sidebar Sublime Text.

D. CARA IMPORT DATABASE
1. Buka XAMPP Control Panel.
2. Klik Start pada Apache.
3. Klik Start pada MySQL.
4. Buka browser.
5. Ketik:
   http://localhost/phpmyadmin
6. Klik menu Import.
7. Pilih file:
   pdam_complaint_system/database/pdam_complaint_system.sql
8. Klik Go / Kirim.
9. Jika berhasil, database bernama pdam_complaint_system akan muncul.

E. CARA MENJALANKAN SISTEM
1. Pastikan Apache dan MySQL di XAMPP sudah aktif.
2. Buka browser.
3. Ketik:
   http://localhost/pdam_complaint_system
4. Sistem akan tampil.

F. AKUN LOGIN CONTOH
Semua password akun contoh adalah: password

1. Admin/Petugas
   Username : admin
   Password : password

2. Teknisi
   Username : teknisi
   Password : password

3. Pelanggan
   Username : pelanggan
   Password : password

G. ALUR PENGGUNAAN SISTEM
1. Pelanggan login.
2. Pelanggan membuat pengaduan.
3. Sistem membuat kode tiket pengaduan.
4. Admin/petugas login.
5. Admin/petugas melihat pengaduan masuk.
6. Admin/petugas menugaskan teknisi atau menolak pengaduan.
7. Teknisi login.
8. Teknisi membuka tugas pengaduan.
9. Teknisi mengisi tindak lanjut dan upload bukti penanganan.
10. Pelanggan dapat melihat status dan riwayat tindak lanjut.

H. BAGIAN FILE PENTING
1. config/database.php
   Untuk mengatur koneksi database.
2. config/app.php
   Untuk mengatur nama aplikasi dan BASE_URL.
3. database/pdam_complaint_system.sql
   File database yang harus di-import ke phpMyAdmin.
4. login.php
   Halaman login.
5. register.php
   Halaman registrasi pelanggan.
6. admin/
   Folder halaman admin/petugas.
7. pelanggan/
   Folder halaman pelanggan.
8. teknisi/
   Folder halaman teknisi.
9. uploads/
   Folder penyimpanan foto pengaduan dan bukti penanganan.

I. JIKA FOLDER PROJECT DIGANTI NAMANYA
Jika folder pdam_complaint_system diganti, misalnya menjadi pdam,
maka buka file:
config/app.php

Ubah:
define('BASE_URL', '/pdam_complaint_system');

Menjadi:
define('BASE_URL', '/pdam');

J. CATATAN UNTUK SKRIPSI BAB IV
Project ini bisa dijelaskan sebagai hasil implementasi dari rancangan sistem pada BAB III.
Fitur yang sudah tersedia:
1. Login multi-user.
2. Registrasi pelanggan.
3. Pengaduan pelanggan.
4. Upload foto bukti pengaduan.
5. Verifikasi pengaduan oleh admin/petugas.
6. Penugasan teknisi.
7. Tindak lanjut teknisi.
8. Upload bukti penanganan.
9. Monitoring status pengaduan.
10. Data kategori pengaduan.
11. Data pelanggan.
12. Data teknisi.
13. Laporan pengaduan.
14. Feedback pelanggan.
