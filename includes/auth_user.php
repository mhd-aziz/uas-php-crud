<?php
// includes/auth_user.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: /user/user_login.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header('Location: /user/user_login.php');
    exit;
}
