<?php

function getSessionCart(): array
{
    if (!isset($_SESSION['guest_cart']) || !is_array($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    return $_SESSION['guest_cart'];
}

function saveSessionCart(array $cart): void
{
    $_SESSION['guest_cart'] = $cart;
}

function getGuestCartItems(): array
{
    $cart = getSessionCart();
    if (!$cart) {
        return [];
    }

    $items = [];
    foreach ($cart as $productId => $data) {
        $productId = (int) $productId;
        $stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.is_active = 1 LIMIT 1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            continue;
        }

        $quantity = max(1, (int) ($data['quantity'] ?? 1));
        $items[] = [
            'product_id' => $product['id'],
            'id' => $product['id'],
            'name' => $product['name'],
            'quantity' => $quantity,
            'size' => $data['size'] ?? null,
            'color' => $data['color'] ?? null,
            'price' => (float) ($product['sale_price'] > 0 ? $product['sale_price'] : $product['price']),
            'main_image' => productImage($product),
            'stock_quantity' => (int) $product['stock_quantity'],
            'category_name' => $product['category_name'] ?? '',
        ];
    }

    return $items;
}

function getDatabaseCartItems(): array
{
    if (!isLoggedIn()) {
        return [];
    }

    $stmt = db()->prepare(
        'SELECT c.id AS cart_id, ci.id AS cart_item_id, ci.product_id, ci.quantity, ci.size, ci.color, ci.price,
        p.name, p.main_image, p.stock_quantity, p.sale_price, p.price AS original_price, cat.name AS category_name
        FROM carts c
        LEFT JOIN cart_items ci ON ci.cart_id = c.id
        LEFT JOIN products p ON p.id = ci.product_id
        LEFT JOIN categories cat ON cat.id = p.category_id
        WHERE c.user_id = ? ORDER BY ci.id DESC'
    );
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

function getCartItems(): array
{
    if (isLoggedIn()) {
        return getDatabaseCartItems();
    }

    return getGuestCartItems();
}

function cartItemCount(): int
{
    $items = getCartItems();
    $count = 0;
    foreach ($items as $item) {
        $count += (int) ($item['quantity'] ?? 1);
    }
    return $count;
}

function wishlistItemCount(): int
{
    if (isLoggedIn()) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM wishlists WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return (int) $stmt->fetchColumn();
    }

    return isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
}

function ensureUserCart(): int
{
    if (!isLoggedIn()) {
        return 0;
    }

    $stmt = db()->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $cartId = $stmt->fetchColumn();

    if ($cartId) {
        return (int) $cartId;
    }

    $stmt = db()->prepare('INSERT INTO carts (user_id, session_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
    $stmt->execute([$_SESSION['user_id'], session_id()]);
    return (int) db()->lastInsertId();
}

function addToCart(int $productId, int $quantity = 1, ?string $size = null, ?string $color = null): bool
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        return false;
    }

    $quantity = max(1, $quantity);
    $stock = (int) ($product['stock_quantity'] ?? 0);
    if ($quantity > $stock) {
        return false;
    }

    if (isLoggedIn()) {
        $cartId = ensureUserCart();
        if (!$cartId) {
            return false;
        }

        $check = db()->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND COALESCE(size, "") = COALESCE(?, "") AND COALESCE(color, "") = COALESCE(?, "") LIMIT 1');
        $check->execute([$cartId, $productId, $size, $color]);
        $existing = $check->fetch();

        if ($existing) {
            $newQty = (int) $existing['quantity'] + $quantity;
            if ($newQty > $stock) {
                return false;
            }
            $update = db()->prepare('UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?');
            $update->execute([$newQty, $existing['id']]);
            return true;
        }

        $insert = db()->prepare('INSERT INTO cart_items (cart_id, product_id, quantity, size, color, price, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $insert->execute([$cartId, $productId, $quantity, $size, $color, (float) ($product['sale_price'] > 0 ? $product['sale_price'] : $product['price'])]);
        return true;
    }

    $cart = getSessionCart();
    $key = (string) $productId;
    $existing = $cart[$key] ?? ['quantity' => 0, 'size' => $size, 'color' => $color];
    $newQty = (int) ($existing['quantity'] ?? 0) + $quantity;

    if ($newQty > $stock) {
        return false;
    }

    $cart[$key] = ['quantity' => $newQty, 'size' => $size, 'color' => $color];
    saveSessionCart($cart);
    return true;
}

function updateCartItem(int $productId, int $quantity, ?string $size = null, ?string $color = null): bool
{
    $quantity = max(0, $quantity);

    if ($quantity === 0) {
        return removeCartItem($productId, $size, $color);
    }

    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        return false;
    }

    $stock = (int) ($product['stock_quantity'] ?? 0);
    if ($quantity > $stock) {
        return false;
    }

    if (isLoggedIn()) {
        $cartId = ensureUserCart();
        $sql = 'UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE cart_id = ? AND product_id = ? AND COALESCE(size, "") = COALESCE(?, "") AND COALESCE(color, "") = COALESCE(?, "")';
        $stmt = db()->prepare($sql);
        return $stmt->execute([$quantity, $cartId, $productId, $size, $color]);
    }

    $cart = getSessionCart();
    if (!isset($cart[(string) $productId])) {
        return false;
    }
    $cart[(string) $productId] = ['quantity' => $quantity, 'size' => $size, 'color' => $color];
    saveSessionCart($cart);
    return true;
}

