<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'includes/header.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<main class="flex-grow pt-24 pb-20 px-8 bg-ivory">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16 fade-in-up">
            <h1 class="text-5xl font-bold serif text-navy mb-4 relative inline-block">
                My Vault
                <span class="absolute -top-4 -right-8 text-rose text-3xl">✦</span>
            </h1>
            <p class="text-navy/60 tracking-[0.2em] uppercase text-sm mt-4">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>.</p>
        </div>

        <div class="bg-ivory-dark/50 rounded-3xl p-10 border border-navy/5 shadow-md">
            <h2 class="text-2xl serif text-navy mb-8">Purchase History</h2>
            <?php if (empty($orders)): ?>
                <div class="text-center py-16 bg-ivory rounded-2xl border border-navy/10 border-dashed">
                    <p class="text-navy/50 font-light mb-4">Your vault is currently empty.</p>
                    <a href="shop.php" class="btn-primary inline-block px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase">Explore Collections</a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php $delay = 0.2; foreach ($orders as $order): ?>
                        <div class="bg-ivory p-6 rounded-2xl shadow-sm border border-navy/5 fade-in-up hover:border-navy/10 transition" style="animation-delay: <?= $delay ?>s">
                            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                                <div class="flex items-center space-x-6 w-full md:w-auto mb-4 md:mb-0">
                                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center text-ivory group transition duration-500 hover:bg-rose hover:rotate-[360deg] shadow-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-navy">Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h3>
                                        <p class="text-[10px] text-navy/50 uppercase tracking-widest mt-1">Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between w-full md:w-auto space-x-8">
                                    <div class="text-right">
                                        <p class="font-bold text-xl text-navy tracking-tight"><span class="text-xs mr-1 text-navy/50">Rs.</span><?= number_format($order['total_amount'], 2) ?></p>
                                        <p class="text-[9px] text-white font-bold uppercase tracking-widest bg-rose px-2 py-0.5 rounded-sm inline-block mt-1">
                                            <?= htmlspecialchars($order['payment_method'] ?? 'Prepaid') ?>
                                        </p>
                                    </div>
                                    <a href="certificate.php?order_id=<?= $order['id'] ?>" class="text-[10px] font-bold uppercase tracking-widest text-navy border border-navy/20 px-4 py-2 rounded-full hover:bg-navy hover:text-ivory transition">View Certificate</a>
                                </div>
                            </div>
                            
                            <!-- Delivery Tracker -->
                            <?php 
                                $step = $order['tracking_step'] ?? 1;
                                $company = $order['tracking_company'] ?? 'Delhivery';
                                $address = $order['shipping_city'] ? ($order['shipping_city'] . ', ' . $order['shipping_state']) : '';
                            ?>
                            <div class="border-t border-navy/10 pt-6">
                                <div class="flex justify-between items-center mb-4 text-[10px] uppercase font-bold tracking-widest">
                                    <span class="text-navy">Tracking Details</span>
                                    <span class="text-rose">Handled by <?= htmlspecialchars($company) ?> <?= $address ? " • Destination: " . htmlspecialchars($address) : '' ?></span>
                                </div>
                                
                                <div class="relative max-w-4xl mx-auto overflow-x-auto pb-4">
                                    <div class="flex items-center justify-between relative z-10 w-full min-w-[500px]">
                                        <!-- CSS Lines -->
                                        <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-navy/10 -z-10 -translate-y-1/2"></div>
                                        <div class="absolute top-1/2 left-4 h-0.5 bg-rose -z-10 -translate-y-1/2 transition-all duration-1000" style="width: <?= ($step - 1) * 25 ?>%"></div>
                                        
                                        <!-- Step 1 -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 <?= $step >= 1 ? 'bg-rose border-rose text-white' : 'bg-white border-navy/20 text-navy/30' ?> mb-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?= $step >= 1 ? 'text-navy' : 'text-navy/40' ?>">Ordered</span>
                                        </div>
                                        
                                        <!-- Step 2 -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 <?= $step >= 2 ? 'bg-rose border-rose text-white' : 'bg-white border-navy/20 text-navy/30' ?> mb-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?= $step >= 2 ? 'text-navy' : 'text-navy/40' ?>">Order Ready</span>
                                        </div>
                                        
                                        <!-- Step 3 -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 <?= $step >= 3 ? 'bg-rose border-rose text-white' : 'bg-white border-navy/20 text-navy/30' ?> mb-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?= $step >= 3 ? 'text-navy' : 'text-navy/40' ?>">Shipped</span>
                                        </div>
                                        
                                        <!-- Step 4 -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 <?= $step >= 4 ? 'bg-rose border-rose text-white' : 'bg-white border-navy/20 text-navy/30' ?> mb-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?= $step >= 4 ? 'text-navy' : 'text-navy/40' ?>">Out for Delivery</span>
                                        </div>
                                        
                                        <!-- Step 5 -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 <?= $step >= 5 ? 'bg-rose border-rose text-white' : 'bg-white border-navy/20 text-navy/30' ?> mb-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?= $step >= 5 ? 'text-navy' : 'text-navy/40' ?>">Delivered</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $delay += 0.15; endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
