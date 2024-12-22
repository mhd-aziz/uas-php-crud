<?php
// user/user_register.php
session_start();
require '../includes/config.php';

$error = '';
$success = '';

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
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $error = "Username sudah digunakan. Silakan pilih yang lain.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if ($stmt->execute([$username, $hashedPassword])) {
                    $user_id = $pdo->lastInsertId();
                    $_SESSION['registration_success'] = "Registrasi berhasil. Silakan masukkan username dan password Anda.";
                    $_SESSION['new_user_id'] = $user_id;
                    header('Location: user_login.php');
                    exit;
                } else {
                    $error = "Gagal mendaftar. Silakan coba lagi.";
                }
            }
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi User - Direktori Buku</title>
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
        <h3 class="text-center mb-3">Registrasi User</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <?php if (!empty($new_user_id)): ?>
                    <br>ID Pengguna: <?= htmlspecialchars($new_user_id) ?>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form method="POST" action="" class="w-100" onsubmit="handleRegister(event)">
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
                <button type="submit" class="btn btn-custom btn-sm w-100 py-2 d-flex align-items-center justify-content-center" id="registerButton">
                    <span id="buttonText">Register</span>
                    <span id="spinner" class="spinner-border text-light ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
            <div class="register-link text-center">
                <p>Sudah punya akun? <a href="user_login.php" class="text-decoration-none">Login</a></p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleRegister(event) {
            event.preventDefault();

            const registerButton = document.getElementById("registerButton");
            const buttonText = document.getElementById("buttonText");
            const spinner = document.getElementById("spinner");
            buttonText.classList.add("d-none");
            spinner.classList.remove("d-none");
            registerButton.disabled = false;
            registerButton.classList.add("btn-loading");
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