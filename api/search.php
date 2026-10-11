<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=UTF-8');
$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['items' => []]);
    exit;
}

$stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?) ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 5');
$like = '%' . $q . '%';
$stmt->execute([$like, $like, $like, $like]);
$rows = $stmt->fetchAll();

$items = [];
foreach ($rows as $row) {
    $items[] = [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'slug' => $row['slug'],
        'price' => (float) ($row['sale_price'] > 0 ? $row['sale_price'] : $row['price']),
        'image' => !empty($row['main_image']) ? $row['main_image'] : 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=750&q=80',
        'category_name' => $row['category_name'] ?? 'Collection',
    ];
}

echo json_encode(['items' => $items]);
