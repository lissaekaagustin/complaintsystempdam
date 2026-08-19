<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

if (!is_logged_in()) {
    redirect('login.php');
}

redirect(role_redirect_path(current_role()));
?>
