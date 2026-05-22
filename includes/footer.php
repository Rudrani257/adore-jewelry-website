<!-- Auth Modal -->
<div id="auth-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-navy/80 backdrop-blur-md">
    <div class="glass border border-rose/30 rounded-2xl w-full max-w-lg mx-4 relative overflow-hidden shadow-2xl auth-container min-h-[500px]">
        <!-- Close Button -->
        <button data-close="auth-modal" class="absolute top-4 right-4 text-ivory/50 hover:text-rose z-10 p-2 focus:outline-none transition-colors duration-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div id="auth-form-wrapper" class="auth-form-wrapper absolute inset-0 flex h-full items-center">
            <!-- Login Form -->
            <div class="auth-form">
                <h2 class="text-4xl font-bold text-center mb-2 serif text-ivory">Welcome Back</h2>
                <p class="text-center text-sm text-ivory/60 mb-8 font-light">Enter your credentials to access your vault.</p>
                <form action="auth.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <input type="email" name="email" required placeholder="Email Address" class="w-full px-4 py-3 bg-navy-light/50 border border-ivory/20 text-ivory rounded-lg focus:outline-none focus:border-rose focus:ring-1 focus:ring-rose transition text-sm placeholder-ivory/40">
                    </div>
                    <div>
                        <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-3 bg-navy-light/50 border border-ivory/20 text-ivory rounded-lg focus:outline-none focus:border-rose focus:ring-1 focus:ring-rose transition text-sm placeholder-ivory/40">
                    </div>
                    <button type="submit" class="w-full btn-primary py-3.5 rounded-lg font-bold tracking-widest uppercase text-sm mt-4 shadow-[0_0_15px_rgba(183,110,121,0.5)]">Sign In</button>
                </form>
                <div class="mt-8 text-center text-sm">
                    <span class="text-ivory/60">New to Adore Jewel?</span> 
                    <a href="#" class="text-rose font-bold hover:text-rose-light transition duration-300 toggle-auth ml-1">Create an account</a>
                </div>
            </div>

            <!-- Signup Form -->
            <div class="auth-form">
                <h2 class="text-4xl font-bold text-center mb-2 serif text-ivory">Join the Elite</h2>
                <p class="text-center text-sm text-ivory/60 mb-8 font-light">Experience bespoke jewelry curation.</p>
                <form action="auth.php" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="signup">
                    <div>
                        <input type="text" name="name" required placeholder="Full Name" class="w-full px-4 py-3 bg-navy-light/50 border border-ivory/20 text-ivory rounded-lg focus:outline-none focus:border-rose focus:ring-1 focus:ring-rose transition text-sm placeholder-ivory/40">
                    </div>
                    <div>
                        <input type="email" name="email" required placeholder="Email Address" class="w-full px-4 py-3 bg-navy-light/50 border border-ivory/20 text-ivory rounded-lg focus:outline-none focus:border-rose focus:ring-1 focus:ring-rose transition text-sm placeholder-ivory/40">
                    </div>
                    <div>
                        <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-3 bg-navy-light/50 border border-ivory/20 text-ivory rounded-lg focus:outline-none focus:border-rose focus:ring-1 focus:ring-rose transition text-sm placeholder-ivory/40">
                    </div>
                    <button type="submit" class="w-full btn-primary py-3.5 rounded-lg font-bold tracking-widest uppercase text-sm mt-4 shadow-[0_0_15px_rgba(183,110,121,0.5)]">Register</button>
                </form>
                <div class="mt-8 text-center text-sm">
                    <span class="text-ivory/60">Already a member?</span> 
                    <a href="#" class="text-rose font-bold hover:text-rose-light transition duration-300 toggle-auth ml-1">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Slide-out Glass Cart -->
<div id="cart-drawer" class="fixed inset-y-0 right-0 w-full max-w-md z-50 transform translate-x-full transition-transform duration-500 ease-in-out">
    <div class="h-full bg-ivory border-l border-rose/10 shadow-[-15px_0_40px_rgba(11,19,43,0.3)] flex flex-col">
        <div class="p-6 border-b border-navy/10 flex justify-between items-center bg-navy text-ivory">
            <h2 class="text-2xl serif font-bold tracking-wide">Your Vault</h2>
            <button data-close="cart" class="text-ivory/60 hover:text-rose focus:outline-none transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div id="cart-items-container" class="flex-grow p-6 overflow-y-auto space-y-6 text-navy">
            <p id="empty-cart-msg" class="text-xs text-center text-navy/40 italic py-10">Vault is empty.</p>
        </div>

        <div class="p-6 border-t border-navy/10 bg-ivory text-navy">
            <div class="flex justify-between items-center mb-6 text-sm font-semibold">
                <span class="text-navy/70 uppercase tracking-widest text-xs">Subtotal</span>
                <span id="cart-subtotal" class="text-2xl font-bold text-navy">Rs. 0</span>
            </div>
            <a href="checkout.php" class="block w-full btn-primary py-4 rounded-xl text-center font-bold tracking-widest text-sm uppercase shadow-lg hover:shadow-xl">Secure Checkout</a>
        </div>
    </div>
</div>
<!-- Cart Overlay -->
<div id="cart-overlay" class="fixed inset-0 bg-navy/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

<footer class="mt-auto header-navy text-ivory py-16 px-8 border-t border-rose/20">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10 border-b border-ivory/10 pb-10 mb-10">
        <div>
            <img src="assets/images/logo.png" alt="ADORE JEWEL Logo" class="h-16 w-auto mb-4 hover-scale mix-blend-screen opacity-90 rounded">
            <p class="text-sm text-ivory/60 leading-relaxed font-light">Crafting the world's most exquisite and radiant pieces for the bold and beautiful. Discover luxury redefined.</p>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase tracking-widest mb-4">Quick Links</h4>
            <ul class="space-y-2 text-sm text-ivory/60 font-light">
                <li><a href="shop.php" class="hover:text-rose transition">Collections</a></li>
                <li><a href="#" class="hover:text-rose transition">Bespoke Jewelry</a></li>
                <li><a href="#" class="hover:text-rose transition">Our Heritage</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase tracking-widest mb-4">Assistance</h4>
            <ul class="space-y-2 text-sm text-ivory/60 font-light">
                <li><a href="#" class="hover:text-rose transition">Contact Concierge</a></li>
                <li><a href="#" class="hover:text-rose transition">Shipping Policy</a></li>
                <li><a href="admin/index.php" class="hover:text-rose font-bold text-rose transition">Admin Access</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs text-ivory/50 font-light">
        <div>&copy; <?php echo date('Y'); ?> ADORE JEWEL INC. All Rights Reserved.</div>
        <div class="space-x-6 mt-4 md:mt-0 uppercase tracking-widest">
            <a href="#" class="hover:text-ivory transition">Privacy Policy</a>
            <a href="#" class="hover:text-ivory transition">Terms of Service</a>
        </div>
    </div>
</footer>

</body>
</html>
