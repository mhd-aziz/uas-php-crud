<?php
// user/user_login.php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
session_start();
require '../includes/config.php';
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: user_dashboard.php');
    exit;
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../admin/admin_dashboard.php');
    exit;
}

$error = '';
$success = '';
if (isset($_SESSION['registration_success'])) {
    $success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Permintaan tidak valid. Silakan coba lagi.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            $error = "Silakan masukkan username dan password.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                unset($_SESSION['csrf_token']);
                header('Location: user_dashboard.php');
                exit;
            } else {
                $error = "Username atau password salah.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login User - Direktori Buku</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>

<body>
    <div class="card-container">
        <div class="logo text-center mb-4">
            <img src="../assets/images/logo.png" alt="Logo" style="max-width: 150px;">
        </div>
        <h3 class="text-center mb-3">Login User</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form method="POST" action="" class="w-100" onsubmit="handleLogin(event)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text" id="username-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" aria-label="Username" aria-describedby="username-icon" required>
                </div>
            </div>
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text" id="password-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" aria-label="Password" aria-describedby="password-icon" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <button type="submit" class="btn btn-custom btn-sm w-100 py-2 d-flex align-items-center justify-content-center" id="loginButton">
                    <span id="buttonText">Login</span>
                    <span id="spinner" class="spinner-border text-light ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
            <div class="register-link text-center">
                <p>Belum punya akun? <a href="user_register.php" class="text-decoration-none">Daftar</a></p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleLogin(event) {
            event.preventDefault();

            const loginButton = document.getElementById("loginButton");
            const buttonText = document.getElementById("buttonText");
            const spinner = document.getElementById("spinner");
            buttonText.classList.add("d-none");
            spinner.classList.remove("d-none");
            loginButton.disabled = false;
            loginButton.classList.add("btn-loading");
            setTimeout(() => {
                event.target.submit();
            }, 5000);
        }
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            if (type === 'password') {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>

</html>