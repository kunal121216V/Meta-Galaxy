<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

requireLogin();
$userId = (int) $_SESSION['user_id'];
$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
$pageTitle = 'Orders | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Account</span>
                <h2>My orders</h2>
            </div>
        </div>

        <?php if (!$orders): ?>
            <div class="empty-state">
                <h3>No orders yet.</h3>
                <p>Your recent purchases will appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-panel">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= e($order['order_number']) ?></td>
                                <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                                <td><?= formatPrice($order['total']) ?></td>
                                <td><?= e($order['order_status']) ?></td>
                                <td><?= e($order['payment_status']) ?></td>
                                <td><a href="<?= asset('order-details.php?id=' . (int) $order['id']) ?>" class="btn btn-sm btn-outline-light">Details</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
