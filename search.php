<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$q = trim($_GET['q'] ?? '');
$pageTitle = 'Search | Meta Galaxy';

if ($q === '') {
    $products = [];
} else {
    $stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?) ORDER BY p.created_at DESC LIMIT 24');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like, $like]);
    $products = $stmt->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Search</span>
                <h2>Results for “<?= e($q) ?>”</h2>
            </div>
            <a href="<?= asset('shop.php') ?>" class="btn btn-outline-light">Back to shop</a>
        </div>

        <?php if (!$products): ?>
            <div class="empty-state">
                <h3>No products found.</h3>
                <p>Try a different keyword or browse the full collection.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
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
                            </div>
                            <div class="product-bottom">
                                <span class="product-stock in">In Stock</span>
                                <a href="<?= asset('product.php?slug=' . rawurlencode($product['slug'])) ?>" class="btn btn-cart-small">View</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
