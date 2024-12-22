<?php
// includes/contact.php
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . $base_url . '/user/user_login.php');
    exit;
}
?>
<section id="contact" class="py-5">
    <div class="container">
        <h2 class="mb-4">Contact Us</h2>
        <p>Jika Anda memiliki pertanyaan atau masukan, silakan hubungi kami melalui form di bawah ini.</p>
        <form method="POST" action="process_contact.php" class="contact-form">
            <div class="mb-3">
                <label for="contactName" class="form-label">Nama</label>
                <input type="text" class="form-control" id="contactName" name="name" placeholder="Masukkan nama Anda" required>
            </div>
            <div class="mb-3">
                <label for="contactEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="contactEmail" name="email" placeholder="Masukkan email Anda" required>
            </div>
            <div class="mb-3">
                <label for="contactMessage" class="form-label">Pesan</label>
                <textarea class="form-control" id="contactMessage" name="message" rows="5" placeholder="Tulis pesan Anda di sini" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
</section>