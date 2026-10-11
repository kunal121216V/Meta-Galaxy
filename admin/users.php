<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$users = db()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
$pageTitle = 'Users | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div><span class="kicker">Users</span><h2>Accounts</h2></div>
            <a href="<?= asset('admin/index.php') ?>" class="btn btn-outline-light">Dashboard</a>
        </div>
        <div class="table-panel">
            <table class="table table-dark table-hover">
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= (int) $user['id'] ?></td>
                            <td><?= e($user['name']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= (int) $user['is_admin'] === 1 ? 'Admin' : 'Customer' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
