<?php
// includes/config.php

$host = 'localhost';
$db   = 'direktori_buku_db';
$user = 'root';
$pass = '';                 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// Menentukan Base URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$base_url .= "://" . $_SERVER['HTTP_HOST'];
$base_url .= "/DIREKTORI_BUKU";
?>
