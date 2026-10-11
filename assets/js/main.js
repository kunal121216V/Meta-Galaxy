document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.main-navbar');
    if (navbar) {
        const onScroll = () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    const searchInput = document.querySelector('input[name="q"]');
    const resultsBox = document.getElementById('searchResults');
    if (searchInput && resultsBox) {
        searchInput.addEventListener('input', async (event) => {
            const q = event.target.value.trim();
            if (!q) {
                resultsBox.innerHTML = '';
                return;
            }

            try {
                const response = await fetch('/meta-galaxy/api/search.php?q=' + encodeURIComponent(q));
                const data = await response.json();
                if (!data.items || data.items.length === 0) {
                    resultsBox.innerHTML = '<div class="empty-state small">No products found.</div>';
                    return;
                }

                resultsBox.innerHTML = data.items.map(item => `
                    <a href="/meta-galaxy/product.php?slug=${encodeURIComponent(item.slug)}" class="search-item">
                        <img src="${item.image}" alt="${item.name}">
                        <div>
                            <div>${item.name}</div>
                            <small>${item.category_name || 'Product'} • ₹${Number(item.price).toLocaleString('en-IN', { maximumFractionDigits: 2 })}</small>
                        </div>
                    </a>
                `).join('');
            } catch (error) {
                console.error('Search suggestion failed', error);
            }
        });
    }

    document.querySelectorAll('[data-add-to-cart]').forEach(button => {
        button.addEventListener('click', async () => {
            const productId = Number(button.dataset.addToCart);
            const quantityInput = document.getElementById('quantityInput');
            const sizeSelect = document.getElementById('sizeSelect');
            const colorSelect = document.getElementById('colorSelect');
            const quantity = quantityInput ? Number(quantityInput.value || 1) : Number(button.dataset.qty || 1);
            const size = sizeSelect ? sizeSelect.value : (button.dataset.size || '');
            const color = colorSelect ? colorSelect.value : (button.dataset.color || '');

            try {
                const response = await fetch('/meta-galaxy/api/cart-add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({ product_id: productId, quantity, size, color }).toString()
                });

                const data = await response.json();
                if (data.success) {
                    const badge = document.querySelector('.nav-badge');
                    if (badge) {
                        badge.textContent = String(data.count || Number(badge.textContent || 0) + 1);
                    }
                    alert(data.message || 'Added to cart');
                } else {
                    alert(data.message || 'Unable to add to cart');
                }
            } catch (error) {
                console.error('Cart add failed', error);
            }
        });
    });

    document.querySelectorAll('[data-wishlist]').forEach(button => {
        button.addEventListener('click', async () => {
            const productId = Number(button.dataset.wishlist);
            try {
                const response = await fetch('/meta-galaxy/api/wishlist-add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({ product_id: productId }).toString()
                });
                const data = await response.json();
                if (data.success) {
                    button.classList.toggle('active', true);
                    button.innerHTML = '<i class="bi bi-heart-fill"></i>';
                    alert(data.message || 'Saved to wishlist');
                } else {
                    alert(data.message || 'This item is already saved');
                }
            } catch (error) {
                console.error('Wishlist add failed', error);
            }
        });
    });

    document.querySelectorAll('.qty-button').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.closest('.qty-box')?.querySelector('input');
            if (!input) return;
            const min = Number(input.min || 1);
            const max = Number(input.max || 999);
            let value = Number(input.value || min);
            if (button.dataset.delta === 'minus') value = Math.max(min, value - 1);
            if (button.dataset.delta === 'plus') value = Math.min(max, value + 1);
            input.value = value;
        });
    });
});
