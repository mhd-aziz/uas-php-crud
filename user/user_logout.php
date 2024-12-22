<?php
// user/user_logout.php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['message'] = "Anda telah berhasil logout.";
header('Location: ../onboarding.php');
exit;
