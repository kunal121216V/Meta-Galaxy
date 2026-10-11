<?php
?>
        </main>

        <footer class="site-footer">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="footer-brand">
                            <span class="brand-mark">M</span>
                            <div>
                                <h4>META GALAXY</h4>
                                <p>FASHION BEYOND DIMENSIONS.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <h6>Shop</h6>
                        <ul>
                            <li><a href="<?= asset('shop.php?gender=Men') ?>">Men</a></li>
                            <li><a href="<?= asset('shop.php?gender=Women') ?>">Women</a></li>
                            <li><a href="<?= asset('shop.php?gender=Kids') ?>">Kids</a></li>
                            <li><a href="<?= asset('shop.php?gender=Shoes') ?>">Shoes</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <h6>Company</h6>
                        <ul>
                            <li><a href="<?= asset('about.php') ?>">About</a></li>
                            <li><a href="<?= asset('contact.php') ?>">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <h6>Customer Service</h6>
                        <ul>
                            <li><a href="<?= asset('shop.php') ?>">FAQ</a></li>
                            <li><a href="<?= asset('shop.php') ?>">Shipping</a></li>
                            <li><a href="<?= asset('shop.php') ?>">Returns</a></li>
                            <li><a href="<?= asset('contact.php') ?>">Support</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="newsletter-box">
                        <h6>Join the meta updates</h6>
                        <form class="newsletter-form" method="post" action="<?= asset('contact.php') ?>">
                            <input type="email" name="email" placeholder="Your email" aria-label="Email">
                            <button type="submit">Subscribe</button>
                        </form>
                    </div>
                    <div class="socials">
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="copyright-row">
                    <span>© <?= date('Y') ?> Meta Galaxy. All rights reserved.</span>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.162.0/build/three.min.js"></script>
    <script src="<?= asset('assets/js/main.js') ?>"></script>
    <?php if (isset($pageHero) && $pageHero): ?>
        <script src="<?= asset('assets/js/three-scene.js') ?>"></script>
    <?php endif; ?>
</body>
</html>
