<?php
session_start();
require_once '../includes/db.php';

// Very basic admin check for demo purposes
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
        if ($_POST['admin_pass'] === 'admin123') { 
            $_SESSION['is_admin'] = true;
            header("Location: index.php");
            exit;
        } else {
            $error = "Unauthorized.";
        }
    } else {
        echo '<!DOCTYPE html><html><head><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-[#232236] flex items-center justify-center h-screen"><form method="POST" class="bg-[#2c2b42] p-8 rounded shadow-2xl border border-white/5"><div class="text-center mb-6"><h2 class="text-white font-serif text-2xl tracking-widest uppercase">Admin Vault</h2><p class="text-[#c38b81] text-xs mt-1 uppercase tracking-widest">Authorized Personnel Only</p></div><input type="password" name="admin_pass" class="px-4 py-3 bg-[#232236] text-white rounded mb-6 w-full border border-white/10 focus:outline-none focus:border-[#c38b81]" placeholder="Vault Passcode"><button class="bg-[#c38b81] hover:bg-[#a8726a] px-4 py-3 text-white rounded w-full uppercase tracking-widest font-bold text-xs transition">Authenticate</button></form></body></html>';
        exit;
    }
}

// Action Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // 1. Update Gold Rate
    if ($_POST['action'] === 'update_rate') {
        $rate = (float)$_POST['gold_rate'];
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'global_gold_rate'");
        $stmt->execute([$rate]);
        header("Location: index.php?tab=dashboard");
        exit;
    }
    
    // 2. Add New Product
    if ($_POST['action'] === 'add_product') {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $occasion = $_POST['occasion'];
        $base_price = (float)$_POST['base_price'];
        $metal_weight = (float)$_POST['metal_weight'];
        $purity = $_POST['purity'];
        $discount = (int)$_POST['discount_percent'];
        $description = $_POST['description'];
        $stock = (int)$_POST['stock'];

        // Handle Image Upload
        $target_dir = "../assets/images/";
        $uploadBaseUrl = "assets/images/";
        $default_img = $uploadBaseUrl . "diamond_ring.png"; // Fallback image

        if (isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == 0) {
            $fileExtension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
            $newFileName = strtolower(str_replace(' ', '_', $name)) . '_' . time() . '.' . $fileExtension;
            $target_file = $target_dir . $newFileName;
            
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $default_img = $uploadBaseUrl . $newFileName;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO products (name, description, category, occasion, base_price, metal_weight, purity, discount_percent, image_url, rating, review_count, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 5.0, 0, 0)");
        $stmt->execute([$name, $description, $category, $occasion, $base_price, $metal_weight, $purity, $discount, $default_img]);
        $new_id = $pdo->lastInsertId();

        $invStmt = $pdo->prepare("INSERT INTO inventory (product_id, stock_count) VALUES (?, ?)");
        $invStmt->execute([$new_id, $stock]);

        header("Location: index.php?tab=products");
        exit;
    }

    // 3. Delete Product
    if ($_POST['action'] === 'delete_product') {
        $id = (int)$_POST['product_id'];
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        header("Location: index.php?tab=products");
        exit;
    }

    // Update Order Tracking
    if ($_POST['action'] === 'update_tracking') {
        $id = (int)$_POST['order_id'];
        $step = (int)$_POST['tracking_step'];
        $status = 'pending';
        if ($step >= 3) $status = 'shipped';
        if ($step == 5) $status = 'completed';
        
        $pdo->prepare("UPDATE orders SET tracking_step = ?, status = ? WHERE id = ?")->execute([$step, $status, $id]);
        header("Location: index.php?tab=orders");
        exit;
    }

    // Delete Order
    if ($_POST['action'] === 'delete_order') {
        $id = (int)$_POST['order_id'];
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
        header("Location: index.php?tab=orders");
        exit;
    }

    // 4. Logout
    if ($_POST['action'] === 'logout') {
        unset($_SESSION['is_admin']);
        header("Location: ../index.php");
        exit;
    }
}

