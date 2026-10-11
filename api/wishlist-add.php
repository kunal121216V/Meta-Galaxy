<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/cart-functions.php';

header('Content-Type: application/json; charset=UTF-8');
$productId = (int) ($_POST['product_id'] ?? 0);
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if (wishlistExists($productId)) {
    echo json_encode(['success' => false, 'message' => 'Item already in wishlist.']);
    exit;
}

if (!addToWishlist($productId)) {
    echo json_encode(['success' => false, 'message' => 'Unable to save wishlist item.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Saved to wishlist.', 'count' => wishlistItemCount()]);
