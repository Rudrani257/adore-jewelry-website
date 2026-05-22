<?php
require_once 'includes/db.php';

try {
    $pdo->exec("ALTER TABLE orders 
        ADD COLUMN shipping_name VARCHAR(150),
        ADD COLUMN shipping_address TEXT,
        ADD COLUMN shipping_apartment VARCHAR(150),
        ADD COLUMN shipping_city VARCHAR(100),
        ADD COLUMN shipping_state VARCHAR(100),
        ADD COLUMN shipping_pincode VARCHAR(20),
        ADD COLUMN shipping_phone VARCHAR(20),
        ADD COLUMN tracking_company VARCHAR(100),
        ADD COLUMN tracking_step TINYINT DEFAULT 1;
    ");
    echo "Migration successful - columns added to orders table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
?>
