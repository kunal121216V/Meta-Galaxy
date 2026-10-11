<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

requireLogin();
$orderId = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) {
    header('Location: ' . asset('orders.php'));
    exit;
}

$itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$pageTitle = 'Order Details | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Order details</span>
                <h2><?= e($order['order_number']) ?></h2>
            </div>
            <a href="<?= asset('orders.php') ?>" class="btn btn-outline-light">Back to orders</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-panel">
                    <table class="table">
                        <thead>
                            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= e($item['product_name']) ?></td>
                                    <td><?= (int) $item['quantity'] ?></td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td><?= formatPrice($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="summary-box">
                    <h4>Order summary</h4>
                    <div class="d-flex justify-content-between mt-3"><span>Status</span><span><?= e($order['order_status']) ?></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Payment</span><span><?= e($order['payment_status']) ?></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Shipping</span><span><?= formatPrice($order['shipping_charge']) ?></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Discount</span><span><?= formatPrice($order['discount']) ?></span></div>
                    <div class="total-row"><span>Total</span><span><?= formatPrice($order['total']) ?></span></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
