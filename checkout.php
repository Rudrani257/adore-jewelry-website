<?php
require_once 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? 'Card';
    $cartTotal = isset($_POST['cart_total']) ? (float)$_POST['cart_total'] : 0.00;
    $itemsJson = $_POST['cart_items'] ?? '[]';
    
    // Shipping Details Capture
    $fName = $_POST['first_name'] ?? '';
    $lName = $_POST['last_name'] ?? '';
    $shipping_name = trim($fName . ' ' . $lName);
    $shipping_address = $_POST['address'] ?? '';
    $shipping_apartment = $_POST['apartment'] ?? '';
    $shipping_city = $_POST['city'] ?? '';
    $shipping_state = $_POST['state'] ?? '';
    $shipping_pincode = $_POST['pin_code'] ?? '';
    $shipping_phone = $_POST['phone'] ?? '';
    
    // Pin-code based delivery allocation logic
    $tracking_company = 'Delhivery'; // default
    $fc = substr($shipping_pincode, 0, 1);
    if ($fc === '1' || $fc === '2') $tracking_company = 'BlueDart';
    if ($fc === '3' || $fc === '4') $tracking_company = 'Ecom Express';
    if ($fc === '5' || $fc === '6') $tracking_company = 'Xpressbees';

    $userId = $_SESSION['user_id'] ?? 1; // Fallback
    
    // Save to cookies if requested
    if (isset($_POST['save_info'])) {
        setcookie('adore_shipping', json_encode([$fName, $lName, $shipping_address, $shipping_apartment, $shipping_city, $shipping_state, $shipping_pincode, $shipping_phone]), time() + (86400 * 30), "/"); // 30 days
    }
    
    // Create order
    if ($cartTotal > 0) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_name, shipping_address, shipping_apartment, shipping_city, shipping_state, shipping_pincode, shipping_phone, tracking_company, tracking_step) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$userId, $cartTotal, $paymentMethod, $shipping_name, $shipping_address, $shipping_apartment, $shipping_city, $shipping_state, $shipping_pincode, $shipping_phone, $tracking_company]);
        $orderId = $pdo->lastInsertId();
        
        // Clear local storage cart visually and redirect via JS
        echo "<script>localStorage.removeItem('adore_cart'); window.location.href='certificate.php?order_id=$orderId';</script>";
        exit;
    }
}
?>

