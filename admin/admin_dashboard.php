<?php
// admin/admin_dashboard.php
require '../includes/header.php';
require '../includes/auth_admin.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$sortable_columns = [
    'title' => 'Judul',
    'author' => 'Pengarang',
    'genre' => 'Jenis Buku',
    'page_count' => 'Jumlah Halaman',
    'publish_year' => 'Tahun Terbit'
];
$allowed_sort_directions = ['asc', 'desc'];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortable_columns) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) && in_array(strtolower($_GET['order']), $allowed_sort_directions) ? strtolower($_GET['order']) : 'asc';
$rows_per_page_options = [5, 10, 15];
$rows_per_page = isset($_GET['rows_per_page']) && in_array((int)$_GET['rows_per_page'], $rows_per_page_options) ? (int)$_GET['rows_per_page'] : 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $rows_per_page;
$search = '';
$params = [];
$where = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $where = "WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
    $search_term = "%" . $search . "%";
    $params = [$search_term, $search_term, $search_term];
}
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM books $where");
$count_stmt->execute($params);
$total_books = $count_stmt->fetchColumn();
$total_pages = ceil($total_books / $rows_per_page);
$order_by = "ORDER BY $sort $order";
$data_stmt = $pdo->prepare("SELECT * FROM books $where $order_by LIMIT ? OFFSET ?");
if ($where) {
    foreach ($params as $k => $v) {
        $data_stmt->bindValue($k + 1, $v, PDO::PARAM_STR);
    }
    $data_stmt->bindValue(count($params) + 1, $rows_per_page, PDO::PARAM_INT);
    $data_stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
} else {
    $data_stmt->bindValue(1, $rows_per_page, PDO::PARAM_INT);
    $data_stmt->bindValue(2, $offset, PDO::PARAM_INT);
}
$data_stmt->execute();
$books = $data_stmt->fetchAll();
?>

<div class="container mt-4 dashboard-container">
    <div class="dashboard-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <h2>Dashboard Admin</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="add_book.php" class="btn btn-success btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Buku Baru">
                <i class="bi bi-plus-circle"></i> Tambah Buku
            </a>
            <a href="<?= $base_url ?>/admin/generate_pdf.php<?= $search ? '?search=' . urlencode($search) : ''; ?>" class="btn btn-secondary btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak PDF">
                <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
            </a>
        </div>
    </div>
    <?php if (isset($_SESSION['delete_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['delete_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['delete_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['delete_error']); ?>
    <?php endif; ?>

    <form method="GET" class="mb-4 d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
        <div class="input-group me-3 mb-2 mb-sm-0">
            <input type="text" name="search" class="form-control" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
        <label for="rows_per_page" class="form-label me-2 mb-2 mb-sm-0">Tampilkan:</label>
        <select name="rows_per_page" id="rows_per_page" class="form-select w-auto" onchange="this.form.submit()">
            <?php foreach ($rows_per_page_options as $option): ?>
                <option value="<?= $option ?>" <?= ($rows_per_page == $option) ? 'selected' : ''; ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-custom table-hover">
            <thead class="table-primary">
                <tr>
                    <th class="text-center text-white">No.</th>
                    <th class="text-center text-white">Cover</th>
                    <?php foreach ($sortable_columns as $column_key => $column_label): ?>
                        <th class="text-center text-white">
                            <?php
                            $new_order = ($sort === $column_key && $order === 'asc') ? 'desc' : 'asc';
                            $sort_url_params = array_merge($_GET, ['sort' => $column_key, 'order' => $new_order, 'page' => 1]);
                            $sort_url = '?' . http_build_query($sort_url_params);
                            $sort_icon = '';
                            if ($sort === $column_key) {
                                $sort_icon = ($order === 'asc') ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>';
                            }
                            ?>
                            <a href="<?= htmlspecialchars($sort_url) ?>" class="text-white text-decoration-none">
                                <?= htmlspecialchars($column_label) ?> <?= $sort_icon ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th class="text-center text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books): ?>
                    <?php foreach ($books as $index => $book): ?>
                        <tr>
                            <td class="text-center align-middle"><?= ($offset + $index + 1); ?></td>
                            <td class="text-center align-middle">
                                <?php if ($book['cover_image'] && file_exists('../uploads/cover_images/' . $book['cover_image'])): ?>
                                    <img src="<?= $base_url ?>/uploads/cover_images/<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" class="img-thumbnail" style="max-width: 100px;">
                                <?php else: ?>
                                    <i class="bi bi-book-fill text-muted" style="font-size: 1.5rem;"></i>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle"><?= htmlspecialchars($book['title']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($book['author']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($book['genre']) ?></td>
                            <td class="text-center align-middle">
                                <?= ($book['page_count'] === '-' || $book['page_count'] === null) ? '-' : htmlspecialchars($book['page_count']) ?>
                            </td>
                            <td class="text-center align-middle">
                                <?= ($book['publish_year'] === '-' || $book['publish_year'] === null) ? '-' : htmlspecialchars($book['publish_year']) ?>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-responsive" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Buku">
                                        <i class="bi bi-pencil-square"></i> Update
                                    </a>
                                    <form action="delete_book.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="btn btn-danger btn-responsive" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Buku">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center align-middle">Tidak ada data ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php
                                                $query = $_GET;
                                                $query['page'] = $page - 1;
                                                echo http_build_query($query);
                                                ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php
                $max_links = 5;
                $start = max(1, $page - floor($max_links / 2));
                $end = min($total_pages, $start + $max_links - 1);
                if (($end - $start) < ($max_links - 1)) {
                    $start = max(1, $end - $max_links + 1);
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php
                                                    $query = $_GET;
                                                    $query['page'] = $i;
                                                    echo http_build_query($query);
                                                    ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php
                                                $query = $_GET;
                                                $query['page'] = $page + 1;
                                                echo http_build_query($query);
                                                ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>