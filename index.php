<?php include 'includes/header.php'; ?>

<!-- Splash Screen -->
<div id="splash-screen" class="fixed inset-0 z-50 flex items-center justify-center transition-opacity duration-1000 overflow-hidden bg-navy">
    <!-- Bloom Effect -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="lotus-bloom"></div>
    </div>
    
    <div class="text-center relative z-10 flex flex-col items-center">
        <img src="assets/images/logo.png" alt="Adore Jewel Logo" class="w-56 h-56 md:w-72 md:h-72 object-contain mb-8 animate-logo-reveal">
        <p class="tracking-[0.4em] text-[12px] md:text-[14px] text-rose uppercase animate-tagline-reveal font-bold">Crafted to Adore</p>
    </div>
</div>

<style>
    @keyframes bloom-pulse {
        0% { transform: scale(0.3) rotate(0deg); opacity: 0; }
        50% { opacity: 0.6; }
        100% { transform: scale(3.5) rotate(45deg); opacity: 0; }
    }
    .lotus-bloom {
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: conic-gradient(from 0deg, transparent 0%, rgba(195,139,129,0.2) 20%, transparent 40%, rgba(195,139,129,0.2) 60%, transparent 80%, rgba(195,139,129,0.2) 100%);
        animation: bloom-pulse 4.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        filter: blur(10px);
    }
    .lotus-bloom::before, .lotus-bloom::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(from 45deg, transparent 0%, rgba(212,163,115,0.15) 20%, transparent 40%, rgba(212,163,115,0.15) 60%, transparent 80%, rgba(212,163,115,0.15) 100%);
        animation: bloom-pulse 4s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        animation-delay: 0.8s;
        filter: blur(8px);
    }
    .lotus-bloom::after {
        background: conic-gradient(from 90deg, transparent 0%, rgba(255,255,255,0.1) 20%, transparent 40%, rgba(255,255,255,0.1) 60%, transparent 80%, rgba(255,255,255,0.1) 100%);
        animation-delay: 1.6s;
    }
    @keyframes logoReveal {
        0% { transform: translateY(30px) scale(0.85); opacity: 0; filter: blur(15px); }
        100% { transform: translateY(0) scale(1); opacity: 1; filter: blur(0); }
    }
    .animate-logo-reveal {
        animation: logoReveal 2.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    @keyframes taglineReveal {
        0% { transform: translateY(15px); opacity: 0; letter-spacing: 0.1em; }
        100% { transform: translateY(0); opacity: 1; letter-spacing: 0.4em; }
    }
    .animate-tagline-reveal {
        animation: taglineReveal 2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        animation-delay: 1.2s;
        opacity: 0; /* starts hidden */
    }
</style>

<main class="flex-grow pt-10 pb-20 px-8 xl:px-12 bg-ivory">
    
    <div class="fade-in-up mt-8">
        <p class="text-rose tracking-[0.25em] text-[8px] uppercase font-bold mb-2">Curated For You</p>
        <div class="flex justify-between items-end mb-8">
            <h1 class="text-5xl font-medium serif text-navy tracking-wide">Featured Pieces</h1>
            <a href="shop.php" class="text-rose text-[11px] font-medium hover:text-navy transition flex items-center tracking-wide">View All <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
        </div>

        <!-- Featured Layout Grid corresponding to screenshot -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Hero Piece Left (Spans 2 cols) -->
            <a href="product.php?id=1" class="group relative rounded-[2rem] overflow-hidden lg:col-span-2 block shadow-lg h-[500px]">
                <img src="assets/images/pearl_necklace.png" alt="Rose Pearl Necklace" class="absolute inset-0 w-full h-full object-cover hover-scale">
                <!-- Inner Bottom Gradient -->
                <div class="absolute inset-0 card-image-overlay opacity-80"></div>
                
                <div class="absolute bottom-8 left-8 text-white z-10">
                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-[10px] font-bold mb-3 border border-white/10">10% OFF</span>
                    <h2 class="serif text-3xl font-medium mb-1">Rose Pearl Necklace</h2>
                    <p class="text-white/70 italic text-sm font-light mb-4">Timeless elegance for her special day</p>
                    <p class="text-xl font-bold">Rs. 7650</p>
                </div>
                
                <div class="absolute bottom-8 right-8 z-10">
                    <button class="bg-white text-navy font-bold text-[11px] px-6 py-2.5 rounded-full shadow-lg hover:bg-rose hover:text-white transition group-hover:scale-105">View Details</button>
                </div>
            </a>

            <!-- Right Column Top & Bottom -->
            <div class="flex flex-col gap-6 h-[500px]">
                <!-- Top Small Piece -->
                <a href="product.php?id=2" class="group relative rounded-[2rem] overflow-hidden flex-1 block shadow-sm border border-navy/5 bg-white flex flex-col items-center justify-center p-8">
                    <img src="assets/images/diamond_ring.png" alt="Diamond Solitaire" class="w-full h-full object-contain mb-8 group-hover:scale-105 transition duration-500 mix-blend-darken">
                    
                    <div class="absolute bottom-6 left-6 text-navy z-10 pr-12">
                        <h3 class="font-bold text-sm leading-tight mb-1">Diamond Solitaire Ring</h3>
                        <p class="text-rose font-medium text-xs">Rs. 45000</p>
                    </div>
                    
                    <button class="absolute bottom-6 right-6 w-8 h-8 rounded-full shadow border border-navy/10 bg-white text-navy flex items-center justify-center hover:bg-rose hover:text-white transition group-hover:-translate-y-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </a>

                <!-- Bottom Small Piece -->
                <a href="product.php?id=3" class="group relative rounded-[2rem] overflow-hidden flex-1 block shadow-sm border border-navy/5 bg-white flex flex-col items-center justify-center p-8">
                    <img src="assets/images/diamond_tennis_bracelet.png" alt="Tennis Bracelet" class="w-full h-full object-contain mb-8 group-hover:scale-105 transition duration-500 mix-blend-darken">
                    
                    <div class="absolute bottom-6 left-6 text-navy z-10 pr-12">
                        <h3 class="font-bold text-sm leading-tight mb-1">Diamond Tennis Bracelet</h3>
                        <p class="text-rose font-medium text-xs">Rs. 58900</p>
                    </div>
                    
                    <button class="absolute bottom-6 right-6 w-8 h-8 rounded-full shadow border border-navy/10 bg-white text-navy flex items-center justify-center hover:bg-rose hover:text-white transition group-hover:-translate-y-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </a>
            </div>

        </div>
    </div>
</main>

<script>
    setTimeout(() => {
        const s = document.getElementById('splash-screen');
        if (s) {
            s.style.opacity = '0';
            setTimeout(() => s.remove(), 1000);
        }
    }, 4800); // Wait 4.8 seconds for the lotus bloom animation
</script>

<?php include 'includes/footer.php'; ?>
