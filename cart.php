<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$pdo = db();
$pageTitle = 'Cart | Meta Galaxy';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    if ($_POST['action'] === 'update') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));
        updateCartItem($productId, $qty);
        setFlash('success', 'Cart updated.');
    }

    if ($_POST['action'] === 'remove') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        removeCartItem($productId);
        setFlash('success', 'Item removed from cart.');
    }

    if ($_POST['action'] === 'clear') {
        clearCart();
        setFlash('success', 'Cart cleared.');
    }

    header('Location: ' . asset('cart.php'));
    exit;
}

$items = getCartItems();
$subtotal = 0;
foreach ($items as $item) {
    $quantity = (int) ($item['quantity'] ?? 1);
    $price = (float) ($item['price'] ?? ($item['sale_price'] ?? $item['original_price'] ?? 0));
    $subtotal += $price * $quantity;
}
$shipping = $subtotal > 0 ? 250 : 0;
$discount = $subtotal > 4000 ? 500 : 0;
$total = $subtotal + $shipping - $discount;

include __DIR__ . '/includes/header.php';
?>
<section class="cart-page">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Cart</span>
                <h2>Your shopping bag</h2>
            </div>
            <a href="<?= asset('shop.php') ?>" class="btn btn-outline-light">Continue shopping</a>
        </div>

        <?php if (!$items): ?>
            <div class="empty-state">
                <h3>Your cart is empty.</h3>
                <p>Add a few pieces from the collection and come back here.</p>
                <a href="<?= asset('shop.php') ?>" class="btn btn-primary mt-3">Shop now</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-card table-panel">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <?php $quantity = (int) ($item['quantity'] ?? 1); $price = (float) ($item['price'] ?? ($item['sale_price'] ?? $item['original_price'] ?? 0)); $itemSubtotal = $price * $quantity; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= e(productImage($item)) ?>" alt="<?= e($item['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 16px;">
                                                <div>
                                                    <div class="fw-bold"><?= e($item['name']) ?></div>
                                                    <?php if (!empty($item['size'])): ?><small class="text-muted">Size: <?= e($item['size']) ?></small><br><?php endif; ?>
                                                    <?php if (!empty($item['color'])): ?><small class="text-muted">Color: <?= e($item['color']) ?></small><?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= formatPrice($price) ?></td>
                                        <td>
                                            <form method="POST" action="<?= asset('cart.php') ?>" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?= (int) ($item['product_id'] ?? $item['id']) ?>">
                                                <input type="number" value="<?= $quantity ?>" min="1" max="99" name="quantity" class="form-control" style="width:90px;">
                                                <button type="submit" class="btn btn-sm btn-outline-light">Update</button>
                                            </form>
                                        </td>
                                        <td><?= formatPrice($itemSubtotal) ?></td>
                                        <td>
                                            <form method="POST" action="<?= asset('cart.php') ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?= (int) ($item['product_id'] ?? $item['id']) ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="summary-box">
                        <h4>Order summary</h4>
                        <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
                        <div class="d-flex justify-content-between mt-2"><span>Shipping</span><span><?= formatPrice($shipping) ?></span></div>
                        <div class="d-flex justify-content-between mt-2"><span>Discount</span><span>-<?= formatPrice($discount) ?></span></div>
                        <div class="total-row"><span>Total</span><span><?= formatPrice($total) ?></span></div>
                        <form method="POST" action="<?= asset('checkout.php') ?>">
                            <button type="submit" class="btn btn-primary w-100 mt-4">Proceed to Checkout</button>
                        </form>
                        <form method="POST" action="<?= asset('cart.php') ?>" class="mt-3">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline-light w-100">Clear Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
