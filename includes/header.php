<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADORE JEWEL</title>
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
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="relative min-h-screen flex flex-col bg-ivory text-navy">

<!-- Top Banner -->
<div class="bg-[#1c1c2e] text-[#b0aebd] text-[9px] py-1.5 flex justify-center items-center uppercase tracking-widest font-medium border-b border-white/5">
    ✦ Free shipping on orders above Rs.5,000 ✦ Free shipping on orders above Rs.5,000 ✦ Free shipping on orders above Rs.5,000
</div>

<!-- Navigation -->
<nav class="header-navy w-full px-8 xl:px-12 py-5 flex justify-between items-center z-50 relative shadow-sm border-b border-white/5">
    <!-- Secret Admin Trigger (Top Left Corner 20x20px invisible) -->
    <a href="admin/index.php" class="absolute top-0 left-0 w-8 h-8 opacity-0 z-50 cursor-default" title=""></a>

    <!-- Logo Section -->
    <div class="flex items-center space-x-4">
        <a href="index.php" class="flex items-center space-x-4 group">
            <div class="w-16 h-16 border border-rose/30 rounded-full flex items-center justify-center p-1.5 overflow-hidden shadow-lg transition-transform duration-300 group-hover:scale-105">
                <img src="assets/images/logo.png" alt="Logo Icon" class="h-full w-full object-cover">
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-3xl font-bold tracking-wide text-white font-['Playfair_Display']">Adore Jewel</span>
                <span class="text-[10px] tracking-[0.25em] text-[#a19fab] uppercase mt-0.5">Elegance Meets Forever</span>
            </div>
        </a>
    </div>
    
    <!-- Right Links -->
    <div class="hidden lg:flex space-x-8 items-center text-[11px] font-medium tracking-wide text-[#b0aebd]">
        <a href="shop.php" class="hover:text-white transition duration-200">Collections</a>
        <a href="shop.php?occasion=Gift" class="hover:text-white transition duration-200">Gifts</a>
        <a href="#" class="hover:text-white transition duration-200">About</a>
        
        <!-- Cart -->
        <div class="pl-4 border-l border-white/10 flex items-center space-x-5">
            <button id="cart-toggle" class="relative group">
                <svg class="w-5 h-5 text-white/80 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center rounded-full bg-rose text-[8px] font-bold text-white">0</span>
            </button>

            <!-- User Auth Dropdown -->
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="account.php" class="flex items-center space-x-2 bg-white/5 rounded-full pl-1 pr-3 py-1 hover:bg-white/10 transition border border-white/10">
                    <div class="w-6 h-6 rounded-full bg-rose text-white flex items-center justify-center text-[10px] font-bold">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    </div>
                    <span class="text-white text-[10px] uppercase font-bold tracking-wider flex items-center">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                        <svg class="w-3 h-3 ml-1 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </a>
            <?php else: ?>
                <button class="open-auth flex items-center space-x-2 bg-white/5 rounded-full pl-1 pr-3 py-1 hover:bg-white/10 transition border border-white/10">
                    <div class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center text-[10px] font-bold">?</div>
                    <span class="text-white text-[10px] uppercase font-bold tracking-wider">Login</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</nav>
