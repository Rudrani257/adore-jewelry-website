<?php 
require_once 'includes/db.php';
include 'includes/header.php'; 

if (!isset($_GET['id'])) {
    header('Location: shop.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: shop.php');
    exit;
}

// Fetch Global Settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('global_gold_rate', 'profit_margin')");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$global_gold_rate = (float)($settings['global_gold_rate'] ?? 75.00);
$profit_margin = (float)($settings['profit_margin'] ?? 1.25);

// Use dynamic database image
$img = !empty($product['image_url']) ? $product['image_url'] : 'assets/images/diamond_ring.png';

?>

<main class="flex-grow pt-24 pb-20 px-8 bg-ivory">
    <div class="max-w-6xl mx-auto bg-ivory-dark rounded-[2.5rem] overflow-hidden shadow-2xl flex flex-col md:flex-row border border-navy/5">
        
        <!-- Product Image View -->
        <div class="w-full md:w-1/2 relative bg-ivory flex items-center justify-center p-12 overflow-hidden jewel-shine">
            <div class="absolute inset-0 bg-gradient-to-tr from-ivory-dark to-ivory opacity-50"></div>
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="relative z-10 w-full h-[500px] object-cover rounded-2xl shadow-2xl hover-scale transition-transform duration-700">
            <!-- Skeleton Loader overlay for preview -->
            <div id="image-loader" class="absolute inset-0 skeleton hidden z-20 opacity-80"></div>
        </div>

        <!-- Customization Lab -->
        <div class="w-full md:w-1/2 p-10 lg:p-16 flex flex-col justify-center bg-white text-navy relative">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="text-9xl serif font-bold">✦</span>
            </div>
            
            <p class="text-rose tracking-[0.3em] text-[10px] uppercase font-bold mb-4 relative z-10"><?= htmlspecialchars($product['category']) ?> Lab Configuration</p>
            <h1 class="text-5xl serif font-bold text-navy mb-6 leading-tight relative z-10"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-navy/60 leading-relaxed mb-10 text-sm font-light relative z-10"><?= htmlspecialchars($product['description']) ?></p>

            <!-- Customization Controls -->
            <div class="space-y-8 mb-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-3">Metal Matrix</label>
                    <div class="flex flex-wrap gap-3">
                        <button class="metal-btn py-2 px-5 border border-rose bg-rose text-white rounded-full text-xs font-bold tracking-wide transition shadow" data-mult="1.0" data-metal="18K Gold">18K Gold</button>
                        <button class="metal-btn py-2 px-5 border border-navy/20 text-navy/60 hover:border-rose hover:text-rose rounded-full text-xs font-bold tracking-wide transition" data-mult="1.2" data-metal="24K Gold">24K Gold</button>
                        <button class="metal-btn py-2 px-5 border border-navy/20 text-navy/60 hover:border-rose hover:text-rose rounded-full text-xs font-bold tracking-wide transition" data-mult="1.5" data-metal="Platinum">Platinum</button>
                    </div>
                </div>

                <?php if ($product['category'] === 'Diamond'): ?>
                <div>
                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-3">Stone Purity</label>
                    <div class="flex flex-wrap gap-3">
                        <button class="stone-btn py-2 px-5 border border-rose bg-rose text-white rounded-full text-xs font-bold tracking-wide transition shadow" data-price="0" data-stone="VVS1">VVS1</button>
                        <button class="stone-btn py-2 px-5 border border-navy/20 text-navy/60 hover:border-rose hover:text-rose rounded-full text-xs font-bold tracking-wide transition" data-price="12000" data-stone="Flawless">Flawless (+ Rs. 12,000)</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Price and Cart -->
            <div class="border-t border-navy/10 pt-8 flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[10px] text-navy/50 uppercase tracking-[0.2em] mb-1">Market Adjusted Value</p>
                    <p class="text-4xl font-bold text-navy serif" id="calculated-price">Rs. <span id="price-val"><?= floor($product['base_price']) ?></span></p>
                </div>
                <button id="add-to-cart-btn" class="btn-rose px-8 py-4 rounded-xl font-bold tracking-widest uppercase text-[10px] flex items-center shadow-lg hover:-translate-y-1 transition duration-300">
                    Add to Vault
                </button>
            </div>
            
            <p class="text-[9px] text-ivory/30 mt-6 tracking-wide uppercase relative z-10">*Price dynamically scaled using Live Global Market Parameters (<?= $global_gold_rate ?> / g).</p>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Hidden variables for JS DB pricing logic
    const basePrice = <?= (float)$product['base_price'] ?>;
    const metalWeight = <?= (float)$product['metal_weight'] ?>;
    const globalGoldRate = <?= $global_gold_rate ?>;
    const profitMargin = <?= $profit_margin ?? 1.25 ?>;
    const dynamicBase = basePrice + (metalWeight * globalGoldRate * profitMargin); // Simulated calculation
    
    let currentMetalMult = 1.0;
    let currentStonePrice = 0;
    const priceDisplay = document.getElementById('price-val');
    const imageLoader = document.getElementById('image-loader');

    function updatePrice() {
        total = Math.floor((dynamicBase * currentMetalMult) + currentStonePrice);
        priceDisplay.textContent = total.toLocaleString('en-IN');
    }

    // Initialize display price
    updatePrice();

    function setupButtons(className, isMetal) {
        const btns = document.querySelectorAll(`.${className}`);
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Remove active classes
                btns.forEach(b => {
                    b.classList.remove('border-rose', 'bg-rose', 'text-white', 'shadow');
                    b.classList.add('border-navy/20', 'text-navy/60');
                });
                // Add active to clicked
                const el = e.currentTarget;
                el.classList.remove('border-navy/20', 'text-navy/60');
                el.classList.add('border-rose', 'bg-rose', 'text-white', 'shadow');

                // Simulate loading image change with the skeleton
                imageLoader.classList.remove('hidden');
                setTimeout(() => imageLoader.classList.add('hidden'), 600);

                if (isMetal) {
                    currentMetalMult = parseFloat(el.dataset.mult);
                } else {
                    currentStonePrice = parseFloat(el.dataset.price);
                }
                updatePrice();
            });
        });
    }

    setupButtons('metal-btn', true);
    setupButtons('stone-btn', false);

    // Add to cart logic
    document.getElementById('add-to-cart-btn').addEventListener('click', () => {
        // Collect current specs
        let metal = '';
        let stone = '';
        document.querySelectorAll('.metal-btn.border-rose').forEach(b => metal = b.dataset.metal);
        document.querySelectorAll('.stone-btn.border-rose').forEach(b => stone = b.dataset.stone);
        
        const specString = stone ? `${metal}, ${stone}` : metal;
        
        window.addToCart({
            id: <?= $id ?>,
            name: "<?= htmlspecialchars($product['name']) ?>",
            img: "<?= $img ?>",
            price: total,
            spec: specString
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
