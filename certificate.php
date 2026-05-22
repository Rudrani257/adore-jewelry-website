<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$orderId = (int)$_GET['order_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

$date = date('F jS, Y', strtotime($order['created_at']));
$certNo = 'AL-' . str_pad($orderId, 6, "0", STR_PAD_LEFT) . '-' . date('Y');
?>

<main class="flex-grow pt-10 pb-20 px-8 bg-gray-50 flex items-center justify-center">
    <div class="max-w-3xl w-full bg-white relative rounded shadow-xl overflow-hidden print:shadow-none print:w-full border-[12px] border-double border-gold p-2">
        <!-- Print Wrapper -->
        <div class="border border-gold p-12 bg-white relative">
            
            <!-- Watermark -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
                <span class="text-[12rem] serif font-bold text-yellow-600 rotate-[-30deg] uppercase">Aura Luxe</span>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 mx-auto bg-gradient-to-tr from-yellow-600 to-yellow-300 rounded-full flex items-center justify-center text-white mb-6 shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>

                <h1 class="text-4xl serif text-gray-900 mb-2 font-bold uppercase tracking-widest">Certificate of Authenticity</h1>
                <p class="text-gold tracking-[0.2em] text-xs uppercase font-semibold mb-10">And Digital Receipt</p>

                <div class="text-left bg-gray-50/50 p-8 rounded border border-gray-100 mb-8 max-w-lg mx-auto">
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        This document certifies that the jewelry described below is an authentic multi-carat creation, meticulously crafted by **AURA LUXE** artisans using materials of the highest grade and purity according to global standards.
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-sm mt-6">
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Certificate No.</div>
                        <div class="font-semibold text-gray-900 text-right"><?= $certNo ?></div>
                        
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Issue Date</div>
                        <div class="font-semibold text-gray-900 text-right"><?= $date ?></div>
                        
                        <div class="text-gray-500 uppercase tracking-wider text-xs">Item Authenticated</div>
                        <div class="font-semibold text-gray-900 text-right">Eternity Diamond Ring</div>

                        <div class="text-gray-500 uppercase tracking-wider text-xs">Specifications</div>
                        <div class="font-semibold text-gray-900 text-right">18K Gold, VVS1</div>

                        <div class="text-gray-500 uppercase tracking-wider text-xs mt-4 pt-4 border-t border-gray-200">Total Value</div>
                        <div class="font-bold text-lg text-gray-900 text-right mt-4 pt-4 border-t border-gray-200">$<?= number_format($order['total_amount'], 2) ?></div>
                    </div>
                </div>

                <div class="flex justify-center items-center mt-12 space-x-12 opacity-80">
                    <div class="text-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/ff/Signature_of_John_Hancock.png" alt="Signature" class="h-10 mx-auto mb-2 opacity-60">
                        <div class="w-32 border-t border-gray-400 mx-auto"></div>
                        <p class="text-xs uppercase tracking-widest text-gray-500 mt-2">Master Jeweler</p>
                    </div>
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full border border-gold flex items-center justify-center mx-auto mb-2 text-gold">SEAL</div>
                        <p class="text-xs uppercase tracking-widest text-gray-500">Official Seal</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<div class="text-center pb-12 print:hidden">
    <button onclick="window.print()" class="btn-primary px-8 py-3 rounded uppercase text-sm font-bold tracking-widest shadow-lg inline-flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002  2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Print Certificate
    </button>
</div>

<?php include 'includes/footer.php'; ?>
