<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ? LIMIT 1');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ' . asset('shop.php'));
    exit;
}

$relatedProducts = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT 4');
$relatedProducts->execute([(int) $product['category_id'], (int) $product['id']]);
$relatedProducts = $relatedProducts->fetchAll();

$sizeOptions = parseJsonOptions($product['size_options']);
$colorOptions = parseJsonOptions($product['color_options']);
$images = parseJsonOptions($product['additional_images']);
$mainImage = productImage($product);

$pageTitle = e($product['name']) . ' | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="product-detail-shell">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-6">
                <div class="detail-gallery">
                    <div class="detail-main-image">
                        <img src="<?= e($mainImage) ?>" alt="<?= e($product['name']) ?>" class="img-fluid">
                    </div>
                    <div class="detail-thumbs">
                        <?php foreach (array_merge([$mainImage], $images) as $image): ?>
                            <div class="detail-thumb">
                                <img src="<?= e($image) ?>" alt="<?= e($product['name']) ?>" class="img-fluid">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="detail-info">
                    <div class="eyebrow"><?= e($product['category_name'] ?? 'Premium product') ?></div>
                    <h1><?= e($product['name']) ?></h1>
                    <div class="detail-price">
                        <strong><?= formatPrice(productPrimaryPrice($product)) ?></strong>
                        <?php if (!empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['price']): ?>
                            <span><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted"><?= e($product['description']) ?></p>
                    <ul class="product-meta-list">
                        <li><strong>Brand:</strong> <?= e($product['brand'] ?? 'Meta Galaxy') ?></li>
                        <li><strong>Gender:</strong> <?= e($product['gender']) ?></li>
                        <li><strong>Stock:</strong> <?= (int) $product['stock_quantity'] ?> available</li>
                        <li><strong>SKU:</strong> <?= e($product['sku']) ?></li>
                    </ul>

                    <?php if ($sizeOptions): ?>
                        <div class="mb-3">
                            <label class="form-label">Size</label>
                            <select class="form-select" name="size" id="sizeSelect">
                                <?php foreach ($sizeOptions as $size): ?>
                                    <option value="<?= e($size) ?>"><?= e($size) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($colorOptions): ?>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <select class="form-select" name="color" id="colorSelect">
                                <?php foreach ($colorOptions as $color): ?>
                                    <option value="<?= e($color) ?>"><?= e($color) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="detail-actions">
                        <div class="qty-box">
                            <button type="button" class="qty-button" data-delta="minus">−</button>
                            <input type="number" id="quantityInput" value="1" min="1" max="<?= (int) $product['stock_quantity'] ?>">
                            <button type="button" class="qty-button" data-delta="plus">+</button>
                        </div>
                        <button class="btn btn-primary" data-add-to-cart="<?= (int) $product['id'] ?>" data-size="" data-color="">Add to Cart</button>
                        <button class="btn btn-outline-light" data-wishlist="<?= (int) $product['id'] ?>">Add to Wishlist</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-spacer">
            <div class="section-header">
                <div>
                    <span class="kicker">Related products</span>
                    <h2>More from this collection</h2>
                </div>
            </div>
            <?php if (!$relatedProducts): ?>
                <div class="empty-state"><p>No related products available.</p></div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($relatedProducts as $related): ?>
                        <article class="product-card">
                            <div class="product-image">
                                <img src="<?= e(productImage($related)) ?>" alt="<?= e($related['name']) ?>">
                            </div>
                            <div class="product-info">
                                <div class="product-meta"><span><?= e($related['category_name'] ?? 'Collection') ?></span><span>★ 4.8</span></div>
                                <h3><?= e($related['name']) ?></h3>
                                <div class="product-pricing">
                                    <span class="current"><?= formatPrice(productPrimaryPrice($related)) ?></span>
                                </div>
                                <div class="product-bottom">
                                    <span class="product-stock in">In Stock</span>
                                    <a href="<?= asset('product.php?slug=' . rawurlencode($related['slug'])) ?>" class="btn btn-cart-small">View</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
