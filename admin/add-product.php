<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$categories = db()->query('SELECT * FROM categories ORDER BY id ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $salePrice = (float) ($_POST['sale_price'] ?? 0);
        $sku = trim($_POST['sku'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $gender = trim($_POST['gender'] ?? 'Men');
        $stock = (int) ($_POST['stock_quantity'] ?? 0);
        $image = trim($_POST['image'] ?? '');

        if ($categoryId <= 0 || $name === '' || $price <= 0) {
            setFlash('danger', 'Category, name, and price are required.');
        } else {
            $slug = $slug !== '' ? $slug : strtolower(str_replace(' ', '-', $name));
            $stmt = db()->prepare('INSERT INTO products (category_id, name, slug, description, price, sale_price, sku, brand, gender, stock_quantity, main_image, is_featured, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, NOW(), NOW())');
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $sku ?: 'MG-' . time(), $brand, $gender, $stock, $image ?: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80']);
            setFlash('success', 'Product added successfully.');
            header('Location: ' . asset('admin/products.php'));
            exit;
        }
    }
}

$pageTitle = 'Add Product | Meta Galaxy';
include __DIR__ . '/../includes/header.php';
?>
<section class="admin-page">
    <div class="container">
        <div class="section-header">
            <div><span class="kicker">Catalog</span><h2>Add product</h2></div>
            <a href="<?= asset('admin/products.php') ?>" class="btn btn-outline-light">Back</a>
        </div>
        <div class="summary-box">
            <form method="POST" action="<?= asset('admin/add-product.php') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Category</label><select class="form-select" name="category_id"><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                    <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug"></div>
                    <div class="col-md-6"><label class="form-label">SKU</label><input class="form-control" name="sku"></div>
                    <div class="col-md-6"><label class="form-label">Brand</label><input class="form-control" name="brand"></div>
                    <div class="col-md-6"><label class="form-label">Gender</label><select class="form-select" name="gender"><option>Men</option><option>Women</option><option>Kids</option><option>Shoes</option></select></div>
                    <div class="col-md-4"><label class="form-label">Price</label><input class="form-control" type="number" step="0.01" name="price" required></div>
                    <div class="col-md-4"><label class="form-label">Sale price</label><input class="form-control" type="number" step="0.01" name="sale_price"></div>
                    <div class="col-md-4"><label class="form-label">Stock quantity</label><input class="form-control" type="number" name="stock_quantity" value="10"></div>
                    <div class="col-12"><label class="form-label">Image URL</label><input class="form-control" name="image"></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4"></textarea></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary">Save product</button></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
