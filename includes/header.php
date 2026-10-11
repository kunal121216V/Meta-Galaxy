<?php

$cartCount = function_exists('cartItemCount') ? cartItemCount() : 0;
$wishlistCount = function_exists('wishlistItemCount') ? wishlistItemCount() : 0;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) : 'Meta Galaxy' ?></title>
    <meta name="description" content="Meta Galaxy — fashion beyond dimensions.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
    <div class="page-shell">
        <nav class="navbar navbar-expand-lg fixed-top main-navbar">
            <div class="container">
                <a class="navbar-brand" href="<?= asset('index.php') ?>">
                    <span class="brand-mark">M</span>
                    <span class="brand-name">META GALAXY</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= asset('index.php') ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= asset('shop.php?gender=Men') ?>">Men</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= asset('shop.php?gender=Women') ?>">Women</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= asset('shop.php?gender=Kids') ?>">Kids</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= asset('shop.php?gender=Shoes') ?>">Shoes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= asset('shop.php') ?>">Shop</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>" href="<?= asset('about.php') ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= asset('contact.php') ?>">Contact</a></li>
                    </ul>

                    <div class="navbar-actions d-flex align-items-center gap-2">
                        <button class="nav-search-btn" type="button" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="bi bi-search"></i>
                        </button>
                        <a class="nav-icon-link" href="<?= asset('wishlist.php') ?>" aria-label="Wishlist">
                            <i class="bi bi-heart"></i>
                            <?php if ($wishlistCount > 0): ?><span class="nav-badge"><?= $wishlistCount ?></span><?php endif; ?>
                        </a>
                        <a class="nav-icon-link" href="<?= asset('cart.php') ?>" aria-label="Cart">
                            <i class="bi bi-bag"></i>
                            <?php if ($cartCount > 0): ?><span class="nav-badge"><?= $cartCount ?></span><?php endif; ?>
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <div class="dropdown">
                                <button class="btn user-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i>
                                    <span><?= e($_SESSION['user_name'] ?? 'Account') ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= asset('account.php') ?>">Account</a></li>
                                    <li><a class="dropdown-item" href="<?= asset('orders.php') ?>">Orders</a></li>
                                    <li><a class="dropdown-item" href="<?= asset('logout.php') ?>">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="btn btn-outline-light btn-sm rounded-pill" href="<?= asset('login.php') ?>">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start mobile-nav" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileMenuLabel">META GALAXY</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="mobile-menu-list">
                    <a href="<?= asset('index.php') ?>">Home</a>
                    <a href="<?= asset('shop.php?gender=Men') ?>">Men</a>
                    <a href="<?= asset('shop.php?gender=Women') ?>">Women</a>
                    <a href="<?= asset('shop.php?gender=Kids') ?>">Kids</a>
                    <a href="<?= asset('shop.php?gender=Shoes') ?>">Shoes</a>
                    <a href="<?= asset('shop.php') ?>">Shop</a>
                    <a href="<?= asset('about.php') ?>">About</a>
                    <a href="<?= asset('contact.php') ?>">Contact</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= asset('account.php') ?>">Account</a>
                        <a href="<?= asset('logout.php') ?>">Logout</a>
                    <?php else: ?>
                        <a href="<?= asset('login.php') ?>">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content search-modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="searchModalLabel">Search products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= asset('search.php') ?>" method="GET" class="search-form">
                            <div class="input-group search-input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control" placeholder="Search for shirts, shoes, pants..." aria-label="Search products">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                        <div id="searchResults" class="search-results mt-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <main>
            <?= renderFlash() ?>