// Fetch Master Data
$tab = $_GET['tab'] ?? 'dashboard';

// Global Data
$goldRate = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'global_gold_rate'")->fetchColumn() ?: 6500.00;

// Analytics
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?: 0.00;
$totalStock = $pdo->query("SELECT SUM(stock_count) FROM inventory")->fetchColumn() ?: 0;

// Collections
$products = $pdo->query("SELECT p.*, i.stock_count FROM products p LEFT JOIN inventory i ON p.id = i.product_id ORDER BY p.id DESC")->fetchAll();
$orders = $pdo->query("SELECT o.id, o.total_amount, o.status, o.created_at, o.tracking_step, o.tracking_company, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 50")->fetchAll();
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA LUXE | Vault Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': '#232236',
                        'navy-light': '#2c2b42',
                        'ivory': '#faf6f0',
                        'ivory-dark': '#eee8db',
                        'rose': '#c38b81',
                        'star': '#d4a373'
                    }
                }
            }
        }
    </script>
    <style>
        .serif { font-family: 'Playfair Display', serif; }
        body { font-family: 'Inter', sans-serif; background: #faf6f0; color: #232236; }
        .tab-btn.active { background-color: #2c2b42; color: #c38b81; border-left: 4px solid #c38b81; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-navy text-white flex flex-col shadow-[15px_0_40px_rgba(35,34,54,0.1)] z-20">
        <div class="p-8 border-b border-white/5 text-center">
            <h2 class="text-3xl font-bold tracking-widest text-ivory serif uppercase">Vault</h2>
            <p class="text-[9px] uppercase tracking-[0.3em] text-rose mt-1">Admin Command</p>
        </div>
        <nav class="flex-grow py-6 flex flex-col">
            <a href="?tab=dashboard" class="tab-btn px-8 py-4 hover:bg-navy-light text-[11px] font-bold tracking-widest uppercase transition <?= $tab=='dashboard'?'active':'text-white/50' ?>">Overview</a>
            <a href="?tab=products" class="tab-btn px-8 py-4 hover:bg-navy-light text-[11px] font-bold tracking-widest uppercase transition <?= $tab=='products'?'active':'text-white/50' ?>">Products & Jewels</a>
            <a href="?tab=orders" class="tab-btn px-8 py-4 hover:bg-navy-light text-[11px] font-bold tracking-widest uppercase transition <?= $tab=='orders'?'active':'text-white/50' ?>">Ledger & Orders</a>
            <a href="?tab=users" class="tab-btn px-8 py-4 hover:bg-navy-light text-[11px] font-bold tracking-widest uppercase transition <?= $tab=='users'?'active':'text-white/50' ?>">Client Base</a>
            
            <div class="mt-auto">
                <a href="../index.php" class="block px-8 py-4 text-[10px] font-bold text-white/30 hover:text-white uppercase tracking-widest transition">⟵ Return to Store</a>
                <form method="POST" class="px-8 pb-6">
                    <input type="hidden" name="action" value="logout">
                    <button class="text-rose text-[10px] uppercase tracking-widest font-bold hover:text-white transition">Terminate Link</button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Engine -->
    <main class="flex-grow overflow-y-auto w-full">
        <!-- Top Nav Header -->
        <header class="bg-white border-b border-navy/5 px-12 py-6 flex justify-between items-center sticky top-0 z-10">
            <h1 class="text-3xl serif font-medium text-navy tracking-wide"><?= ucfirst($tab) ?></h1>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-[9px] uppercase tracking-widest text-navy/50 font-bold">Admin Active</p>
                    <p class="text-xs font-bold text-navy">Master Control</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-rose flex items-center justify-center text-white text-lg font-serif">M</div>
            </div>
        </header>

        <div class="p-12 pb-24 max-w-7xl mx-auto space-y-12">

            <?php if($tab === 'dashboard'): ?>
                <!-- Analytics Blocks -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-navy/5 shadow-sm transform transition hover:-translate-y-1">
                        <p class="text-[10px] uppercase font-bold text-navy/50 tracking-widest mb-2">Total Arsenal</p>
                        <p class="text-4xl text-navy font-serif"><?= number_format($totalStock) ?> <span class="text-sm font-sans text-navy/40">Units</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-navy/5 shadow-sm transform transition hover:-translate-y-1">
                        <p class="text-[10px] uppercase font-bold text-navy/50 tracking-widest mb-2">Total Revenue</p>
                        <p class="text-3xl text-rose font-bold">Rs. <?= number_format($totalRevenue) ?></p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-navy/5 shadow-sm transform transition hover:-translate-y-1">
                        <p class="text-[10px] uppercase font-bold text-navy/50 tracking-widest mb-2">Client DB</p>
                        <p class="text-4xl text-navy font-serif"><?= number_format($userCount) ?></p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-navy/5 shadow-sm transform transition hover:-translate-y-1">
                        <p class="text-[10px] uppercase font-bold text-navy/50 tracking-widest mb-2">Ledger</p>
                        <p class="text-4xl text-navy font-serif"><?= number_format($orderCount) ?> <span class="text-sm font-sans text-navy/40">Orders</span></p>
                    </div>
                </div>

                <!-- Market Engine -->
                <section class="bg-navy p-10 rounded-[2rem] shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-10 opacity-10 blur-sm pointer-events-none">
                        <span class="text-9xl serif font-bold text-white">✦</span>
                    </div>
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between">
                        <div class="mb-6 md:mb-0">
                            <span class="inline-block bg-white/10 px-3 py-1 text-rose text-[9px] uppercase tracking-widest font-bold rounded-full mb-3">Live Feed</span>
                            <h2 class="text-3xl text-white font-serif mb-2">Market Core Engine</h2>
                            <p class="text-white/50 text-xs w-2/3">Globally overrides product baseline pricing dynamically across all customer endpoints using INR per Gram.</p>
                        </div>
                        
                        <form method="POST" class="bg-navy-light/50 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
                            <input type="hidden" name="action" value="update_rate">
                            <label class="block text-[10px] font-bold text-rose uppercase tracking-widest mb-3">Gold Rate (INR / Gram)</label>
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-white/50 font-bold">Rs.</span>
                                    <input type="number" step="0.01" name="gold_rate" value="<?= htmlspecialchars($goldRate) ?>" class="pl-12 pr-4 py-3 bg-white/5 text-white border border-white/10 rounded-xl focus:outline-none focus:border-rose w-48 font-bold">
                                </div>
                                <button type="submit" class="bg-rose hover:bg-[#a8726a] text-white px-6 py-3 rounded-xl font-bold tracking-widest uppercase text-[10px] shadow-lg transition">Sync Target</button>
                            </div>
                        </form>
                    </div>
                </section>
            <?php endif; ?>

            <?php if($tab === 'products'): ?>
                <!-- Upload Product System -->
                <section class="bg-white p-8 md:p-10 rounded-[2rem] shadow-sm border border-navy/5 mb-10">
                    <h2 class="text-2xl font-serif text-navy mb-6">Synthesize New Jewel</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="action" value="add_product">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Nomenclature</label>
                                <input type="text" name="name" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose focus:ring-0 text-navy font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Description</label>
                                <input type="text" name="description" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose focus:ring-0 text-navy text-sm italic">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Category</label>
                                    <select name="category" class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose text-sm text-navy font-bold appearance-none">
                                        <option>Diamond</option><option>Gold</option><option>Pearl</option><option>Platinum</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Occasion</label>
                                    <select name="occasion" class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose text-sm text-navy font-bold appearance-none">
                                        <option>Classic</option><option>Wedding</option><option>Anniversary</option><option>Birthday</option><option>Baby</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Base Value (Rs.)</label>
                                    <input type="number" step="1" name="base_price" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose text-sm text-rose font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Discount %</label>
                                    <input type="number" step="1" name="discount_percent" value="0" class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:border-rose text-sm text-navy font-bold">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Weight(g)</label>
                                    <input type="number" step="0.01" name="metal_weight" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Purity</label>
                                    <input type="text" name="purity" placeholder="18K" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl text-sm uppercase">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">Stock</label>
                                    <input type="number" name="stock" required value="10" class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl text-sm font-bold text-navy">
                                </div>
                            </div>
                            
                            <!-- Image Uploader -->
                            <div>
                                <label class="block text-[10px] font-bold text-navy/50 uppercase tracking-widest mb-2">HD Asset Injector</label>
                                <div class="w-full px-4 py-2 border-2 border-dashed border-rose/30 bg-rose/5 rounded-xl text-center">
                                    <input type="file" name="product_image" accept="image/png, image/jpeg" class="w-full text-xs text-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-rose file:text-white hover:file:bg-[#a8726a] transition cursor-pointer">
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-navy/5 text-right">
                            <button type="submit" class="bg-navy hover:bg-rose text-white px-8 py-3 rounded-xl font-bold tracking-widest uppercase text-xs shadow-lg transition">Inject into DataCore</button>
                        </div>
                    </form>
                </section>

                <!-- Jewel Arsenal Feed -->
                <section class="bg-white rounded-[2rem] shadow-sm border border-navy/5 overflow-hidden">
                    <div class="p-8 border-b border-navy/5">
                        <h2 class="text-2xl font-serif text-navy">Arsenal Database</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-ivory-dark/30">
                                <tr class="text-[9px] uppercase tracking-widest text-navy/40">
                                    <th class="px-8 py-4 font-bold">Visual</th>
                                    <th class="px-8 py-4 font-bold">Metadata</th>
                                    <th class="px-8 py-4 font-bold">Matrix</th>
                                    <th class="px-8 py-4 font-bold">Value</th>
                                    <th class="px-8 py-4 font-bold text-right">Admin Override</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy/5">
                                <?php foreach($products as $p): ?>
                                <tr class="hover:bg-ivory/50 transition">
                                    <td class="px-8 py-4">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-ivory">
                                            <img src="../<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-full object-cover">
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <p class="font-bold text-sm text-navy"><?= htmlspecialchars($p['name']) ?></p>
                                        <p class="text-[10px] text-navy/50 uppercase mt-1 tracking-widest"><?= htmlspecialchars($p['category']) ?> / <?= htmlspecialchars($p['occasion']) ?></p>
                                    </td>
                                    <td class="px-8 py-4">
                                        <span class="inline-block px-2 py-0.5 bg-ivory border border-navy/10 rounded text-[10px] font-bold text-navy uppercase"><?= htmlspecialchars($p['purity']) ?></span>
                                        <p class="text-xs font-bold mt-2"><span class="text-navy/50">Stock:</span> <span class="text-rose"><?= $p['stock_count'] ?: '0' ?></span></p>
                                    </td>
                                    <td class="px-8 py-4">
                                        <p class="text-sm font-bold text-rose">Rs. <?= number_format($p['base_price']) ?></p>
                                        <?php if($p['discount_percent'] > 0): ?>
                                            <span class="inline-block px-2 py-0.5 bg-red-100 text-red-600 rounded text-[9px] font-bold uppercase tracking-widest mt-1">Sale: <?= $p['discount_percent'] ?>%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <form method="POST" onsubmit="return confirm('Initiate permanent purge of this jewel?');">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="text-[10px] uppercase font-bold tracking-widest text-red-400 hover:text-red-600 transition underline underline-offset-4 decoration-red-200 hover:decoration-red-400">Purge</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if($tab === 'orders'): ?>
                <!-- Orders View -->
                <section class="bg-white rounded-[2rem] shadow-sm border border-navy/5 overflow-hidden">
                    <div class="p-8 border-b border-navy/5">
                        <h2 class="text-2xl font-serif text-navy">Global Ledger</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-ivory-dark/30">
                                <tr class="text-[9px] uppercase tracking-widest text-navy/40">
                                    <th class="px-8 py-4 font-bold">Transaction ID</th>
                                    <th class="px-8 py-4 font-bold">Client Identity</th>
                                    <th class="px-8 py-4 font-bold">Revenue & Time</th>
                                    <th class="px-8 py-4 font-bold">Courier & Tracking</th>
                                    <th class="px-8 py-4 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy/5">
                                <?php foreach($orders as $o): ?>
                                <?php 
                                    $expDateStr = date('F j, Y', strtotime($o['created_at'] . ' + 5 days'));
                                    $isReached = time() >= strtotime($o['created_at'] . ' + 5 days');
                                    $step = $o['tracking_step'] ?? 1;
                                ?>
                                <tr class="hover:bg-ivory/50 transition">
                                    <td class="px-8 py-5 text-sm font-bold text-navy">#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-8 py-5 text-xs text-navy uppercase tracking-widest"><?= htmlspecialchars($o['user_name'] ?? 'TERMINATED') ?></td>
                                    <td class="px-8 py-5">
                                        <p class="text-sm font-bold text-rose">Rs. <?= number_format($o['total_amount']) ?></p>
                                        <p class="text-[10px] text-navy/50 font-bold tracking-widest uppercase mt-1"><?= date('M j, Y H:i', strtotime($o['created_at'])) ?></p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <form method="POST" class="flex flex-col gap-2 relative">
                                            <input type="hidden" name="action" value="update_tracking">
                                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                            <select name="tracking_step" onchange="this.form.submit()" class="w-full px-3 py-2 bg-white border border-navy/10 rounded focus:outline-none focus:border-rose text-xs text-navy font-bold uppercase tracking-widest appearance-none cursor-pointer">
                                                <option value="1" <?= $step == 1 ? 'selected' : '' ?>>Ordered</option>
                                                <option value="2" <?= $step == 2 ? 'selected' : '' ?>>Order Ready</option>
                                                <option value="3" <?= $step == 3 ? 'selected' : '' ?>>Shipped</option>
                                                <option value="4" <?= $step == 4 ? 'selected' : '' ?>>Out for Delivery</option>
                                                <option value="5" <?= $step == 5 ? 'selected' : '' ?>>Delivered</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center top-[-15px]">
                                                <svg class="w-3 h-3 text-navy/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                            <div class="text-[9px] font-bold tracking-widest uppercase text-navy/50 flex flex-col gap-1 mt-1">
                                                <span>Courier: <?= htmlspecialchars($o['tracking_company'] ?? "Delhivery") ?></span>
                                                <?php if($step == 5): ?>
                                                    <span class="text-green-600">Reached</span>
                                                <?php else: ?>
                                                    <span class="<?= $isReached ? 'text-red-500' : '' ?>">Est. Reach: <?= $expDateStr ?> <?= $isReached ? '(DELAYED)' : '' ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <form method="POST" onsubmit="return confirm('Do you want to completely purge this order log?');">
                                            <input type="hidden" name="action" value="delete_order">
                                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                            <button type="submit" class="text-[10px] uppercase font-bold tracking-widest text-red-500 hover:text-white hover:bg-red-500 px-3 py-2 rounded transition">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <?php if($tab === 'users'): ?>
                <section class="bg-white rounded-[2rem] shadow-sm border border-navy/5 p-12 text-center">
                    <span class="text-6xl text-rose font-serif mb-4 block">✦</span>
                    <h2 class="text-2xl font-serif text-navy mb-2">Classified Sub-systems Active</h2>
                    <p class="text-sm italic text-navy/50">Viewing exact user vectors requires encrypted clearances.</p>
                </section>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
