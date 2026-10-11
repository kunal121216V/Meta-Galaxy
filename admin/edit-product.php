<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$id = (int) ($_GET['id'] ?? 0);
$product = db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$product->execute([$id]);
$product = $product->fetch();
if (!$product) {
    header('Location: ' . asset('admin/products.php'));
    exit;
}

$categories = db()->query('SELECT * FROM categories ORDER BY id ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $categoryId = (int) ($_POST['category_id'] ?? $product['category_id']);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $salePrice = (float) ($_POST['sale_price'] ?? 0);
        $gender = trim($_POST['gender'] ?? $product['gender']);
        $stock = (int) ($_POST['stock_quantity'] ?? $product['stock_quantity']);
        $image = trim($_POST['image'] ?? '');
        $stmt = db()->prepare('UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, sale_price = ?, gender = ?, stock_quantity = ?, main_image = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$categoryId, $name, $description, $price, $salePrice, $gender, $stock, $image ?: $product['main_image'], $id]);
        setFlash('success', 'Product updated.');
        header('Location: ' . asset('admin/products.php'));
        exit;
    }
}

$pageTitle = 'Edit Product | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div><span class="kicker">Catalog</span><h2>Edit product</h2></div>
            <a href="<?= asset('admin/products.php') ?>" class="btn btn-outline-light">Back</a>
        </div>
        <div class="summary-box">
            <form method="POST" action="<?= asset('admin/edit-product.php?id=' . (int) $id) ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Category</label><select class="form-select" name="category_id"><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (int) $category['id'] === (int) $product['category_id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= e($product['name']) ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Gender</label><select class="form-select" name="gender"><option <?= $product['gender'] === 'Men' ? 'selected' : '' ?>>Men</option><option <?= $product['gender'] === 'Women' ? 'selected' : '' ?>>Women</option><option <?= $product['gender'] === 'Kids' ? 'selected' : '' ?>>Kids</option><option <?= $product['gender'] === 'Shoes' ? 'selected' : '' ?>>Shoes</option></select></div>
                    <div class="col-md-6"><label class="form-label">Price</label><input class="form-control" type="number" step="0.01" name="price" value="<?= (float) $product['price'] ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Sale price</label><input class="form-control" type="number" step="0.01" name="sale_price" value="<?= e($product['sale_price'] ?? 0) ?>"></div>
                    <div class="col-md-6"><label class="form-label">Stock quantity</label><input class="form-control" type="number" name="stock_quantity" value="<?= (int) $product['stock_quantity'] ?>"></div>
                    <div class="col-12"><label class="form-label">Image URL</label><input class="form-control" name="image" value="<?= e($product['main_image']) ?>"></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4"><?= e($product['description']) ?></textarea></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary">Update product</button></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
