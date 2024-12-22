<!-- includes/header.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$isUser = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$current_page = basename($_SERVER['PHP_SELF']);
echo "<!-- Base URL: " . htmlspecialchars($base_url) . " -->";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Direktori Buku<?php echo $isAdmin ? ' - Admin Dashboard' : ($isUser ? ' - User Dashboard' : ''); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/header.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/footer.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/dashboard.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars($base_url); ?>/index.php">
                    <img src="<?= htmlspecialchars($base_url); ?>/assets/images/logo_dashboard.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
                    Direktori Buku
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end text-bg-primary offcanvas-custom-width" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Navigasi</h5>
                        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                            <?php if ($isAdmin): ?>

                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'admin_dashboard.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/admin/admin_dashboard.php">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= htmlspecialchars($base_url); ?>/admin/admin_logout.php">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            <?php elseif ($isUser): ?>

                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'user_dashboard.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/user/user_dashboard.php">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= htmlspecialchars($base_url); ?>/user/user_logout.php">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            <?php else: ?>

                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'index.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/index.php">
                                        <i class="bi bi-house-fill"></i> Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'user_login.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/user/user_login.php">
                                        <i class="bi bi-box-arrow-in-right"></i> Login
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'user_register.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/user/user_register.php">
                                        <i class="bi bi-pencil-square"></i> Register
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'admin_login.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/admin/admin_login.php">
                                        <i class="bi bi-box-arrow-in-right"></i> Login
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>