function removeCartItem(int $productId, ?string $size = null, ?string $color = null): bool
{
    if (isLoggedIn()) {
        $cartId = ensureUserCart();
        $stmt = db()->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ? AND COALESCE(size, "") = COALESCE(?, "") AND COALESCE(color, "") = COALESCE(?, "")');
        return $stmt->execute([$cartId, $productId, $size, $color]);
    }

    $cart = getSessionCart();
    unset($cart[(string) $productId]);
    saveSessionCart($cart);
    return true;
}

function clearCart(): void
{
    if (isLoggedIn()) {
        $cartId = ensureUserCart();
        db()->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([$cartId]);
        return;
    }

    $_SESSION['guest_cart'] = [];
}

function wishlistExists(int $productId): bool
{
    if (isLoggedIn()) {
        $stmt = db()->prepare('SELECT id FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id'], $productId]);
        return (bool) $stmt->fetchColumn();
    }

    $wishlist = $_SESSION['wishlist'] ?? [];
    return isset($wishlist[$productId]);
}

function addToWishlist(int $productId): bool
{
    if (wishlistExists($productId)) {
        return false;
    }

    if (isLoggedIn()) {
        $stmt = db()->prepare('INSERT INTO wishlists (user_id, product_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
        return $stmt->execute([$_SESSION['user_id'], $productId]);
    }

    $wishlist = $_SESSION['wishlist'] ?? [];
    $wishlist[$productId] = true;
    $_SESSION['wishlist'] = $wishlist;
    return true;
}

function removeFromWishlist(int $productId): bool
{
    if (isLoggedIn()) {
        $stmt = db()->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
        return $stmt->execute([$_SESSION['user_id'], $productId]);
    }

    $wishlist = $_SESSION['wishlist'] ?? [];
    unset($wishlist[$productId]);
    $_SESSION['wishlist'] = $wishlist;
    return true;
}

function getWishlistItems(): array
{
    if (isLoggedIn()) {
        $stmt = db()->prepare('SELECT w.product_id, p.name, p.main_image, p.price, p.sale_price FROM wishlists w LEFT JOIN products p ON p.id = w.product_id WHERE w.user_id = ? ORDER BY w.created_at DESC');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll();
    }

    $wishlist = $_SESSION['wishlist'] ?? [];
    if (!$wishlist) {
        return [];
    }

    $keys = array_keys($wishlist);
    $placeholders = implode(',', array_fill(0, count($keys), '?'));
    $stmt = db()->prepare("SELECT id, name, main_image, price, sale_price FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($keys);
    return $stmt->fetchAll();
}
