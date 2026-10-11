<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$pdo = db();
$pageTitle = 'Meta Galaxy | Fashion Beyond Dimensions';
$pageHero = true;

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC LIMIT 4")->fetchAll();
$featuredProducts = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 8")->fetchAll();
$newArrivals = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 4")->fetchAll();
$bestSellers = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.stock_quantity ASC LIMIT 4")->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<section class="hero-section">
    <div class="hero-backdrop"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="kicker">Future collection</span>
            <h1>ENTER THE <span>FUTURE</span> OF FASHION</h1>
            <p>Discover fashion designed for every generation. Premium silhouettes, elevated essentials, and performance-driven pieces built for the next dimension.</p>
            <div class="hero-actions">
                <a href="<?= asset('shop.php?gender=Men') ?>" class="btn btn-primary">SHOP MEN</a>
                <a href="<?= asset('shop.php?gender=Women') ?>" class="btn btn-outline-light">SHOP WOMEN</a>
                <a href="<?= asset('shop.php?gender=Kids') ?>" class="btn btn-outline-light">SHOP KIDS</a>
            </div>
            <div class="hero-metrics">
                <div class="metric-box">
                    <strong>30k+</strong>
                    <span>Shoppers</span>
                </div>
                <div class="metric-box">
                    <strong>4.9/5</strong>
                    <span>Average Rating</span>
                </div>
                <div class="metric-box">
                    <strong>48h</strong>
                    <span>Express Delivery</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-stage">
                <canvas id="threeCanvas"></canvas>
            </div>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Featured categories</span>
                <h2>Shop By Style</h2>
            </div>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="<?= asset('category.php?slug=' . rawurlencode($category['slug'])) ?>" class="category-card">
                    <img src="<?= e(productImage(['main_image' => $category['image']])) ?>" alt="<?= e($category['name']) ?>">
                    <div class="overlay">
                        <strong><?= e($category['name']) ?></strong>
                        <span><?= e($category['gender']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Trending now</span>
                <h2>Trending Products</h2>
            </div>
            <a href="<?= asset('shop.php') ?>" class="btn btn-outline-light">View All</a>
        </div>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <article class="product-card">
                    <div class="product-image">
                        <span class="product-badge"><?= e(productLabel($product)) ?></span>
                        <div class="product-actions">
                            <button type="button" class="product-action" data-wishlist="<?= (int) $product['id'] ?>" aria-label="Wishlist"><i class="bi bi-heart"></i></button>
                        </div>
                        <img src="<?= e(productImage($product)) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-meta"><span><?= e($product['category_name'] ?? 'Collection') ?></span><span>★ 4.9</span></div>
                        <h3><?= e($product['name']) ?></h3>
                        <div class="product-pricing">
                            <span class="current"><?= formatPrice(productPrimaryPrice($product)) ?></span>
                            <?php if (!empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['price']): ?>
                                <span class="old"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-bottom">
                            <span class="product-stock <?= (int) $product['stock_quantity'] > 0 ? 'in' : 'low' ?>"><?= (int) $product['stock_quantity'] > 0 ? 'In Stock' : 'Low Stock' ?></span>
                            <a href="<?= asset('product.php?slug=' . rawurlencode($product['slug'])) ?>" class="btn btn-cart-small">View</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="promo-banner">
            <div class="promo-content">
                <div>
                    <span class="kicker">Limited drop</span>
                    <h3>Future Essentials</h3>
                </div>
                <a href="<?= asset('shop.php') ?>" class="btn btn-primary">Explore the drop</a>
            </div>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Fresh arrivals</span>
                <h2>New Arrivals</h2>
            </div>
        </div>
        <div class="product-grid">
            <?php foreach ($newArrivals as $product): ?>
                <article class="product-card">
                    <div class="product-image">
                        <span class="product-badge">New</span>
                        <div class="product-actions">
                            <button type="button" class="product-action" data-wishlist="<?= (int) $product['id'] ?>" aria-label="Wishlist"><i class="bi bi-heart"></i></button>
                        </div>
                        <img src="<?= e(productImage($product)) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-meta"><span><?= e($product['category_name'] ?? 'Collection') ?></span><span>★ 4.8</span></div>
                        <h3><?= e($product['name']) ?></h3>
                        <div class="product-pricing">
                            <span class="current"><?= formatPrice(productPrimaryPrice($product)) ?></span>
                            <?php if (!empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['price']): ?>
                                <span class="old"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-bottom">
                            <span class="product-stock <?= (int) $product['stock_quantity'] > 0 ? 'in' : 'low' ?>"><?= (int) $product['stock_quantity'] > 0 ? 'In Stock' : 'Low Stock' ?></span>
                            <button class="btn btn-cart-small" data-add-to-cart="<?= (int) $product['id'] ?>">Add</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Best sellers</span>
                <h2>Most Loved Pieces</h2>
            </div>
        </div>
        <div class="feature-grid">
            <?php foreach ($bestSellers as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-badge">Best</span>
                        <img src="<?= e(productImage($product)) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-meta"><span><?= e($product['category_name'] ?? 'Collection') ?></span><span>★ 5.0</span></div>
                        <h3><?= e($product['name']) ?></h3>
                        <div class="product-pricing">
                            <span class="current"><?= formatPrice(productPrimaryPrice($product)) ?></span>
                        </div>
                        <div class="product-bottom">
                            <span class="product-stock in">Top Rated</span>
                            <a href="<?= asset('product.php?slug=' . rawurlencode($product['slug'])) ?>" class="btn btn-cart-small">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Why Meta Galaxy</span>
                <h2>Built for modern wardrobes</h2>
            </div>
        </div>
        <div class="feature-grid">
            <div class="feature-card">
                <i class="bi bi-truck"></i>
                <h4>Fast shipping</h4>
                <p>Express delivery on all premium orders.</p>
            </div>
            <div class="feature-card">
                <i class="bi bi-shield-check"></i>
                <h4>Verified quality</h4>
                <p>Curated essentials with trusted materials.</p>
            </div>
            <div class="feature-card">
                <i class="bi bi-arrow-repeat"></i>
                <h4>Easy returns</h4>
                <p>Hassle-free returns within 7 days.</p>
            </div>
            <div class="feature-card">
                <i class="bi bi-headset"></i>
                <h4>Concierge support</h4>
                <p>Expert assistance from style to checkout.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-spacer">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Customer love</span>
                <h2>Testimonials</h2>
            </div>
        </div>
        <div class="review-grid">
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p>“The fit and finish are incredible. Meta Galaxy feels premium without being overdone.”</p>
                <div class="review-meta">
                    <div class="review-avatar">A</div>
                    <div>
                        <strong>Aarav</strong><br>
                        <small>Verified buyer</small>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p>“Their silhouettes feel futuristic but still wearable. I’ve already ordered a second set.”</p>
                <div class="review-meta">
                    <div class="review-avatar">S</div>
                    <div>
                        <strong>Sana</strong><br>
                        <small>Verified buyer</small>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p>“The 3D aesthetic is gorgeous, but the real win is the quality and comfort.”</p>
                <div class="review-meta">
                    <div class="review-avatar">K</div>
                    <div>
                        <strong>Kunal</strong><br>
                        <small>Verified buyer</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
