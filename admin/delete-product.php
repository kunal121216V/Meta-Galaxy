<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

requireAdmin();
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    setFlash('success', 'Product deleted.');
}
header('Location: ' . asset('admin/products.php'));
exit;
