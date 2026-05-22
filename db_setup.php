<?php
// db_setup.php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS aura_luxe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE aura_luxe_db");

    // Table: users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table: settings (Global Gold Rate)
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value VARCHAR(255) NOT NULL
    )");

    // Define initial Global Gold Rate (price per gram in USD for example, typically around 75$ for 24K)
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('global_gold_rate', '75.00')");
    $stmt->execute();
    
    // Add margin setting for admin adjustments
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('profit_margin', '1.25')");
    $stmt->execute();

    // Table: products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        description TEXT,
        category ENUM('Diamond', 'Gold', 'Pearl', 'Platinum') NOT NULL,
        base_price DECIMAL(10, 2) NOT NULL,
        metal_weight DECIMAL(8, 2) NOT NULL, -- in grams
        purity VARCHAR(20) NOT NULL, -- e.g. 18K, 24K, VVS1
        image_url VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table: inventory
    $pdo->exec("CREATE TABLE IF NOT EXISTS inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        stock_count INT NOT NULL DEFAULT 0,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Table: orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'completed', 'shipped') DEFAULT 'pending',
        payment_method VARCHAR(50) DEFAULT 'Card',
        shipping_name VARCHAR(150),
        shipping_address TEXT,
        shipping_apartment VARCHAR(150),
        shipping_city VARCHAR(100),
        shipping_state VARCHAR(100),
        shipping_pincode VARCHAR(20),
        shipping_phone VARCHAR(20),
        tracking_company VARCHAR(100),
        tracking_step TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Table: order_items
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10, 2) NOT NULL,
        customized_details TEXT, -- e.g. JSON string of {metal: 'Platinum', stone: 'Ruby'}
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE NO ACTION
    )");

    // Seed Admin User
    $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@auraluxe.com', :pass, 'admin')");
    $stmt->execute(['pass' => $adminPass]);

    // Seed Sample Products
    $products = [
        ['Eternity Diamond Ring', 'A stunning 1.5 Carat precision cut diamond ring set in 18K Gold.', 'Diamond', 0, 5.0, 'VVS1, 18K', 'assets/images/diamond_ring.jpg'],
        ['Classic Gold Bangle', 'Timeless 24K pure gold bangle designed for everyday luxury.', 'Gold', 0, 25.0, '24K', 'assets/images/gold_bangle.jpg'],
        ['Oceanic Pearl Necklace', 'Lustrous South Sea pearls intertwined with platinum accents.', 'Pearl', 1500.00, 10.0, 'AAA, 950', 'assets/images/pearl_necklace.jpg'],
        ['Royal Platinum Band', 'Sophisticated platinum wedding band.', 'Platinum', 800.00, 8.0, '950', 'assets/images/platinum_band.jpg']
    ];

    $stmtInsertProd = $pdo->prepare("INSERT INTO products (name, description, category, base_price, metal_weight, purity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtInsertInv = $pdo->prepare("INSERT INTO inventory (product_id, stock_count) VALUES (?, ?)");

    // Check if products exist before seeding
    $res = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($res == 0) {
        foreach ($products as $p) {
            $stmtInsertProd->execute($p);
            $prodId = $pdo->lastInsertId();
            $stmtInsertInv->execute([$prodId, random_int(5, 50)]);
        }
    }

    echo "Database setup completed successfully.";

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
