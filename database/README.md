# Database notes

This project uses the existing MySQL database: `meta_galaxy`.

## Tables inspected

The storefront is built around the tables already present in the database:

- `users`
- `categories`
- `products`
- `carts`
- `cart_items`
- `orders`
- `order_items`
- `wishlists`
- `contacts`

## Important schema findings

- `users` includes `id`, `name`, `email`, `phone`, `password`, `is_admin`.
- `categories` includes `id`, `name`, `slug`, `gender`, `description`, `image`.
- `products` includes `category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `sku`, `brand`, `gender`, `stock_quantity`, `size_options`, `color_options`, `main_image`, and `additional_images`.
- `carts` and `cart_items` are already defined and can store guest or logged-in customer carts.
- `orders` and `order_items` already hold order records and item snapshots.
- `wishlists` has the expected user-product relationship.
- `contacts` accepts message submissions from the storefront.

## Existing sample data

- 12 categories already exist.
- 30 products already exist.
- 3 users are already present, including an admin account.
- 1 order is already in the system.

## Required future change (if needed)

No destructive schema changes were required. The project uses the existing structure as-is. Any additional admin columns or order-specific fields should be added only if the application requires them in a later phase.
