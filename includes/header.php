<!-- includes/header.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$isUser = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$username = '';
if ($isAdmin) {
    $username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';
} elseif ($isUser) {
    $username = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : 'User';
}

$current_page = basename($_SERVER['PHP_SELF']);
echo "<!-- Base URL: " . htmlspecialchars($base_url) . " -->";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Direktori Buku<?php echo $isAdmin ? ' - Admin Dashboard' : ($isUser ? ' - User Dashboard' : ''); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/header.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/footer.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($base_url); ?>/assets/css/dashboard.css" rel="stylesheet">
</head>

<body>
    <header class="navbar-hero">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#dashboard">
                    <img src="<?= htmlspecialchars($base_url); ?>/assets/images/logo_dashboard.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top me-2">
                    <span class="fw-bold">Direktori Buku</span>
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
                        <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                            <?php if ($isUser): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#dashboard">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#about-us">
                                        <i class="bi bi-info-circle me-2"></i> About Us
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#contact">
                                        <i class="bi bi-telephone me-2"></i> Contact
                                    </a>
                                </li>
                            <?php elseif ($isAdmin): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#dashboard">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'index.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/index.php">
                                        <i class="bi bi-house-fill me-2"></i> Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'user_login.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/user/user_login.php">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'user_register.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/user/user_register.php">
                                        <i class="bi bi-pencil-square me-2"></i> Register
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo ($current_page == 'admin_login.php') ? ' active' : ''; ?>" href="<?= htmlspecialchars($base_url); ?>/admin/admin_login.php">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Admin Login
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php if ($isAdmin || $isUser): ?>
                    <div class="d-flex align-items-center position-relative">
                        <div class="nav-item me-3 position-relative">
                            <a class="nav-link position-relative" href="#" id="notificationButton">
                                <i class="bi bi-bell"></i>
                                <span class="badge">3</span>
                            </a>
                            <div class="notification-box" id="notificationBox">
                                <p>Tidak ada notifikasi</p>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?= htmlspecialchars($base_url); ?>/assets/images/profile.png" alt="Profil" width="30" height="30" class="rounded-circle me-2">
                                <?= htmlspecialchars($username); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?= htmlspecialchars($base_url); ?>/<?= $isAdmin ? 'admin/admin_logout.php' : 'user/user_logout.php'; ?>">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
    </main>
</body>

</html>