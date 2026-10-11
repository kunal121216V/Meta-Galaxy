<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
        header('Location: ' . asset('checkout.php'));
        exit;
    }

    $items = getCartItems();
    if (!$items) {
        setFlash('danger', 'Your cart is empty.');
        header('Location: ' . asset('cart.php'));
        exit;
    }

    $pdo = db();
    $total = 0; foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? $item['id']);
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if (!$product) {
            setFlash('danger', 'One or more products are unavailable.');
            header('Location: ' . asset('cart.php'));
            exit;
        }
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        if ($quantity > (int) $product['stock_quantity']) {
            setFlash('danger', 'Stock is not available for one item in your cart.');
            header('Location: ' . asset('cart.php'));
            exit;
        }
        $price = (float) ($item['price'] ?? ($product['sale_price'] > 0 ? $product['sale_price'] : $product['price']));
        $total += $price * $quantity;
    }

    $shipping = $total > 0 ? 250 : 0;
    $discount = $total > 4000 ? 500 : 0;
    $grandTotal = $total + $shipping - $discount;

    $userId = isLoggedIn() ? (int) $_SESSION['user_id'] : null;
    $orderNumber = generateOrderNumber();
    $shippingName = trim($_POST['shipping_name'] ?? '');
    $shippingPhone = trim($_POST['shipping_phone'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $shippingCity = trim($_POST['shipping_city'] ?? '');
    $shippingState = trim($_POST['shipping_state'] ?? '');
    $shippingPostalCode = trim($_POST['shipping_postal_code'] ?? '');
    $shippingCountry = trim($_POST['shipping_country'] ?? 'India');
    $paymentMethod = $_POST['payment_method'] ?? 'COD';

    if ($shippingName === '' || $shippingPhone === '' || $shippingAddress === '') {
        setFlash('danger', 'Please fill in your shipping details.');
        header('Location: ' . asset('checkout.php'));
        exit;
    }

    $pdo->beginTransaction();
    try {
        $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, order_number, subtotal, shipping_charge, discount, total, payment_status, order_status, shipping_name, shipping_phone, shipping_address, shipping_city, shipping_state, shipping_postal_code, shipping_country, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, "Pending", ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $orderStmt->execute([$userId, $orderNumber, $total, $shipping, $discount, $grandTotal, $paymentMethod === 'online' ? 'Paid' : 'COD', $shippingName, $shippingPhone, $shippingAddress, $shippingCity, $shippingState, $shippingPostalCode, $shippingCountry]);
        $orderId = (int) $pdo->lastInsertId();

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? $item['id']);
            $product = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
            $product->execute([$productId]);
            $productRow = $product->fetch();
            $quantity = (int) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['price'] ?? ($productRow['sale_price'] > 0 ? $productRow['sale_price'] : $productRow['price']));
            $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, size, color, price, subtotal, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $insertItem->execute([$orderId, $productId, $productRow['name'], $quantity, $item['size'] ?? null, $item['color'] ?? null, $unitPrice, $unitPrice * $quantity]);
            $stockUpdate = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?');
            $stockUpdate->execute([$quantity, $productId, $quantity]);
        }

        clearCart();
        $pdo->commit();
        header('Location: ' . asset('order-success.php?order=' . urlencode($orderNumber)));
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        setFlash('danger', 'Checkout failed. Please try again.');
        header('Location: ' . asset('cart.php'));
        exit;
    }
}

$pageTitle = 'Checkout | Meta Galaxy';
$items = getCartItems();
if (!$items) {
    setFlash('danger', 'Your cart is empty.');
    header('Location: ' . asset('cart.php'));
    exit;
}
$subtotal = 0; foreach ($items as $item) { $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 1)); }
$shipping = $subtotal > 0 ? 250 : 0; $discount = $subtotal > 4000 ? 500 : 0; $total = $subtotal + $shipping - $discount;
include __DIR__ . '/includes/header.php';
?>
<section class="cart-page">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Checkout</span>
                <h2>Complete your order</h2>
            </div>
        </div>

        <form method="POST" action="<?= asset('checkout.php') ?>">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="summary-box mb-4">
                        <h4>Customer information</h4>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Full name</label>
                                <input class="form-control" type="text" name="shipping_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input class="form-control" type="text" name="shipping_phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="summary-box mb-4">
                        <h4>Shipping address</h4>
                        <div class="row g-3 mt-2">
                            <div class="col-12"><label class="form-label">Address</label><input class="form-control" type="text" name="shipping_address" required></div>
                            <div class="col-md-4"><label class="form-label">City</label><input class="form-control" type="text" name="shipping_city"></div>
                            <div class="col-md-4"><label class="form-label">State</label><input class="form-control" type="text" name="shipping_state"></div>
                            <div class="col-md-4"><label class="form-label">Postal code</label><input class="form-control" type="text" name="shipping_postal_code"></div>
                            <div class="col-12"><label class="form-label">Country</label><input class="form-control" type="text" name="shipping_country" value="India"></div>
                        </div>
                    </div>

                    <div class="summary-box">
                        <h4>Payment method</h4>
                        <div class="mt-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="COD" checked>
                                <label class="form-check-label">Cash on Delivery</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="online">
                                <label class="form-check-label">Demo Online Payment (Demo only)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-box">
                        <h4>Order summary</h4>
                        <?php foreach ($items as $item): ?>
                            <?php $price = (float) ($item['price'] ?? 0); $quantity = (int) ($item['quantity'] ?? 1); ?>
                            <div class="d-flex justify-content-between mt-3">
                                <span><?= e($item['name']) ?> × <?= $quantity ?></span>
                                <span><?= formatPrice($price * $quantity) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
                        <div class="d-flex justify-content-between mt-2"><span>Shipping</span><span><?= formatPrice($shipping) ?></span></div>
                        <div class="d-flex justify-content-between mt-2"><span>Discount</span><span>-<?= formatPrice($discount) ?></span></div>
                        <div class="total-row"><span>Total</span><span><?= formatPrice($total) ?></span></div>
                        <button type="submit" class="btn btn-primary w-100 mt-4">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
