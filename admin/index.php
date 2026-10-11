<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/cart-functions.php';

requireAdmin();
$pdo = db();
$totalProducts = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalOrders = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue = $pdo->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn();
$pending = $pdo->query('SELECT COUNT(*) FROM orders WHERE order_status = "Pending"')->fetchColumn();
$lowStock = $pdo->query('SELECT COUNT(*) FROM products WHERE stock_quantity < 20')->fetchColumn();
$pageTitle = 'Admin Dashboard | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Dashboard</span>
                <h2>Admin panel</h2>
            </div>
            <a href="<?= asset('admin/logout.php') ?>" class="btn btn-outline-light">Logout</a>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Total Products</p><h3><?= (int) $totalProducts ?></h3></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Total Users</p><h3><?= (int) $totalUsers ?></h3></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Total Orders</p><h3><?= (int) $totalOrders ?></h3></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Revenue</p><h3><?= formatPrice($revenue) ?></h3></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Pending Orders</p><h3><?= (int) $pending ?></h3></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-box"><p class="text-muted">Low Stock</p><h3><?= (int) $lowStock ?></h3></div></div>
        </div>

        <div class="mt-5 d-flex flex-wrap gap-3">
            <a href="<?= asset('admin/products.php') ?>" class="btn btn-primary">Manage products</a>
            <a href="<?= asset('admin/categories.php') ?>" class="btn btn-outline-light">Manage categories</a>
            <a href="<?= asset('admin/orders.php') ?>" class="btn btn-outline-light">Manage orders</a>
            <a href="<?= asset('admin/users.php') ?>" class="btn btn-outline-light">Users</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
