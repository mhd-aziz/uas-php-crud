<?php
// index.php
require_once 'includes/header.php';
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin/admin_dashboard.php');
    exit;
} elseif (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: user/user_dashboard.php');
    exit;
} else {
    header('Location: onboarding.php');
    exit;
}
