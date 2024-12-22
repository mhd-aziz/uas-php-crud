<?php
// includes/about_us.php
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . $base_url . '/user/user_login.php');
    exit;
}
?>
<section id="about-us" class="py-5 bg-light section-fade-in">
    <div class="container">
        <div class="mb-5 text-center">
            <h2 class="mb-3">About Us</h2>
            <p class="lead">Direktori Buku adalah platform yang bertujuan untuk menyediakan informasi lengkap mengenai berbagai buku. Kami berkomitmen untuk membantu Anda menemukan buku favorit dengan mudah dan cepat.</p>
        </div>
        <div class="mb-5">
            <h3 class="mb-4 text-center">Pengembang</h3>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <img src="<?= htmlspecialchars($base_url); ?>/assets/images/profile.png" alt="Adam Anwar Sofian" class="rounded-circle mb-3" width="100" height="100">
                            <h5 class="card-title">Adam Anwar Sofian</h5>
                            <p class="card-text">Hai, saya Adam Anwar Sofian, pengembang di balik Direktori Buku. Saya berharap platform ini dapat memberikan pengalaman terbaik bagi Anda dalam menemukan buku favorit. Terima kasih telah menggunakan layanan kami!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-5">
            <h3 class="mb-4 text-center">Fitur Unggulan Kami</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-book-half fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">Katalog Lengkap</h5>
                            <p class="card-text">Kami menyediakan katalog buku yang lengkap dari berbagai genre dan penulis ternama.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-star-fill fs-1 text-warning mb-3"></i>
                            <h5 class="card-title">Bookmark Favorit</h5>
                            <p class="card-text">Simpan buku favorit Anda dan akses kapan saja melalui fitur bookmark kami.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-square-text fs-1 text-success mb-3"></i>
                            <h5 class="card-title">Umpan Balik Pengguna</h5>
                            <p class="card-text">Berikan ulasan dan rating untuk buku yang telah Anda baca, membantu pengguna lain dalam memilih.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>