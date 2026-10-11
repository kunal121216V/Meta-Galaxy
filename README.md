# META GALAXY

A core PHP + MySQL e-commerce storefront built for XAMPP without Laravel or any PHP framework.

## Project structure

- `index.php` — homepage
- `shop.php` — shop and filter listing
- `product.php` — product detail page
- `cart.php` — cart overview
- `checkout.php` — checkout and order processing
- `orders.php`, `order-details.php` — customer order history
- `login.php`, `register.php`, `logout.php` — authentication
- `admin/` — admin dashboard and CRUD screens
- `config/database.php` — PDO database connection
- `includes/` — shared PHP helpers and layout
- `assets/` — CSS and JS assets
- `api/` — cart and search AJAX endpoints

## Setup

1. Place the project in `C:\xampp\htdocs\meta-galaxy`.
2. Ensure Apache and MySQL are running in XAMPP.
3. Open `http://localhost/meta-galaxy/` in the browser.
4. Use the existing `meta_galaxy` database. Do not modify or recreate it unless required by the admin setup.

## Admin login

The existing `users` table already contains an admin account. The live seed in the MySQL database currently uses:

- Email: `admin@metagalaxy.com`
- Password: `password`

For compatibility with older setup notes, the app also accepts the legacy default `Admin@123` when it matches the stored hash.

## Notes

- Cart and wishlist are session-based for guests and database-backed for logged-in users when applicable.
- The project uses the existing `meta_galaxy` schema without removing or resetting legacy tables.
- Products and categories are loaded directly from MySQL.
