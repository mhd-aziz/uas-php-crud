<?php
// onboarding.php
session_start();
require 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Onboarding - Direktori Buku</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="assets/css/auth.css" rel="stylesheet">
</head>

<body class="onboarding">
    <div class="card-container">
        <div class="logo text-center mb-4">
            <img src="assets/images/logo.png" alt="Logo" style="max-width: 150px;">
        </div>
        <h3 class="text-center mb-3">Selamat Datang di Direktori Buku</h3>
        <p class="text-center mb-4">Pilih peran Anda untuk melanjutkan</p>
        <div class="w-100">
            <a href="user/user_login.php" class="btn btn-custom w-100 py-2 mb-3">
                <i class="bi bi-person-fill me-2"></i> Sebagai User
            </a>
            <a href="admin/admin_login.php" class="btn btn-custom w-100 py-2">
                <i class="bi bi-gear-fill me-2"></i> Sebagai Admin
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>