<main class="flex-grow pt-24 pb-20 px-8 bg-ivory">
    <div class="max-w-5xl mx-auto bg-white rounded-[2rem] border border-navy/5 shadow-xl overflow-hidden">
        <div class="grid md:grid-cols-2">
            <!-- Order Summary -->
            <div class="p-10 lg:p-14 bg-ivory-dark/20 flex flex-col justify-between">
                <div>
                    <h2 class="text-3xl serif text-navy mb-8">Order Summary</h2>
                    <div id="checkout-items-list" class="space-y-4 mb-6">
                        <!-- JS injected list here -->
                    </div>
                </div>
                
                <div class="border-t border-navy/10 pt-6">
                    <div class="flex justify-between text-sm mb-3 text-navy/60 uppercase tracking-widest"><span class="font-bold">Subtotal</span><span id="checkout-subtotal" class="font-bold text-navy">Rs. 0</span></div>
                    <div class="flex justify-between text-sm mb-3 text-navy/60 uppercase tracking-widest"><span class="font-bold">Insured Shipping</span><span class="text-rose font-bold">Complimentary</span></div>
                    <div class="flex justify-between text-xl font-bold text-navy mt-6 pt-6 border-t border-navy/10">
                        <span class="serif text-2xl">Total</span><span id="checkout-total" class="text-rose">Rs. 0</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details & Delivery -->
            <div class="p-10 lg:p-14 border-l border-navy/5 bg-white overflow-y-auto max-h-[85vh] checkout-scroll">
                <style>
                    .checkout-scroll::-webkit-scrollbar { width: 4px; }
                    .checkout-scroll::-webkit-scrollbar-thumb { background: #c38b81; border-radius: 4px; }
                </style>
                <form method="POST" id="checkout-form" class="space-y-8">
                    <input type="hidden" name="cart_total" id="hidden_cart_total" value="0">
                    <input type="hidden" name="cart_items" id="hidden_cart_items" value="[]">
                    
                    <?php
                        $s_f = $s_l = $s_a = $s_ap = $s_c = $s_st = $s_p = $s_ph = '';
                        if (isset($_COOKIE['adore_shipping'])) {
                            $savedData = json_decode($_COOKIE['adore_shipping'], true);
                            if(is_array($savedData) && count($savedData) === 8) {
                                list($s_f, $s_l, $s_a, $s_ap, $s_c, $s_st, $s_p, $s_ph) = $savedData;
                            }
                        }
                    ?>
                    
                    <!-- Delivery Form -->
                    <div class="space-y-4">
                        <h2 class="text-2xl serif text-navy mb-4">Delivery</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">First Name</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($s_f) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">Last Name</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($s_l) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($s_a) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                        </div>
                        
                        <div>
                            <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">Apartment, suite, etc. (optional)</label>
                            <input type="text" name="apartment" value="<?= htmlspecialchars($s_ap) ?>" class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                        </div>
                        
                        <div class="grid grid-cols-6 gap-4">
                            <div class="col-span-3">
                                <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">City</label>
                                <input type="text" name="city" value="<?= htmlspecialchars($s_c) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">State</label>
                                <input type="text" name="state" value="<?= htmlspecialchars($s_st) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy text-ellipsis">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2 overflow-hidden whitespace-nowrap">PIN code</label>
                                <input type="text" name="pin_code" value="<?= htmlspecialchars($s_p) ?>" required class="w-full px-3 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[9px] font-bold text-navy/50 uppercase tracking-widest mb-2">Phone</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($s_ph) ?>" required class="w-full px-4 py-3 bg-ivory-dark/30 border border-navy/10 rounded-xl focus:outline-none focus:border-rose transition text-sm text-navy">
                        </div>
                        
                        <label class="flex items-center space-x-3 cursor-pointer mt-2 group">
                            <input type="checkbox" name="save_info" class="w-4 h-4 text-rose bg-white border-navy/20 rounded focus:ring-rose accent-rose" <?= $s_f ? 'checked' : '' ?>>
                            <span class="text-xs text-navy/70 group-hover:text-navy transition">Save this information for next time</span>
                        </label>
                    </div>
                    
                    <!-- Shipping Method -->
                    <div class="space-y-4 pt-4 border-t border-navy/5">
                        <h2 class="text-lg font-bold text-navy">Shipping method</h2>
                        <div class="border border-navy/20 bg-ivory-dark/10 rounded-xl p-4 flex justify-between items-center shadow-sm">
                            <span class="text-sm font-medium text-navy">Standard shipping (Insured)</span>
                            <span class="text-sm font-bold text-navy uppercase">Free</span>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="space-y-4 pt-4 border-t border-navy/5">
                        <h2 class="text-lg font-bold text-navy mb-1">Payment</h2>
                        <p class="text-[10px] text-navy/50 mb-3">All transactions are secure and encrypted.</p>
                        
                        <div class="border border-navy/20 rounded-xl overflow-hidden shadow-sm">
                            <!-- Card Option -->
                            <label class="flex flex-col border-b border-navy/10 cursor-pointer">
                                <div class="flex items-center p-4 bg-ivory-dark/10 hover:bg-ivory transition">
                                    <input type="radio" name="payment_method" value="Card" checked class="w-4 h-4 text-rose accent-rose border-gray-300">
                                    <span class="ml-3 text-sm font-bold text-navy">Pay with Cards, Net Banking</span>
                                </div>
                                <!-- Expanded Card form (visual trick) -->
                                <div class="p-4 bg-white/50 space-y-3">
                                    <input type="text" placeholder="Card Number" class="w-full px-4 py-3 bg-white border border-navy/10 rounded-lg focus:outline-none focus:border-rose transition text-sm text-navy">
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" placeholder="MM / YY" class="px-4 py-3 bg-white border border-navy/10 rounded-lg focus:outline-none focus:border-rose text-sm text-navy text-center">
                                        <input type="text" placeholder="CVC" class="px-4 py-3 bg-white border border-navy/10 rounded-lg focus:outline-none focus:border-rose text-sm text-navy text-center">
                                    </div>
                                </div>
                            </label>

                            <!-- UPI Option -->
                            <label class="flex flex-col border-b border-navy/10 cursor-pointer">
                                <div class="flex items-center p-4 bg-ivory-dark/10 hover:bg-ivory transition">
                                    <input type="radio" name="payment_method" value="UPI/Cred" class="w-4 h-4 text-rose accent-rose border-gray-300">
                                    <span class="ml-3 text-sm font-bold text-navy tracking-wide">UPI / CRED Pay</span>
                                </div>
                            </label>
                            
                            <!-- COD Option -->
                            <label class="flex flex-col cursor-pointer">
                                <div class="flex items-center p-4 bg-ivory-dark/10 hover:bg-ivory transition">
                                    <input type="radio" name="payment_method" value="Cash on Delivery" class="w-4 h-4 text-rose accent-rose border-gray-300">
                                    <span class="ml-3 text-sm font-bold text-navy tracking-wide">Cash on Delivery (COD)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-navy/5">
                        <button type="submit" class="w-full bg-navy hover:bg-rose transition duration-400 text-white py-4 rounded-xl font-bold tracking-widest text-xs uppercase flex justify-center items-center shadow-xl hover:shadow-rose/30 hover:-translate-y-1 group">
                            <svg class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Confirm Order <span id="checkout-btn-total" class="ml-2 font-black">Rs. 0</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('adore_cart')) || [];
    const container = document.getElementById('checkout-items-list');
    const submitBtn = document.getElementById('checkout-form').querySelector('button[type="submit"]');
    
    if (cart.length === 0) {
        container.innerHTML = `<p class="text-xs text-rose italic py-8 border border-rose/10 bg-rose/5 rounded-xl text-center">Your vault is empty. <br><a href="shop.php" class="underline mt-2 inline-block font-bold">Return to shop</a></p>`;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hover:bg-navy');
        return;
    }

    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
        const html = `
        <div class="flex items-center space-x-5 bg-white p-3 rounded-2xl shadow-sm border border-navy/5">
            <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-ivory relative">
                <img src="${item.img}" alt="Preview" class="w-full h-full object-cover">
                <div class="absolute top-1 right-1 bg-navy text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center">${item.quantity}</div>
            </div>
            <div class="flex-grow pr-4">
                <p class="font-bold text-navy text-sm font-serif mb-1">${item.name}</p>
                <p class="text-[9px] text-navy/50 uppercase tracking-widest mb-1">${item.spec}</p>
                <div class="font-bold text-[13px] text-rose">Rs. ${(item.price * item.quantity).toLocaleString('en-IN')}</div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Populate totals
    const formattedTotal = 'Rs. ' + total.toLocaleString('en-IN');
    document.getElementById('checkout-subtotal').textContent = formattedTotal;
    document.getElementById('checkout-total').textContent = formattedTotal;
    document.getElementById('checkout-btn-total').textContent = formattedTotal;
    
    // Attach to hidden inputs for form submission processing
    document.getElementById('hidden_cart_total').value = total;
    document.getElementById('hidden_cart_items').value = JSON.stringify(cart);
});
</script>

<?php include 'includes/footer.php'; ?>
