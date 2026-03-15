<footer class="site-footer">
    <div class="site-shell py-4">
        <div class="row g-4 footer-grid">
            <section class="col-12 col-sm-6 col-lg-3">
                <h4>Shop</h4>
                <a href="#">New Arrivals</a>
                <a href="#">Best Sellers</a>
                <a href="#">Collections</a>
            </section>

            <section class="col-12 col-sm-6 col-lg-3">
                <h4>Company</h4>
                <a href="<?= site_url('/#about') ?>">About Us</a>
                <a href="#">Contact</a>
                <a href="#">Careers</a>
            </section>

            <section class="col-12 col-sm-6 col-lg-3">
                <h4>Support</h4>
                <a href="#">Help Center</a>
                <a href="#">Licensing</a>
                <a href="#">Terms</a>
            </section>

            <section class="col-12 col-sm-6 col-lg-3">
                <h4>Stay Updated</h4>
                <p class="footer-note">Get new 3D asset drops and promo updates.</p>
                <form class="footer-subscribe" action="#" method="post">
                    <input type="email" name="email" placeholder="Email address" aria-label="Email address">
                    <button type="submit">Subscribe</button>
                </form>
            </section>
        </div>

        <div class="footer-bottom mt-3">
            <p class="mb-0">Copyright © <?= date('Y') ?> Byte-Sized Bakes. All rights reserved.</p>
        </div>
    </div>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
</body>
</html>