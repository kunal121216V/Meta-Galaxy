<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';

global $pageTitle;
$pageTitle = 'Order Successful | Meta Galaxy';
$orderNumber = $_GET['order'] ?? '';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="empty-state">
            <h3>Order placed successfully.</h3>
            <p>Your order number is <strong><?= e($orderNumber ?: 'MG-0000') ?></strong>.</p>
            <p>We have sent a confirmation to your email and you can track your order in your account.</p>
            <a href="<?= asset('orders.php') ?>" class="btn btn-primary mt-3">View my orders</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
