<?php
// admin/edit_book.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/config.php';
require '../includes/auth_admin.php';
$allowed_genres = [
    'Buku Cerita',
    'Buku Horror',
    'Buku Sekolah',
    'Buku Fiksi',
    'Buku Non-Fiksi',
    'Buku Referensi',
    'Buku Biografi',
    'Buku Teknologi',
    'Buku Agama',
    'Buku Komik'
];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: admin_dashboard.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid CSRF token.";
    }
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $page_count = trim($_POST['page_count']);
    $publish_year = trim($_POST['publish_year']);
    if (empty($title)) {
        $errors[] = "Judul buku wajib diisi.";
    }
    if (empty($author)) {
        $errors[] = "Pengarang wajib diisi.";
    }
    if (empty($genre)) {
        $errors[] = "Jenis buku wajib dipilih.";
    } elseif (!in_array($genre, $allowed_genres)) {
        $errors[] = "Jenis buku yang dipilih tidak valid.";
    }
    if (!empty($page_count)) {
        if (!is_numeric($page_count) || (int)$page_count <= 0) {
            $errors[] = "Jumlah halaman harus berupa angka positif.";
        } else {
            $page_count = (int)$page_count;
        }
    } else {
        $page_count = '-';
    }
    if (!empty($publish_year)) {
        if (!is_numeric($publish_year) || (int)$publish_year < 1000 || (int)$publish_year > (int)date("Y")) {
            $errors[] = "Tahun terbit harus berupa angka antara 1000 dan " . date("Y") . ".";
        } else {
            $publish_year = (int)$publish_year;
        }
    } else {
        $publish_year = '-';
    }
    $cover_image = $book['cover_image'];
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] != UPLOAD_ERR_NO_FILE) {
        if ($_FILES['cover_image']['error'] != UPLOAD_ERR_OK) {
            $errors[] = "Terjadi kesalahan saat mengupload cover image.";
        } else {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES['cover_image']['name'];
            $file_size = $_FILES['cover_image']['size'];
            $file_tmp = $_FILES['cover_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_ext)) {
                $errors[] = "Jenis file cover harus JPG, JPEG, PNG, atau GIF.";
            }

            if ($file_size > 2 * 1024 * 1024) {
                $errors[] = "Ukuran file cover tidak boleh lebih dari 2MB.";
            }

            $mime_type = mime_content_type($file_tmp);
            $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mime_type, $allowed_mime)) {
                $errors[] = "File yang diupload bukan gambar yang valid.";
            }

            if (empty($errors)) {
                $upload_dir = realpath(dirname(__FILE__) . '/../uploads/cover_images/');
                if (!$upload_dir) {
                    $errors[] = "Direktori upload tidak ditemukan.";
                } else {
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $errors[] = "Gagal membuat direktori upload.";
                        }
                    }

                    if (empty($errors)) {
                        $new_file_name = uniqid() . '.' . $file_ext;
                        $destination = $upload_dir . DIRECTORY_SEPARATOR . $new_file_name;
                        if (move_uploaded_file($file_tmp, $destination)) {
                            if ($book['cover_image'] && file_exists($upload_dir . DIRECTORY_SEPARATOR . $book['cover_image'])) {
                                unlink($upload_dir . DIRECTORY_SEPARATOR . $book['cover_image']);
                            }
                            $cover_image = $new_file_name;
                        } else {
                            $errors[] = "Gagal mengupload cover image.";
                        }
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, genre = ?, page_count = ?, publish_year = ?, cover_image = ? WHERE id = ?");
        $stmt->execute([$title, $author, $genre, $page_count, $publish_year, $cover_image, $id]);
        $success = "Buku berhasil diperbarui.";
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();
    }
}

$csrf_token = $_SESSION['csrf_token'];
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h2>Edit Buku</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Judul Buku <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Pengarang <span class="text-danger">*</span></label>
            <input type="text" name="author" id="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Jenis Buku <span class="text-danger">*</span></label>
            <select name="genre" id="genre" class="form-select" required>
                <option value="">-- Pilih Jenis Buku --</option>
                <?php foreach ($allowed_genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= (isset($genre) && $genre == $g) ? 'selected' : (htmlspecialchars($book['genre']) == $g ? 'selected' : ''); ?>><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="page_count" class="form-label">Jumlah Halaman</label>
            <input type="number" name="page_count" id="page_count" class="form-control" value="<?= ($book['page_count'] !== '-') ? htmlspecialchars($book['page_count']) : '' ?>" min="1">
            <div class="form-text">Opsional</div>
        </div>
        <div class="mb-3">
            <label for="publish_year" class="form-label">Tahun Terbit</label>
            <input type="number" name="publish_year" id="publish_year" class="form-control" value="<?= ($book['publish_year'] !== '-') ? htmlspecialchars($book['publish_year']) : '' ?>" min="1000" max="<?= date("Y") ?>">
            <div class="form-text">Opsional</div>
        </div>
        <div class="mb-3">
            <label for="cover_image" class="form-label">Foto Cover</label>
            <input type="file" name="cover_image" id="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
            <div class="form-text">Jenis file yang diperbolehkan: JPG, JPEG, PNG, GIF. Maksimal ukuran: 2MB.</div>
            <?php if ($book['cover_image'] && file_exists('../uploads/cover_images/' . $book['cover_image'])): ?>
                <img src="<?= $base_url ?>/uploads/cover_images/<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-primary btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Perbarui Buku">
                <i class="bi bi-pencil-square"></i> Update
            </button>
            <a href="admin_dashboard.php" class="btn btn-secondary btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
</body>

</html>