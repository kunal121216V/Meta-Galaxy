<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 50')->fetchAll();
$pageTitle = 'Orders | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div><span class="kicker">Orders</span><h2>Customer orders</h2></div>
            <a href="<?= asset('admin/index.php') ?>" class="btn btn-outline-light">Dashboard</a>
        </div>
        <div class="table-panel">
            <table class="table table-dark table-hover">
                <thead><tr><th>Order</th><th>User</th><th>Total</th><th>Status</th><th>Payment</th></tr></thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= e($order['order_number']) ?></td>
                            <td><?= e($order['shipping_name'] ?? '-') ?></td>
                            <td><?= formatPrice($order['total']) ?></td>
                            <td><?= e($order['order_status']) ?></td>
                            <td><?= e($order['payment_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
