<?php
require_once __DIR__ . '/config/helpers.php';

session_destroy();
session_start();
flash('success', 'Anda berhasil logout.');
redirect('login.php');
?>
