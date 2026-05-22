// assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
    // 1. Splash Screen Fade
    const splash = document.getElementById('splash-screen');
    if (splash) {
        setTimeout(() => {
            splash.classList.add('hidden');
            setTimeout(() => splash.remove(), 1500);
        }, 2000);
    }

    // 2. Auth Modal Slide Logic
    const authModal = document.getElementById('auth-modal');
    const authWrapper = document.getElementById('auth-form-wrapper');
    if (authModal) {
        const toggleBtns = document.querySelectorAll('.toggle-auth');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                authWrapper.classList.toggle('show-signup');
            });
        });

        // Close Modal
        document.querySelectorAll('[data-close="auth-modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                authModal.classList.add('hidden');
            });
        });
    }

    // Open Auth Modal
    const openAuthBtns = document.querySelectorAll('.open-auth');
    openAuthBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if(authModal) authModal.classList.remove('hidden');
        });
    });

    // 3. Secret Admin Trigger
    const secretCorner = document.getElementById('secret-trigger');
    if (secretCorner) {
        let clickCount = 0;
        let timeout;
        
        secretCorner.addEventListener('click', () => {
            clickCount++;
            clearTimeout(timeout);
            
            if (clickCount >= 5) {
                // Secret redirect
                window.location.href = 'admin/index.php';
            }
            
            timeout = setTimeout(() => {
                clickCount = 0;
            }, 2000); // Reset after 2 seconds
        });
    }

    // 4. Slide-out Cart Logic
    const cartToggle = document.getElementById('cart-toggle');
    const cartDrawer = document.getElementById('cart-drawer');
    const cartOverlay = document.getElementById('cart-overlay');
    
    function openCart() {
        if(cartDrawer && cartOverlay) {
            cartDrawer.classList.remove('translate-x-full');
            cartOverlay.classList.remove('hidden');
            setTimeout(() => cartOverlay.classList.remove('opacity-0'), 10);
        }
    }

    function closeCart() {
        if(cartDrawer && cartOverlay) {
            cartDrawer.classList.add('translate-x-full');
            cartOverlay.classList.add('opacity-0');
            setTimeout(() => cartOverlay.classList.add('hidden'), 300);
        }
    }

    if (cartToggle) {
        cartToggle.addEventListener('click', openCart);
    }
    
    if (cartOverlay) {
        cartOverlay.addEventListener('click', closeCart);
    }
    
    document.querySelectorAll('[data-close="cart"]').forEach(btn => {
        btn.addEventListener('click', closeCart);
    });

    // 5. Client Cart Logic
    function updateCartUI() {
        const cart = JSON.parse(localStorage.getItem('adore_cart')) || [];
        const container = document.getElementById('cart-items-container');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const subtotalEl = document.getElementById('cart-subtotal');
        
        if (!container || !subtotalEl) return;
        
        let subtotal = 0;
        let count = 0;
        
        const existingItems = container.querySelectorAll('.cart-item-row');
        existingItems.forEach(e => e.remove());

        if (cart.length > 0) {
            if(emptyMsg) emptyMsg.style.display = 'none';
        } else {
            if(emptyMsg) emptyMsg.style.display = 'block';
        }

        cart.forEach((item, index) => {
            subtotal += item.price * item.quantity;
            count += item.quantity;
            
            const itemHTML = `
            <div class="cart-item-row flex space-x-4 items-center bg-ivory-dark p-3 rounded-xl border border-navy/5 shadow-sm">
                <div class="w-20 h-20 bg-ivory rounded-lg overflow-hidden flex-shrink-0">
                    <img src="${item.img}" alt="Preview" class="w-full h-full object-cover">
                </div>
                <div class="flex-grow">
                    <h4 class="serif font-bold text-navy text-sm">${item.name}</h4>
                    <p class="text-[10px] text-navy/60 mb-2 uppercase tracking-widest">${item.spec}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-rose">Rs. ${item.price.toLocaleString('en-IN')}</span>
                        <div class="flex items-center space-x-2 text-xs font-bold text-navy">
                            <button class="cart-qty-btn w-6 h-6 rounded-full border border-navy/20 hover:bg-navy hover:text-ivory transition flex items-center justify-center" data-idx="${index}" data-action="minus">-</button>
                            <span>${item.quantity}</span>
                            <button class="cart-qty-btn w-6 h-6 rounded-full border border-navy/20 hover:bg-navy hover:text-ivory transition flex items-center justify-center" data-idx="${index}" data-action="plus">+</button>
                        </div>
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', itemHTML);
        });

        subtotalEl.textContent = 'Rs. ' + subtotal.toLocaleString('en-IN');
        
        // Update nav badge
        const badge = document.querySelector('#cart-toggle span');
        if (badge) badge.textContent = count;
        
        // Bind quantity buttons
        document.querySelectorAll('.cart-qty-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = e.currentTarget.dataset.idx;
                const act = e.currentTarget.dataset.action;
                let c = JSON.parse(localStorage.getItem('adore_cart')) || [];
                if (act === 'plus') {
                    c[idx].quantity++;
                } else {
                    c[idx].quantity--;
                    if (c[idx].quantity <= 0) c.splice(idx, 1);
                }
                localStorage.setItem('adore_cart', JSON.stringify(c));
                updateCartUI();
            });
        });
    }

    // Explicitly attach to window so product.php can call it
    window.addToCart = function(item) {
        let cart = JSON.parse(localStorage.getItem('adore_cart')) || [];
        let found = cart.find(c => c.id == item.id && c.spec == item.spec);
        if(found) found.quantity += 1;
        else cart.push({...item, quantity: 1});
        localStorage.setItem('adore_cart', JSON.stringify(cart));
        updateCartUI();
        openCart();
    }

    updateCartUI();
});
