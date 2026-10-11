<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['remove'])) {
    removeFromWishlist((int) $_POST['remove']);
    setFlash('success', 'Item removed from wishlist.');
    header('Location: ' . asset('wishlist.php'));
    exit;
}

$pageTitle = 'Wishlist | Meta Galaxy';
$items = getWishlistItems();
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Saved</span>
                <h2>Your wishlist</h2>
            </div>
        </div>

        <?php if (!$items): ?>
            <div class="empty-state">
                <h3>Your wishlist is empty.</h3>
                <p>Save your favorite looks for later.</p>
                <a href="<?= asset('shop.php') ?>" class="btn btn-primary mt-3">Browse collection</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($items as $item): ?>
                    <article class="product-card">
                        <div class="product-image">
                            <img src="<?= e(productImage($item)) ?>" alt="<?= e($item['name']) ?>">
                        </div>
                        <div class="product-info">
                            <h3><?= e($item['name']) ?></h3>
                            <div class="product-pricing">
                                <span class="current"><?= formatPrice(productPrimaryPrice($item)) ?></span>
                            </div>
                            <div class="product-bottom">
                                <form method="POST" action="<?= asset('wishlist.php') ?>">
                                    <input type="hidden" name="remove" value="<?= (int) ($item['product_id'] ?? $item['id']) ?>">
                                    <button type="submit" class="btn btn-outline-light">Remove</button>
                                </form>
                                <button class="btn btn-cart-small" data-add-to-cart="<?= (int) ($item['product_id'] ?? $item['id']) ?>">Move to cart</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
