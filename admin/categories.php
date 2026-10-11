<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$categories = db()->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
$pageTitle = 'Categories | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div><span class="kicker">Catalog</span><h2>Categories</h2></div>
            <a href="<?= asset('admin/index.php') ?>" class="btn btn-outline-light">Dashboard</a>
        </div>
        <div class="table-panel">
            <table class="table table-dark table-hover">
                <thead><tr><th>ID</th><th>Name</th><th>Gender</th><th>Slug</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= (int) $category['id'] ?></td>
                            <td><?= e($category['name']) ?></td>
                            <td><?= e($category['gender']) ?></td>
                            <td><?= e($category['slug']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
