<!-- includes/footer.php -->
</main>
<footer class="footer bg-primary text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span>&copy; <?php echo date("Y"); ?> Direktori Buku. All rights reserved.</span>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-end">
                <a href="#about-us" class="text-white me-3">About Us</a>
                <a href="#contact" class="text-white">Contact</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const notificationButton = document.getElementById('notificationButton');
        const notificationBox = document.getElementById('notificationBox');
        notificationButton.addEventListener('click', function(e) {
            e.preventDefault();
            notificationBox.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!notificationButton.contains(e.target) && !notificationBox.contains(e.target)) {
                notificationBox.classList.remove('show');
            }
        });
        window.addEventListener('resize', function() {
            notificationBox.classList.remove('show');
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const faders = document.querySelectorAll('.section-fade-in');

        const appearOptions = {
            threshold: 0.2,
            rootMargin: "0px 0px -50px 0px"
        };

        const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    return;
                } else {
                    entry.target.classList.add('visible');
                    appearOnScroll.unobserve(entry.target);
                }
            });
        }, appearOptions);

        faders.forEach(fader => {
            appearOnScroll.observe(fader);
        });
    });
</script>
</body>

</html>