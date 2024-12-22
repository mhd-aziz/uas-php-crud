<?php
// admin/delete_book.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../includes/config.php';
require '../includes/auth_admin.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['csrf_token'])) {
        $id = $_POST['id'];
        $csrf_token = $_POST['csrf_token'];
        if (hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            if (is_numeric($id)) {
                $stmt = $pdo->prepare("SELECT cover_image FROM books WHERE id = ?");
                $stmt->execute([$id]);
                $book = $stmt->fetch();

                if ($book) {
                    $upload_dir = realpath(dirname(__FILE__) . '/../uploads/cover_images/');

                    if ($upload_dir) {
                        if ($book['cover_image']) {
                            $cover_path = $upload_dir . DIRECTORY_SEPARATOR . $book['cover_image'];
                            if (file_exists($cover_path)) {
                                if (!unlink($cover_path)) {
                                    $_SESSION['delete_error'] = "Gagal menghapus file cover gambar.";
                                    header('Location: admin_dashboard.php');
                                    exit;
                                }
                            }
                        }
                        $delete_stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
                        if ($delete_stmt->execute([$id])) {
                            $_SESSION['delete_success'] = "Buku berhasil dihapus.";
                            header('Location: admin_dashboard.php');
                            exit;
                        } else {
                            $_SESSION['delete_error'] = "Gagal menghapus buku dari database.";
                            header('Location: admin_dashboard.php');
                            exit;
                        }
                    } else {
                        $_SESSION['delete_error'] = "Direktori upload tidak ditemukan.";
                        header('Location: admin_dashboard.php');
                        exit;
                    }
                } else {
                    $_SESSION['delete_error'] = "Buku tidak ditemukan.";
                    header('Location: admin_dashboard.php');
                    exit;
                }
            } else {
                $_SESSION['delete_error'] = "ID buku tidak valid.";
                header('Location: admin_dashboard.php');
                exit;
            }
        } else {
            $_SESSION['delete_error'] = "Token keamanan tidak valid.";
            header('Location: admin_dashboard.php');
            exit;
        }
    } else {
        $_SESSION['delete_error'] = "Data yang dikirim tidak lengkap.";
        header('Location: admin_dashboard.php');
        exit;
    }
} else {
    header('Location: admin_dashboard.php');
    exit;
}
