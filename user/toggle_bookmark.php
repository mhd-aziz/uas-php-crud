<?php
// user/toggle_bookmark.php
session_start();
require '../includes/config.php';
require '../includes/auth_user.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['bookmark_error'] = "Permintaan tidak valid.";
        header('Location: user_dashboard.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

    if ($book_id > 0) {
        $stmt = $pdo->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$user_id, $book_id]);
        $bookmark = $stmt->fetch();

        if ($bookmark) {
            $delete_stmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = ?");
            $delete_stmt->execute([$bookmark['id']]);
            $_SESSION['bookmark_message'] = "Bookmark berhasil dihapus.";
        } else {
            $insert_stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, book_id) VALUES (?, ?)");
            try {
                $insert_stmt->execute([$user_id, $book_id]);
                $_SESSION['bookmark_message'] = "Bookmark berhasil ditambahkan.";
            } catch (PDOException $e) {
                $_SESSION['bookmark_error'] = "Gagal menambahkan bookmark.";
            }
        }
    } else {
        $_SESSION['bookmark_error'] = "ID buku tidak valid.";
    }
} else {
    $_SESSION['bookmark_error'] = "Metode permintaan tidak diizinkan.";
}
header('Location: user_dashboard.php');
exit;
