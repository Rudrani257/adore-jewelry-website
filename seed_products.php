<?php
require 'includes/db.php';

try {
    // 2. Clear old placeholders and reset Auto Increment
    $pdo->exec("TRUNCATE TABLE products");

    // 3. Update global gold rate from USD strictly to INR Base (~6500 Rs per gram)
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'global_gold_rate'");
    $stmt->execute(['6500.00']);

    // 4. Seed New Authentic Data matching screenshots
    $newProducts = [
        [
            'name' => 'Rose Pearl Necklace',
            'description' => 'Timeless elegance for her special day',
            'category' => 'Pearl',
            'base_price' => 7650.00,
            'metal_weight' => 2.50,
            'purity' => '18K',
            'image_url' => 'assets/images/pearl_necklace.png',
            'occasion' => 'Wedding',
            'discount_percent' => 10,
            'rating' => 4.9,
            'review_count' => 128,
            'is_featured' => true
        ],
        [
            'name' => 'Diamond Solitaire Ring',
            'description' => 'A forever promise, perfectly set',
            'category' => 'Diamond',
            'base_price' => 45000.00,
            'metal_weight' => 4.20,
            'purity' => '18K',
            'image_url' => 'assets/images/diamond_ring.png',
            'occasion' => 'Anniversary',
            'discount_percent' => 0,
            'rating' => 4.8,
            'review_count' => 81,
            'is_featured' => true
        ],
        [
            'name' => 'Diamond Tennis Bracelet',
            'description' => 'Where every moment sparkles',
            'category' => 'Diamond',
            'base_price' => 58900.00,
            'metal_weight' => 6.50,
            'purity' => '18K',
            'image_url' => 'assets/images/diamond_ring.png', // Fallback
            'occasion' => 'Birthday',
            'discount_percent' => 5,
            'rating' => 5.0,
            'review_count' => 156,
            'is_featured' => true
        ],
        [
            'name' => 'Gold Layered Bracelet',
            'description' => 'Stack, style, shine',
            'category' => 'Gold',
            'base_price' => 10200.00,
            'metal_weight' => 3.80,
            'purity' => '24K',
            'image_url' => 'assets/images/gold_bangle.png',
            'occasion' => 'Classic',
            'discount_percent' => 15,
            'rating' => 4.5,
            'review_count' => 64,
            'is_featured' => false
        ],
        [
            'name' => 'Pearl Drop Earrings',
            'description' => 'Grace in every movement',
            'category' => 'Pearl',
            'base_price' => 5500.00,
            'metal_weight' => 1.50,
            'purity' => '18K',
            'image_url' => 'assets/images/pearl_necklace.png', // Fallback
            'occasion' => 'Birthday',
            'discount_percent' => 0,
            'rating' => 4.7,
            'review_count' => 43,
            'is_featured' => false
        ],
        [
            'name' => 'Baby Charm Bracelet',
            'description' => 'Tiny wrists, precious memories',
            'category' => 'Gold',
            'base_price' => 3200.00,
            'metal_weight' => 1.20,
            'purity' => '24K',
            'image_url' => 'assets/images/gold_bangle.png', // Fallback
            'occasion' => 'Baby',
            'discount_percent' => 0,
            'rating' => 4.9,
            'review_count' => 31,
            'is_featured' => false
        ],
        [
            'name' => 'Platinum V Pendant',
            'description' => 'Modern geometry meets classic luxury',
            'category' => 'Platinum',
            'base_price' => 22400.00,
            'metal_weight' => 3.00,
            'purity' => 'PT950',
            'image_url' => 'assets/images/diamond_ring.png', // Fallback
            'occasion' => 'Classic',
            'discount_percent' => 0,
            'rating' => 4.6,
            'review_count' => 27,
            'is_featured' => false
        ],
        [
            'name' => 'Anniversary Gift Set',
            'description' => 'The perfect gift, beautifully told',
            'category' => 'Pearl',
            'base_price' => 18900.00,
            'metal_weight' => 5.50,
            'purity' => '18K',
            'image_url' => 'assets/images/pearl_necklace.png', // Fallback
            'occasion' => 'Anniversary',
            'discount_percent' => 20,
            'rating' => 5.0,
            'review_count' => 72,
            'is_featured' => false
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, category, base_price, metal_weight, purity, image_url, occasion, discount_percent, rating, review_count, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($newProducts as $p) {
        $stmt->execute([
            $p['name'],
            $p['description'],
            $p['category'],
            $p['base_price'],
            $p['metal_weight'],
            $p['purity'],
            $p['image_url'],
            $p['occasion'],
            $p['discount_percent'],
            $p['rating'],
            $p['review_count'],
            $p['is_featured']
        ]);
    }
    echo "Upgrade complete.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
