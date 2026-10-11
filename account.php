<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

requireLogin();
$user = getCurrentUser();
$pageTitle = 'Account | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="account-page">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Account</span>
                <h2>Welcome, <?= e($user['name']) ?></h2>
            </div>
            <a href="<?= asset('logout.php') ?>" class="btn btn-outline-light">Logout</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="account-card p-4">
                    <h4>Profile</h4>
                    <ul class="list-unstyled mb-0 text-muted">
                        <li><strong class="text-light">Name:</strong> <?= e($user['name']) ?></li>
                        <li><strong class="text-light">Email:</strong> <?= e($user['email']) ?></li>
                        <li><strong class="text-light">Phone:</strong> <?= e($user['phone'] ?? 'Not provided') ?></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="account-card p-4">
                    <h4>Quick actions</h4>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= asset('orders.php') ?>" class="btn btn-outline-light">View orders</a>
                        <a href="<?= asset('wishlist.php') ?>" class="btn btn-outline-light">Wishlist</a>
                        <a href="<?= asset('cart.php') ?>" class="btn btn-outline-light">Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
