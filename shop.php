<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/cart-functions.php';

$pdo = db();
$pageTitle = 'Shop | Meta Galaxy';

$genderFilter = $_GET['gender'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$searchTerm = $_GET['q'] ?? '';
$priceFilter = $_GET['price'] ?? 'all';
$stockFilter = $_GET['stock'] ?? 'all';
$sort = $_GET['sort'] ?? 'featured';
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$where = ['p.is_active = 1'];
$params = [];
if ($genderFilter !== '') {
    $where[] = 'p.gender = ?';
    $params[] = $genderFilter;
}
if ($categoryFilter !== '') {
    $where[] = 'p.category_id = ?';
    $params[] = $categoryFilter;
}
if ($searchTerm !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?)';
    $searchLike = '%' . $searchTerm . '%';
    $params = array_merge($params, [$searchLike, $searchLike, $searchLike, $searchLike]);
}
if ($priceFilter === 'under-2000') { $where[] = 'p.price < 2000'; }
if ($priceFilter === '2000-4000') { $where[] = 'p.price BETWEEN 2000 AND 4000'; }
if ($priceFilter === 'above-4000') { $where[] = 'p.price > 4000'; }
if ($stockFilter === 'in-stock') { $where[] = 'p.stock_quantity > 0'; }
if ($stockFilter === 'low-stock') { $where[] = 'p.stock_quantity > 0 AND p.stock_quantity < 20'; }

$sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where);

if ($sort === 'newest') {
    $sql .= ' ORDER BY p.created_at DESC';
} elseif ($sort === 'price_asc') {
    $sql .= ' ORDER BY p.price ASC';
} elseif ($sort === 'price_desc') {
    $sql .= ' ORDER BY p.price DESC';
} else {
    $sql .= ' ORDER BY p.is_featured DESC, p.created_at DESC';
}

$totalStmt = db()->prepare(str_replace('SELECT p.*, c.name AS category_name', 'SELECT COUNT(*)', $sql));
$totalStmt->execute($params);
$totalProducts = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $limit));
$sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = db()->query('SELECT * FROM categories ORDER BY id ASC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="filter-sidebar">
                    <h4>Filters</h4>
                    <form method="GET" action="shop.php" class="filter-form">
                        <div class="filter-group">
                            <label>Search</label>
                            <input type="text" name="q" class="form-control" value="<?= e($searchTerm) ?>" placeholder="Search products...">
                        </div>
                        <div class="filter-group">
                            <label>Gender</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="" <?= $genderFilter === '' ? 'checked' : '' ?>>
                                <label class="form-check-label">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="Men" <?= $genderFilter === 'Men' ? 'checked' : '' ?>>
                                <label class="form-check-label">Men</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="Women" <?= $genderFilter === 'Women' ? 'checked' : '' ?>>
                                <label class="form-check-label">Women</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="Kids" <?= $genderFilter === 'Kids' ? 'checked' : '' ?>>
                                <label class="form-check-label">Kids</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="Shoes" <?= $genderFilter === 'Shoes' ? 'checked' : '' ?>>
                                <label class="form-check-label">Shoes</label>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label>Category</label>
                            <select class="form-select" name="category">
                                <option value="">All categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= (string) $categoryFilter === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Price</label>
                            <select class="form-select" name="price">
                                <option value="all" <?= $priceFilter === 'all' ? 'selected' : '' ?>>Any price</option>
                                <option value="under-2000" <?= $priceFilter === 'under-2000' ? 'selected' : '' ?>>Under ₹2,000</option>
                                <option value="2000-4000" <?= $priceFilter === '2000-4000' ? 'selected' : '' ?>>₹2,000 - ₹4,000</option>
                                <option value="above-4000" <?= $priceFilter === 'above-4000' ? 'selected' : '' ?>>Above ₹4,000</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Stock</label>
                            <select class="form-select" name="stock">
                                <option value="all" <?= $stockFilter === 'all' ? 'selected' : '' ?>>All</option>
                                <option value="in-stock" <?= $stockFilter === 'in-stock' ? 'selected' : '' ?>>In stock</option>
                                <option value="low-stock" <?= $stockFilter === 'low-stock' ? 'selected' : '' ?>>Low stock</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Apply filters</button>
                    </form>
                </div>
            </aside>

            <div class="col-lg-9">
                <div class="sort-bar">
                    <div>
                        <span class="kicker">Collection</span>
                        <h2 class="mb-0">Meta Galaxy Shop</h2>
                    </div>
                    <div class="page-controls">
                        <span class="text-muted"><?= $totalProducts ?> products</span>
                        <select class="form-select" onchange="location = this.value;">
                            <option value="<?= asset('shop.php?gender=' . urlencode($genderFilter) . '&category=' . urlencode($categoryFilter) . '&q=' . urlencode($searchTerm) . '&price=' . urlencode($priceFilter) . '&stock=' . urlencode($stockFilter) . '&sort=featured') ?>" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
                            <option value="<?= asset('shop.php?gender=' . urlencode($genderFilter) . '&category=' . urlencode($categoryFilter) . '&q=' . urlencode($searchTerm) . '&price=' . urlencode($priceFilter) . '&stock=' . urlencode($stockFilter) . '&sort=newest') ?>" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="<?= asset('shop.php?gender=' . urlencode($genderFilter) . '&category=' . urlencode($categoryFilter) . '&q=' . urlencode($searchTerm) . '&price=' . urlencode($priceFilter) . '&stock=' . urlencode($stockFilter) . '&sort=price_asc') ?>" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="<?= asset('shop.php?gender=' . urlencode($genderFilter) . '&category=' . urlencode($categoryFilter) . '&q=' . urlencode($searchTerm) . '&price=' . urlencode($priceFilter) . '&stock=' . urlencode($stockFilter) . '&sort=price_desc') ?>" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <?php if (!$products): ?>
                    <div class="empty-state">
                        <h3>No products found.</h3>
                        <p>Try another filter or browse the full collection.</p>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <article class="product-card">
                                <div class="product-image">
                                    <span class="product-badge"><?= e(productLabel($product)) ?></span>
                                    <div class="product-actions">
                                        <button type="button" class="product-action" data-wishlist="<?= (int) $product['id'] ?>" aria-label="Wishlist"><i class="bi bi-heart"></i></button>
                                    </div>
                                    <img src="<?= e(productImage($product)) ?>" alt="<?= e($product['name']) ?>">
                                </div>
                                <div class="product-info">
                                    <div class="product-meta"><span><?= e($product['category_name'] ?? 'Collection') ?></span><span>★ 4.9</span></div>
                                    <h3><?= e($product['name']) ?></h3>
                                    <div class="product-pricing">
                                        <span class="current"><?= formatPrice(productPrimaryPrice($product)) ?></span>
                                        <?php if (!empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['price']): ?>
                                            <span class="old"><?= formatPrice($product['price']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-bottom">
                                        <span class="product-stock <?= (int) $product['stock_quantity'] > 0 ? 'in' : 'low' ?>"><?= (int) $product['stock_quantity'] > 0 ? 'In Stock' : 'Low Stock' ?></span>
                                        <a href="<?= asset('product.php?slug=' . rawurlencode($product['slug'])) ?>" class="btn btn-cart-small">View</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <nav aria-label="Page navigation" class="d-flex justify-content-center">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= asset('shop.php?gender=' . urlencode($genderFilter) . '&category=' . urlencode($categoryFilter) . '&q=' . urlencode($searchTerm) . '&price=' . urlencode($priceFilter) . '&stock=' . urlencode($stockFilter) . '&sort=' . urlencode($sort) . '&page=' . $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
