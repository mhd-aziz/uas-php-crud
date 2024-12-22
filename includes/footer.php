<!-- includes/footer.php -->
</main>
<footer class="footer bg-primary text-white py-4 mt-5">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <span>&copy; <?php echo date("Y"); ?> Direktori Buku. All rights reserved.</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?= $base_url ?>/about.php" class="text-white me-3">About Us</a>
                <a href="<?= $base_url ?>/contact.php" class="text-white me-3">Contact</a>
                <a href="<?= $base_url ?>/privacy.php" class="text-white">Privacy Policy</a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col text-center">
                <a href="#" class="text-white me-3" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white me-3" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-white me-3" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            document.body.classList.add('scrolled');
        } else {
            document.body.classList.remove('scrolled');
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
</body>

</html>