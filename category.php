<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: ' . asset('shop.php'));
    exit;
}

$products = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT 12');
$products->execute([(int) $category['id']]);
$products = $products->fetchAll();

$pageTitle = e($category['name']) . ' | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker"><?= e($category['gender']) ?></span>
                <h2><?= e($category['name']) ?></h2>
            </div>
            <a href="<?= asset('shop.php') ?>" class="btn btn-outline-light">Back to shop</a>
        </div>

        <div class="glass-panel p-4 mb-4">
            <p class="mb-0 text-muted"><?= e($category['description'] ?? 'Curated pieces for the modern wardrobe.') ?></p>
        </div>

        <?php if (!$products): ?>
            <div class="empty-state">
                <h3>No products found.</h3>
                <p>This collection is being refreshed. Check back soon.</p>
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
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
