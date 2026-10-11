<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$pageTitle = 'Admin Products | Meta Galaxy';
$products = db()->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="kicker">Catalog</span>
                <h2>Products</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= asset('admin/index.php') ?>" class="btn btn-outline-light">Dashboard</a>
                <a href="<?= asset('admin/add-product.php') ?>" class="btn btn-primary">Add product</a>
            </div>
        </div>

        <div class="table-panel">
            <table class="table table-dark table-hover">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= (int) $product['id'] ?></td>
                            <td><?= e($product['name']) ?></td>
                            <td><?= e($product['category_name'] ?? '-') ?></td>
                            <td><?= formatPrice($product['price']) ?></td>
                            <td><?= (int) $product['stock_quantity'] ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?= asset('admin/edit-product.php?id=' . (int) $product['id']) ?>" class="btn btn-sm btn-outline-light">Edit</a>
                                    <a href="<?= asset('admin/delete-product.php?id=' . (int) $product['id']) ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
