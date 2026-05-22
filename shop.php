<?php 
require_once 'includes/db.php';
include 'includes/header.php'; 

// Fetch Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

?>

<main class="flex-grow pt-10 pb-32 px-8 xl:px-12 bg-ivory">
    
    <div class="fade-in-up mt-8">
        <div class="flex justify-between items-end mb-8">
            <div>
                <p class="text-rose tracking-[0.25em] text-[8px] uppercase font-bold mb-2">Our Collection</p>
                <h1 class="text-5xl font-medium serif text-navy tracking-wide">All Pieces</h1>
            </div>
            
            <div class="flex space-x-4">
                <select class="bg-white border text-[11px] border-black/5 text-navy px-6 py-2 rounded-full appearance-none pr-10 shadow-sm focus:outline-none">
                    <option>All Occasions</option>
                    <option>Birthday</option>
                    <option>Anniversary</option>
                    <option>Wedding</option>
                    <option>Baby</option>
                </select>
                <select class="bg-white border text-[11px] border-black/5 text-navy px-6 py-2 rounded-full appearance-none pr-10 shadow-sm focus:outline-none">
                    <option>All Materials</option>
                    <option>Diamond</option>
                    <option>Gold</option>
                    <option>Pearl</option>
                </select>
            </div>
        </div>

        <!-- Product Grid corresponding to screenshot -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 gap-y-10">
            <?php foreach($products as $p): ?>
                <?php 
                    // Calculate original price if there's a discount
                    $discountStr = '';
                    $oldPriceStr = '';
                    if ($p['discount_percent'] > 0) {
                        $discountStr = '<span class="badge-pill badge-rose absolute top-4 left-4 z-10">' . $p['discount_percent'] . '% OFF</span>';
                        $oldPrice = $p['base_price'] / (1 - ($p['discount_percent']/100));
                        $oldPriceStr = '<span class="text-[9px] text-[#b0aebd] line-through ml-2">Rs. ' . floor($oldPrice) . '</span>';
                    }
                ?>
                <a href="product.php?id=<?= $p['id'] ?>" class="group block bg-white rounded-[2rem] overflow-hidden shadow-[0_4px_20px_rgba(35,34,54,0.04)] border border-navy/5 relative flex flex-col hover:-translate-y-1 transition duration-300">
                    
                    <!-- Frame Box Section -->
                    <div class="w-full aspect-square relative bg-ivory-dark/30 flex items-center justify-center p-8">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-contain hover-scale drop-shadow-xl mix-blend-darken">
                        
                        <!-- Top Left Discount -->
                        <?= $discountStr ?>
                        
                        <!-- Top Right Wishlist Star -->
                        <div class="absolute top-4 right-4 z-10 wish-star text-[#b0aebd]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        
                        <!-- Bottom Left Category Pill -->
                        <span class="badge-pill absolute bottom-3 left-4 z-10 leading-none py-1 px-3 text-[9px]"><?= htmlspecialchars($p['category']) ?></span>

                        <!-- Subtle bottom inner gradient so white pill pops -->
                        <div class="absolute inset-0 bg-gradient-to-t from-white/40 via-transparent to-transparent pointer-events-none"></div>
                    </div>
                    
                    <!-- Text Section -->
                    <div class="p-6 relative flex flex-col justify-between flex-grow">
                        <div>
                            <h3 class="text-navy font-bold text-sm tracking-tight mb-0.5"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="text-[#b0aebd] text-[10px] italic mb-2 line-clamp-1"><?= htmlspecialchars($p['description']) ?></p>
                            
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 cursor-default">
                                <div class="flex text-star text-[10px]">
                                    <?php 
                                        $rating = round($p['rating']);
                                        for($i=0; $i<5; $i++) {
                                            if ($i < $rating) {
                                                echo '<span>★</span>';
                                            } else {
                                                echo '<span class="text-gray-200">★</span>';
                                            }
                                        }
                                    ?>
                                </div>
                                <span class="text-[9px] text-[#b0aebd] font-medium">(<?= $p['review_count'] ?>)</span>
                            </div>
                        </div>

                        <!-- Price & Add Button -->
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-rose font-bold text-[13px]">Rs. <?= floor($p['base_price']) ?></span>
                                <?= $oldPriceStr ?>
                            </div>
                            <!-- Floating Add Button -->
                            <button class="btn-add-float absolute bottom-4 right-5" aria-label="Add or View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                    
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
