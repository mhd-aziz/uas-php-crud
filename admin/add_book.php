<?php
// admin/add_book.php
session_start();
require '../includes/config.php';
require '../includes/auth_admin.php';

$errors = [];
$success = '';

// Definisikan daftar genre yang diperbolehkan
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        }
    }
    if (!empty($publish_year)) {
        if (!is_numeric($publish_year) || (int)$publish_year < 1000 || (int)$publish_year > (int)date("Y")) {
            $errors[] = "Tahun terbit harus berupa angka antara 1000 dan " . date("Y") . ".";
        }
    }
    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] != 0) {
        $errors[] = "Foto cover wajib diunggah.";
    }
    $cover_image = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
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
                        $cover_image = $new_file_name;
                    } else {
                        $errors[] = "Gagal mengupload cover image.";
                    }
                }
            }
        }
    }
    $page_count = empty($page_count) ? '-' : (int)$page_count;
    $publish_year = empty($publish_year) ? '-' : (int)$publish_year;

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, page_count, cover_image, publish_year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $genre, $page_count, $cover_image, $publish_year]);
        $success = "Buku berhasil ditambahkan.";
        $title = $author = $genre = $page_count = $publish_year = '';
    }
}

?>

<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h2>Tambah Buku</h2>
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
        <div class="mb-3">
            <label for="title" class="form-label">Judul Buku <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Pengarang <span class="text-danger">*</span></label>
            <input type="text" name="author" id="author" class="form-control" value="<?= isset($author) ? htmlspecialchars($author) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Jenis Buku <span class="text-danger">*</span></label>
            <select name="genre" id="genre" class="form-select" required>
                <option value="">-- Pilih Jenis Buku --</option>
                <?php foreach ($allowed_genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= (isset($genre) && $genre == $g) ? 'selected' : ''; ?>><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="page_count" class="form-label">Jumlah Halaman</label>
            <input type="number" name="page_count" id="page_count" class="form-control" value="<?= isset($page_count) && $page_count !== '-' ? htmlspecialchars($page_count) : '' ?>" min="1">
            <div class="form-text">Opsional</div>
        </div>
        <div class="mb-3">
            <label for="publish_year" class="form-label">Tahun Terbit</label>
            <input type="number" name="publish_year" id="publish_year" class="form-control" value="<?= isset($publish_year) && $publish_year !== '-' ? htmlspecialchars($publish_year) : '' ?>" min="1000" max="<?= date("Y") ?>">
            <div class="form-text">Opsional</div>
        </div>
        <div class="mb-3">
            <label for="cover_image" class="form-label">Foto Cover <span class="text-danger">*</span></label>
            <input type="file" name="cover_image" id="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.gif" required>
            <div class="form-text">Jenis file yang diperbolehkan: JPG, JPEG, PNG, GIF. Maksimal ukuran: 2MB.</div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-success btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Buku Baru">
                <i class="bi bi-plus-circle"></i> Tambah
            </button>
            <a href="admin_dashboard.php" class="btn btn-secondary btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>