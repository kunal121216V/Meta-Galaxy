<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/cart-functions.php';

header('Content-Type: application/json; charset=UTF-8');

$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$size = $_POST['size'] ?? null;
$color = $_POST['color'] ?? null;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if (!addToCart($productId, $quantity, $size, $color)) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Item added to cart.', 'count' => cartItemCount()]);
