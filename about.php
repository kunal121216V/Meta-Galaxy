<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';
$pageTitle = 'About | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="kicker">About us</span>
                <h2 class="mb-3">Fashion beyond dimensions.</h2>
                <p class="text-muted">Meta Galaxy was built to reimagine everyday fashion through premium essentials, expressive silhouettes, and a future-ready aesthetic. We blend utility, comfort, and bold design for shoppers across generations.</p>
                <p class="text-muted">From performance-driven menswear to elevated womenswear and playful kidswear, every product is curated to feel premium, wearable, and distinctly modern.</p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1200&q=80" alt="Meta Galaxy fashion editorial" class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
