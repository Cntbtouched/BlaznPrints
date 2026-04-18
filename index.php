<?php
session_start();

// 1. Safe Database Connection
$pricesPath = __DIR__ . '/admin/data/prices.json';
$prices = ['tumbler' => 28, 'tshirt' => 22, 'tote' => 18, 'mug' => 15, 'sticker' => 5, 'custom' => 20];
$galleryItems = [];

try {
    @include 'config/db.php';
    
    // Load Prices from DB or JSON file
    if (file_exists($pricesPath)) {
        $prices = json_decode(file_get_contents($pricesPath), true) ?: $prices;
    }
    
    // Load Gallery
    $galleryPath = __DIR__ . '/admin/data/gallery.json';
    if (file_exists($galleryPath)) {
        $galleryItems = json_decode(file_get_contents($galleryPath), true) ?: [];
    }
} catch (Exception $e) {
    // Silently fail if data loading fails
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlaznPrints | Custom Tumblers, Tees & Prints</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/playfair-display@5/700.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/lato@5/400.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { display: ['Playfair Display', 'serif'], sans: ['Lato', 'sans-serif'] },
                    colors: { blaze: { 500: '#f97316', 600: '#ea580c' }, rustic: { 50: '#fafaf9', 900: '#1c1917' } }
                }
            }
        }
    </script>
    <style>
        * { scroll-behavior: smooth; }
        body { font-family: 'Lato', sans-serif; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); } 50% { box-shadow: 0 0 50px rgba(249, 115, 22, 0.6); } }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
        .animate-fade-in-right { animation: fadeInRight 0.8s ease-out forwards; }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2.5s ease-in-out infinite; }
        .animate-marquee { animation: marquee 25s linear infinite; }
        .hidden-section { opacity: 0; }
        .visible-section { opacity: 1; }
        .hero-gradient { background: linear-gradient(160deg, #1c1917 0%, #292524 25%, #44403c 55%, #7c2d12 100%); }
        .text-gradient { background: linear-gradient(135deg, #f97316, #fb923c, #facc15); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .text-gradient-fire { background: linear-gradient(135deg, #f97316 0%, #ea580c 30%, #dc2626 60%, #facc15 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-gradient { background: linear-gradient(135deg, #f97316, #ea580c); transition: all 0.3s ease; }
        .btn-gradient:hover { background: linear-gradient(135deg, #ea580c, #c2410c); transform: translateY(-3px); box-shadow: 0 12px 35px rgba(249, 115, 22, 0.45); }
        .btn-outline { border: 2px solid #f97316; color: #f97316; transition: all 0.3s ease; }
        .btn-outline:hover { background: #f97316; color: white; }
        .card-hover { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .card-hover:hover { transform: translateY(-10px) scale(1.02); }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: #f97316; transition: width 0.3s ease; }
        .nav-link:hover::after { width: 100%; }
        .gallery-item { transition: all 0.4s ease; }
        .gallery-item:hover { transform: scale(1.03); box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .form-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15); }
        .hamburger-line { transition: all 0.3s ease; }
        .hamburger-active .hamburger-line:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger-active .hamburger-line:nth-child(2) { opacity: 0; }
        .hamburger-active .hamburger-line:nth-child(3) { transform: rotate(-45deg) translate(7px, -6px); }
        .mobile-menu { transition: all 0.3s ease; }
        .toast { animation: slideDown 0.3s ease-out forwards; }
        .product-tab { transition: all 0.2s ease; }
        .product-tab.active { background: #f97316; color: white; }
        .product-tab:not(.active) { background: transparent; color: rgba(255,255,255,0.7); }
        .mockup-view.hidden { display: none; }
        .mockup-view.flex { display: flex; }
    </style>
</head>
<body class="bg-rustic-50 text-rustic-900 overflow-x-hidden">

<!-- Announcement Bar -->
<div class="bg-gradient-to-r from-blaze-600 via-blaze-500 to-blaze-600 text-white text-center py-2 text-sm font-semibold">🔥 FREE custom tote bag with every first order! — Montgomery, TX & Nationwide Shipping 🔥</div>

<!-- Navigation -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent" style="top: 36px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <a href="#home" class="flex items-center gap-3 group">
                <img src="logo.png" alt="BlaznPrints" class="w-12 h-12 rounded-lg object-cover shadow-lg" onerror="this.style.display='none'">
                <div><span class="text-2xl font-display font-bold text-white">Blazn<span class="text-blaze-500">Prints</span></span></div>
            </a>
            <div class="hidden lg:flex items-center gap-7">
                <a href="#home" class="nav-link text-white/90 hover:text-white text-sm font-bold">Home</a>
                <a href="#about" class="nav-link text-white/90 hover:text-white text-sm font-bold">Meet Diana</a>
                <a href="#gallery" class="nav-link text-white/90 hover:text-white text-sm font-bold">Gallery</a>
                <a href="#builder" class="nav-link text-white/90 hover:text-white text-sm font-bold">Design Studio</a>
                <a href="#contact" class="nav-link text-white/90 hover:text-white text-sm font-bold">Order</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
    <span class="text-white/80 text-sm font-bold" style="position:relative;z-index:999!important">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    <a href="logout.php" class="nav-link text-white/90 hover:text-white text-sm font-bold" style="position:relative;z-index:999!important">Logout</a>
<?php else: ?>
    <a href="login.php" class="nav-link text-white/90 hover:text-white text-sm font-bold" style="position:relative;z-index:999!important">Login</a>
    <a href="register.php" class="btn-gradient text-white px-4 py-2 rounded-full text-sm font-bold" style="position:relative;z-index:999!important">Register</a>
<?php endif; ?>
                
                <button onclick="openQuoteModal()" class="btn-gradient text-white px-6 py-2.5 rounded-full text-sm font-bold">🤠 Get a Quote</button>
            </div>
            <button id="hamburgerBtn" class="lg:hidden flex flex-col gap-1.5 p-2" onclick="toggleMobileMenu()">
                <span class="hamburger-line w-6 h-0.5 bg-white rounded-full"></span>
                <span class="hamburger-line w-6 h-0.5 bg-white rounded-full"></span>
                <span class="hamburger-line w-6 h-0.5 bg-white rounded-full"></span>
            </button>
        </div>
    </div>
    <div id="mobileMenu" class="mobile-menu hidden lg:hidden bg-rustic-900/98 backdrop-blur-lg border-t border-white/10">
        <div class="px-4 py-6 space-y-3">
            <a href="#home" onclick="closeMobileMenu()" class="block text-white/90 text-base font-bold py-2">Home</a>
            <a href="#builder" onclick="closeMobileMenu()" class="block text-white/90 text-base font-bold py-2">Design Studio</a>
            <a href="#contact" onclick="closeMobileMenu()" class="block text-white/90 text-base font-bold py-2">Order Now</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero-gradient relative min-h-screen flex items-center overflow-hidden pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in-left">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-5 py-2.5 mb-8 border border-white/10">
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-white/80 text-sm font-bold">Montgomery, TX — Orders Open Now</span>
                </div>
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-extrabold text-white leading-[1.05] mb-8">If It Ain't <br><span class="text-gradient-fire">Smoke'n</span>,<br>It Ain't <span class="text-gradient-fire">Print'n</span>!</h1>
                <p class="text-xl text-white/70 mb-10 max-w-lg leading-relaxed">I'm Diana — and I turn your ideas into <strong class="text-blaze-300">something you'll want to show off</strong>. Tumblers, tees, bags, stickers... all made by hand, right here in Texas.</p>
                <div class="flex flex-wrap gap-4 mb-12">
                    <a href="#builder" class="btn-gradient text-white px-10 py-4 rounded-full text-base font-bold shadow-2xl">🎨 Start Designing</a>
                    <a href="gallery.php" class="border-2 border-white/25 text-white px-10 py-4 rounded-full text-base font-bold hover:bg-white/10 transition-all">👀 View Full Gallery</a>
                </div>
                <div class="flex items-center gap-4 pt-8 border-t border-white/10">
                    <div class="flex -space-x-3">
                        <img src="blazn-pics/roses%20tumbler%20design.png" alt="" class="w-10 h-10 rounded-full border-2 border-blaze-500 object-cover bg-blaze-500" onerror="this.classList.add('bg-blaze-500')">
                        <img src="blazn-pics/gnome%20with%20heart%20tumbler%20design.png" alt="" class="w-10 h-10 rounded-full border-2 border-blaze-500 object-cover bg-blaze-400" onerror="this.classList.add('bg-blaze-400')">
                        <img src="blazn-pics/tote1.png" alt="" class="w-10 h-10 rounded-full border-2 border-blaze-500 object-cover bg-blaze-300" onerror="this.classList.add('bg-blaze-300')">
                    </div>
                    <div>
                        <div class="flex gap-0.5 text-yellow-400">★★★★★</div>
                        <p class="text-white/50 text-xs mt-1">Happy customers across Texas</p>
                    </div>
                </div>
            </div>
            <div class="animate-fade-in-right hidden lg:block">
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blaze-500 via-blaze-400 to-red-500 rounded-3xl blur-xl opacity-25 animate-pulse-glow"></div>
                    <img id="hero-main-img" src="blazn-pics/roses%20tumbler%20design.png" alt="Custom Tumbler" class="relative rounded-3xl shadow-2xl w-full object-cover animate-float transition-opacity duration-500" style="max-height: 520px;" onerror="this.style.background='#292524'; this.style.minHeight='400px';">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Marquee -->
<div class="bg-blaze-500 text-white py-3 overflow-hidden">
    <div class="animate-marquee">
        <div class="flex whitespace-nowrap">
            <span class="mx-8 text-sm font-bold tracking-widest uppercase">🔥 Tumblers • T-Shirts • Hoodies • Tote Bags • Wine Bags • Mugs • Coasters • Stickers • Glow Stickers • Bumper Stickers • Hats • Custom Everything 🔥</span>
            <span class="mx-8 text-sm font-bold tracking-widest uppercase">🔥 Tumblers • T-Shirts • Hoodies • Tote Bags • Wine Bags • Mugs • Coasters • Stickers • Glow Stickers • Bumper Stickers • Hats • Custom Everything 🔥</span>
        </div>
    </div>
</div>

<!-- About Section -->
<section id="about" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="hidden-section" data-animation="fadeInLeft">
                <div class="relative">
                    <div class="absolute -inset-3 bg-gradient-to-br from-blaze-200 to-blaze-100 rounded-3xl blur-sm opacity-50"></div>
                    <img src="diana-portrait.jpg" alt="Diana Jordan" class="relative rounded-3xl shadow-2xl w-full object-cover" style="min-height: 500px;" onerror="this.parentElement.innerHTML='<div class=\'relative rounded-3xl shadow-2xl bg-gradient-to-br from-blaze-100 to-blaze-200 flex items-center justify-center\' style=\'min-height: 500px;\'><div class=\'text-center\'><div class=\'text-6xl mb-4\'>👩</div><p class=\'text-blaze-600 font-bold\'>Diana Jordan</p></div></div>'">
                </div>
            </div>
            <div class="hidden-section" data-animation="fadeInRight">
                <span class="text-blaze-500 font-bold text-sm uppercase tracking-widest">The Woman Behind the Print</span>
                <h2 class="text-4xl sm:text-5xl font-display font-extrabold mt-3 mb-6">Meet <span class="text-gradient">Diana Jordan</span></h2>
                <div class="space-y-5 text-rustic-600 leading-relaxed text-lg">
                    <p>I'm a full-time insurance agent with State Farm by day, and by night? I'm creating custom prints that make people smile. My family suggested I try printing as a hobby — and once I started, <strong class="text-rustic-800">I couldn't stop</strong>.</p>
                    <p>There's something about bringing someone's idea to life that just gets me. I do <strong class="text-rustic-800">everything myself</strong> — every design, every print, every package. When you order from BlaznPrints, you're ordering from me, Diana. Not some faceless company.</p>
                    <p class="text-blaze-600 font-bold text-xl italic font-display">"This isn't just a business to me. It's my passion. And I treat every order like it's my own."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<!-- Products Section -->
<section id="products" class="py-24 bg-rustic-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blaze-500 font-bold text-sm uppercase tracking-widest">What I Make</span>
            <h2 class="text-4xl sm:text-5xl font-display font-extrabold mt-3 mb-4">If You Can <span class="text-gradient">Dream It</span>,<br>I Can Print It</h2>
            <p class="text-rustic-600 max-w-2xl mx-auto text-lg">Here's what I make — but honestly, I'm always up for trying something new. Just ask!</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <!-- Tumbler -->
            <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-md group">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                    <img src="blazn-pics/gnome%20with%20heart%20tumbler%20design.png" alt="Tumbler" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-3 right-3 bg-blaze-500 text-white px-3 py-1 rounded-full text-xs font-bold">⭐ #1 Seller</div>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold mb-1">20oz Tumblers</h3>
                    <p class="text-rustic-400 text-sm">My absolute favorite to make. Custom designs on insulated tumblers.</p>
                </div>
            </div>
            <!-- T-Shirt -->
            <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-md group">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-pink-50 to-red-50">
                    <img src="blazn-pics/t-shirt1.png" alt="T-Shirt" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold mb-1">T-Shirts & Tees</h3>
                    <p class="text-rustic-400 text-sm">Vibrant prints that don't crack or fade.</p>
                </div>
            </div>
            <!-- Tote -->
            <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-md group">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50">
                    <img src="blazn-pics/tote1.png" alt="Tote Bag" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold mb-1">Tote Bags</h3>
                    <p class="text-rustic-400 text-sm">From Halloween designs to everyday carry.</p>
                </div>
            </div>
            <!-- Wine Bag -->
            <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-md group">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50">
                    <img src="blazn-pics/winebag1.png" alt="Wine Bag" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold mb-1">Wine Bags</h3>
                    <p class="text-rustic-400 text-sm">Show up to the party with style.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Featured Gallery (Shortened) -->
<section id="gallery" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blaze-500 font-bold text-sm uppercase tracking-widest">Featured Work</span>
            <h2 class="text-4xl sm:text-5xl font-display font-extrabold mt-3 mb-4">Straight From <span class="text-gradient">My Studio</span></h2>
            <a href="gallery.php" class="inline-block mt-4 btn-gradient text-white px-8 py-3 rounded-full font-bold">View Full Gallery →</a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php 
            $featured = array_slice($galleryItems, 0, 4); 
            if(empty($featured)) $featured = [['file'=>'roses%20tumbler%20design.png'],['file'=>'tote1.png'],['file'=>'t-shirt1.png'],['file'=>'winebag1.png']];
            foreach($featured as $item): ?>
            <div class="gallery-item rounded-2xl overflow-hidden shadow-lg cursor-pointer" onclick="openLightbox(this)">
                <img src="blazn-pics/<?=$item['file']?>" alt="Featured" class="w-full h-64 object-cover">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DESIGN STUDIO SECTION -->
<section id="builder" class="py-24 bg-rustic-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="text-blaze-500 font-bold text-sm uppercase tracking-widest">Professional Design Studio</span>
            <h2 class="text-4xl sm:text-5xl font-display font-extrabold mt-3 mb-4 text-white">Create Your <span class="text-gradient">Masterpiece</span></h2>
        </div>
        
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            <!-- LEFT: PREVIEW -->
            <div class="lg:col-span-7">
                <div class="bg-gradient-to-b from-rustic-800 to-rustic-900 rounded-3xl p-8 shadow-2xl border border-white/10 min-h-[550px] flex items-center justify-center relative overflow-hidden">
                    <div class="absolute top-6 left-1/2 transform -translate-x-1/2 bg-rustic-900/80 backdrop-blur-md p-2 rounded-xl flex gap-2 border border-white/10 z-20">
                        <button class="product-tab active px-4 py-2 rounded-lg text-sm font-bold transition-all" data-product="tumbler">🥤 Tumbler</button>
                        <button class="product-tab px-4 py-2 rounded-lg text-sm font-bold transition-all" data-product="tshirt">👕 T-Shirt</button>
                        <button class="product-tab px-4 py-2 rounded-lg text-sm font-bold transition-all" data-product="tote">👜 Tote Bag</button>
                    </div>

                    <div id="preview-canvas" class="w-full h-[450px] flex items-center justify-center relative">
                        <!-- TUMBLER MOCKUP -->
                        <div id="mockup-tumbler" class="mockup-view hidden flex-col items-center justify-center">
                            <div class="relative w-48 h-80 group">
                                <div class="tumbler-body absolute inset-0 bg-white rounded-b-3xl overflow-hidden shadow-2xl transition-colors duration-500">
                                    <div class="tumbler-design absolute inset-0 flex items-center justify-center overflow-hidden bg-transparent">
                                        <img id="img-tumbler" src="" class="w-full h-full object-cover opacity-0 transition-opacity duration-300" alt="Design">
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent skew-x-12"></div>
                                </div>
                                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-44 h-8 bg-gray-300 rounded-t-lg shadow-md z-10"></div>
                            </div>
                        </div>

                        <!-- T-SHIRT MOCKUP (REALISTIC SVG) -->
                        <div id="mockup-tshirt" class="mockup-view hidden flex-col items-center justify-center">
                            <svg width="260" height="300" viewBox="0 0 260 300" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-2xl">
                                <path d="M70 20 L190 20 L210 60 L240 50 L220 110 L190 100 L190 280 L70 280 L70 100 L40 110 L20 50 L50 60 Z" fill="#ffffff" class="tshirt-body transition-colors duration-500"/>
                                <path d="M100 20 C110 40 150 40 160 20" stroke="#e5e7eb" stroke-width="4" fill="none"/>
                                <foreignObject x="85" y="110" width="90" height="120" class="tshirt-design">
                                    <div xmlns="http://www.w3.org/1999/xhtml" class="w-full h-full flex items-center justify-center overflow-hidden">
                                        <img id="img-tshirt" src="" class="w-full h-full object-contain opacity-0 transition-opacity duration-300 mix-blend-multiply"/>
                                    </div>
                                </foreignObject>
                            </svg>
                        </div>

                        <!-- TOTE BAG MOCKUP (REALISTIC SVG) -->
                        <div id="mockup-tote" class="mockup-view hidden flex-col items-center justify-center">
                            <svg width="220" height="280" viewBox="0 0 220 280" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-2xl">
                                <path d="M60 60 C60 20 160 20 160 60" stroke="#d97706" stroke-width="8" fill="none" stroke-linecap="round"/>
                                <rect x="30" y="70" width="160" height="200" rx="8" fill="#fef3c7" class="tote-body transition-colors duration-500"/>
                                <foreignObject x="60" y="110" width="100" height="100" class="tote-design">
                                    <div xmlns="http://www.w3.org/1999/xhtml" class="w-full h-full flex items-center justify-center overflow-hidden">
                                        <img id="img-tote" src="" class="w-full h-full object-contain opacity-0 transition-opacity duration-300 mix-blend-multiply"/>
                                    </div>
                                </foreignObject>
                            </svg>
                        </div>

                        <div id="empty-state" class="text-center text-white/30">
                            <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="font-bold text-lg">Upload an image to start</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <button onclick="document.getElementById('file-upload').click()" class="bg-blaze-500 hover:bg-blaze-600 text-white py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        📤 Upload Image
                    </button>
                    <input type="file" id="file-upload" class="hidden" accept="image/*">
                    
                    <div class="col-span-3 bg-white/5 rounded-xl p-3 flex items-center gap-4 border border-white/10">
                        <span class="text-sm font-bold text-white/70 whitespace-nowrap">Product Color:</span>
                        <div class="flex gap-2" id="color-swatches">
                            <button class="w-8 h-8 rounded-full bg-white border-2 border-white/20 hover:scale-110 transition-transform" onclick="setProductColor('#ffffff')"></button>
                            <button class="w-8 h-8 rounded-full bg-pink-500 border-2 border-white/20 hover:scale-110 transition-transform" onclick="setProductColor('#ec4899')"></button>
                            <button class="w-8 h-8 rounded-full bg-black border-2 border-white/20 hover:scale-110 transition-transform" onclick="setProductColor('#111827')"></button>
                            <button class="w-8 h-8 rounded-full bg-blue-600 border-2 border-white/20 hover:scale-110 transition-transform" onclick="setProductColor('#2563eb')"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: CONTROLS & PRICING -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white text-rustic-900 rounded-2xl p-6 shadow-xl border-t-4 border-blaze-500">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-display font-bold">Order Summary</h3>
                            <p class="text-sm text-rustic-500 mt-1" id="summary-subtitle">20oz Tumbler • Stock Design</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-rustic-400 uppercase font-bold">Estimated Total</div>
                            <div class="text-3xl font-extrabold text-blaze-500" id="live-total">$28.00</div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 mb-6 text-sm border-b border-rustic-100 pb-4">
                        <div class="flex justify-between">
                            <span class="text-rustic-500">Base Price (<span id="qty-display">1</span> items)</span>
                            <span class="font-bold" id="price-base">$28.00</span>
                        </div>
                        <div class="flex justify-between hidden" id="row-design">
                            <span class="text-rustic-500" id="label-design">Custom Design</span>
                            <span class="font-bold text-blaze-600" id="price-design">+$5.00</span>
                        </div>
                        <div id="list-addons" class="space-y-1"></div>
                        <div class="flex justify-between hidden text-green-600" id="row-discount">
                            <span>🎉 Bulk Discount</span>
                            <span class="font-bold" id="price-discount">-$0.00</span>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center justify-between bg-rustic-50 p-3 rounded-lg">
                            <span class="font-bold text-sm">Quantity</span>
                            <div class="flex items-center gap-3">
                                <button onclick="adjustQty(-1)" class="w-8 h-8 rounded bg-white shadow text-rustic-700 font-bold hover:bg-rustic-100">−</button>
                                <input type="number" id="input-qty" value="1" min="1" class="w-12 text-center font-bold bg-transparent focus:outline-none" onchange="calculateTotal()">
                                <button onclick="adjustQty(1)" class="w-8 h-8 rounded bg-white shadow text-rustic-700 font-bold hover:bg-rustic-100">+</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-rustic-500 mb-2 uppercase">Design Option</label>
                            <select id="select-design" onchange="calculateTotal()" class="w-full px-3 py-2 border-2 border-rustic-200 rounded-lg focus:border-blaze-500 focus:outline-none bg-white text-sm">
                                <option value="stock" data-cost="0">📂 Stock Design (Free)</option>
                                <option value="custom" data-cost="5">🎨 Custom Image (+$5)</option>
                                <option value="wrap" data-cost="12">🌐 Full 360° Wrap (+$12)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-rustic-200 hover:border-blaze-300 cursor-pointer transition-all">
                                <input type="checkbox" class="addon-cb accent-blaze-500" data-cost="3" onchange="calculateTotal()">
                                <span class="text-xs font-bold flex-1">✨ Glow Ink</span>
                                <span class="text-xs text-rustic-500">+$3</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-rustic-200 hover:border-blaze-300 cursor-pointer transition-all">
                                <input type="checkbox" class="addon-cb accent-blaze-500" data-cost="8" onchange="calculateTotal()">
                                <span class="text-xs font-bold flex-1">⚡ Rush 48hr</span>
                                <span class="text-xs text-rustic-500">+$8</span>
                            </label>
                        </div>
                    </div>

                    <button onclick="submitOrder()" class="btn-gradient text-white w-full py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                        📩 Send Order to Diana
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-24 bg-rustic-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 hidden-section" data-animation="fadeInUp">
            <span class="text-blaze-500 font-bold text-sm uppercase tracking-widest">Let's Make Something</span>
            <h2 class="text-4xl sm:text-5xl font-display font-extrabold mt-3 mb-4">Ready to <span class="text-gradient">Order?</span></h2>
        </div>
        <div class="grid lg:grid-cols-5 gap-12">
            <div class="lg:col-span-2 hidden-section" data-animation="fadeInLeft">
                <div class="space-y-6">
                    <a href="tel:9362078565" class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-md border border-rustic-100 hover:border-blaze-200 transition-all group">
                        <div class="w-12 h-12 bg-blaze-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blaze-100 transition-all">
                            <svg class="w-6 h-6 text-blaze-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-rustic-900 mb-1">Call or Text Diana</h4>
                            <p class="text-blaze-600 font-bold text-lg">(936) 207-8565</p>
                        </div>
                    </a>
                    <a href="mailto:Support@blaznprints.com" class="flex items-start gap-4 p-5 bg-white rounded-2xl shadow-md border border-rustic-100 hover:border-blaze-200 transition-all group">
                        <div class="w-12 h-12 bg-blaze-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blaze-100 transition-all">
                            <svg class="w-6 h-6 text-blaze-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-rustic-900 mb-1">Email</h4>
                            <p class="text-blaze-600 font-semibold text-sm">Support@blaznprints.com</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="lg:col-span-3 hidden-section" data-animation="fadeInRight">
                <form id="contactForm" onsubmit="handleFormSubmit(event, 'contact')" class="bg-white rounded-2xl p-8 shadow-lg border border-rustic-100">
                    <div class="grid sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-rustic-700 mb-2">Your Name *</label>
                            <input type="text" name="name" required class="form-input w-full px-4 py-3 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none transition-all text-sm" placeholder="Your name">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-rustic-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" class="form-input w-full px-4 py-3 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none transition-all text-sm" placeholder="(555) 123-4567">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-rustic-700 mb-2">Email *</label>
                        <input type="email" name="email" required class="form-input w-full px-4 py-3 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none transition-all text-sm" placeholder="your@email.com">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-rustic-700 mb-2">Tell Me About Your Project *</label>
                        <textarea name="message" required rows="5" class="form-input w-full px-4 py-3 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none transition-all text-sm resize-none" placeholder="What do you want? Colors? Design ideas? How many? When do you need it?"></textarea>
                    </div>
                    <button type="submit" id="submitBtn" class="btn-gradient text-white w-full py-4 rounded-xl font-bold text-base flex items-center justify-center gap-2">
                        <span id="submitText">Send It, Diana! 🔥</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Quote Modal -->
<div id="quoteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQuoteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-bounce-in">
            <div class="p-6 border-b border-rustic-100 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-display font-bold">Get a Free Quote 🔥</h3>
                    <p class="text-rustic-400 text-sm">I'll get back to you within 24 hours!</p>
                </div>
                <button onclick="closeQuoteModal()" class="w-10 h-10 rounded-xl hover:bg-rustic-100 flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="quoteForm" onsubmit="handleFormSubmit(event, 'checkout')" class="p-6 space-y-4">
                <input type="hidden" name="name" value="<?php echo isset($_SESSION['user_name']) ?   htmlspecialchars($_SESSION['user_name']) : ''; ?>">
<?php if(!isset($_SESSION['user_id'])): ?>
<div>
    <label class="block text-sm font-bold text-rustic-700 mb-1.5">Your Name *</label>
    <input type="text" name="name_visible" required class="form-input w-full px-4 py-2.5 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none text-sm" placeholder="Your name">
</div>
<?php endif; ?>
                                     <input type="hidden" name="product" id="modal-product">
                <input type="hidden" name="quantity" id="modal-quantity">
                <input type="hidden" name="total" id="modal-total">
                <input type="hidden" name="design" id="modal-design">
                <input type="hidden" name="addons" id="modal-addons">
                
                <div>
                    <label class="block text-sm font-bold text-rustic-700 mb-1.5">Email *</label>
                    <input type="email" name="email" required class="form-input w-full px-4 py-2.5 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none text-sm" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-bold text-rustic-700 mb-1.5">Describe Your Idea *</label>
                    <textarea name="message" required rows="4" class="form-input w-full px-4 py-2.5 rounded-xl border border-rustic-200 bg-rustic-50 focus:outline-none text-sm resize-none" placeholder="Colors, design ideas, occasion, when you need it..."></textarea>
                </div>
                <button type="submit" class="btn-gradient text-white w-full py-3.5 rounded-xl font-bold flex items-center justify-center gap-2">
                    Submit Quote Request 🔥
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-24 right-4 z-50 hidden">
    <div class="toast bg-white rounded-xl shadow-2xl p-4 flex items-center gap-3 max-w-sm border border-rustic-100">
        <div id="toastIcon" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-green-100">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div id="toastTitle" class="font-bold text-sm"></div>
            <div id="toastMessage" class="text-rustic-500 text-xs mt-0.5"></div>
        </div>
    </div>
</div>

<!-- Back to Top -->
<button id="backToTop" class="back-to-top hidden fixed bottom-6 right-6 z-40 w-12 h-12 btn-gradient rounded-full shadow-lg flex items-center justify-center text-white" onclick="window.scrollTo({top:0, behavior:'smooth'})">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
    // Global Login State
    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

    // Mobile Menu
    function toggleMobileMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
        document.getElementById('hamburgerBtn').classList.toggle('hamburger-active');
    }
    function closeMobileMenu() {
        document.getElementById('mobileMenu').classList.add('hidden');
        document.getElementById('hamburgerBtn').classList.remove('hamburger-active');
    }

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('bg-rustic-900/95', 'backdrop-blur-lg', 'shadow-lg');
        else navbar.classList.remove('bg-rustic-900/95', 'backdrop-blur-lg', 'shadow-lg');
        const backToTop = document.getElementById('backToTop');
        if (window.scrollY > 500) backToTop.classList.replace('hidden', 'visible');
        else backToTop.classList.replace('visible', 'hidden');
    });

    // Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('hidden-section');
                entry.target.classList.add('visible-section');
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.hidden-section').forEach(el => observer.observe(el));

    // Toast Notification
    function showToast(title, message) {
        const toast = document.getElementById('toast');
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMessage').textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    }

    // Universal Form Handler (Handles both Contact and Quote forms)
    async function handleFormSubmit(e, source) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true; btn.textContent = 'Sending...';
    const formData = new FormData(form);
    const endpoint = (source === 'checkout') ? 'checkout_action.php' : 'send_email.php';

    try {
        const response = await fetch(endpoint, { method: 'POST', body: formData });
        const text = await response.text();
        let result;
        try { result = JSON.parse(text); } catch { result = { status: 'error', message: 'Invalid server response' }; }

        if (result.status === 'success') {
            showToast('Success!', result.message);
            form.reset();
            if (source === 'checkout') closeQuoteModal();
        } else {
            showToast('Error', result.message || 'Something went wrong');
        }
    } catch (error) {
        console.error('Form submit error:', error);
        showToast('Error', 'Network error. Please call Diana at (936) 207-8565.');
    } finally {
        btn.disabled = false; btn.textContent = originalText;
    }
}
        // Choose endpoint based on source
        const endpoint = (source === 'checkout') ? 'checkout_action.php' : 'send_email.php';

        try {
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const result = await response.json();
            
            if (result.status === 'success') {
                showToast('Success!', result.message);
                form.reset();
                if (source === 'checkout') closeQuoteModal();
            } else {
                showToast('Error', result.message);
            }
        } catch (error) {
            showToast('Error', 'Network error. Please try again or call Diana.');
        } finally {
            btn.disabled = false; btn.textContent = originalText;
        }
    }

    // Modals
    function openQuoteModal() { 
        // MANDATORY LOGIN CHECK
        if (!isLoggedIn) { 
            window.location.href = 'login.php?return=' + encodeURIComponent(window.location.href); 
            return; 
        }
        document.getElementById('quoteModal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    function closeQuoteModal() { document.getElementById('quoteModal').classList.add('hidden'); document.body.style.overflow = ''; }
    
    function openLightbox(element) {
        const img = element.querySelector('img');
        const lb = document.createElement('div');
        lb.className = 'fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 animate-fade-in-up';
        lb.onclick = () => lb.remove();
        lb.innerHTML = `<img src="${img.src}" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl">`;
        document.body.appendChild(lb);
    }

    // ==========================================
    // DESIGN STUDIO & PRICING LOGIC
    // ==========================================
    let currentProduct = 'tumbler';
    let basePrices = <?php echo json_encode($prices); ?>;
    if (typeof basePrices !== 'object' || basePrices === null) basePrices = { 'tumbler': 28, 'tshirt': 22, 'tote': 18, 'mug': 15 };
    let productColors = { 'tumbler': '#ffffff', 'tshirt': '#ffffff', 'tote': '#fef3c7' };

    // Tab Switching
    document.querySelectorAll('.product-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.product-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentProduct = tab.dataset.product;
            updateProductView();
        });
    });

    function updateProductView() {
        document.querySelectorAll('.mockup-view').forEach(m => m.classList.add('hidden'));
        const el = document.getElementById(`mockup-${currentProduct}`);
        if(el) { el.classList.remove('hidden'); el.classList.add('flex'); }
        setProductColor(productColors[currentProduct]);
    }

    function adjustQty(change) {
        const input = document.getElementById('input-qty');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1; if (val > 500) val = 500;
        input.value = val;
        calculateTotal();
    }

    function calculateTotal() {
        const qty = Math.max(1, parseInt(document.getElementById('input-qty').value) || 1);
        const base = parseFloat(basePrices[currentProduct]) || 20;
        
        const select = document.getElementById('select-design');
        const designCost = parseFloat(select.options[select.selectedIndex].dataset.cost) || 0;
        const designLabel = select.options[select.selectedIndex].text;

        let addonTotal = 0;
        const addons = [];
        document.querySelectorAll('.addon-cb:checked').forEach(cb => {
            const cost = parseFloat(cb.dataset.cost) || 0;
            addonTotal += cost;
            addons.push({ name: cb.nextElementSibling.textContent.trim(), cost });
        });

        let discount = 0;
        if (qty >= 11) discount = 0.15; else if (qty >= 6) discount = 0.10; else if (qty >= 3) discount = 0.05;

        const subtotal = (base + designCost + addonTotal) * qty;
        const discountAmt = subtotal * discount;
        const total = subtotal - discountAmt;

        document.getElementById('qty-display').textContent = qty;
        document.getElementById('price-base').textContent = `$${(base * qty).toFixed(2)}`;
        document.getElementById('summary-subtitle').textContent = `${currentProduct.charAt(0).toUpperCase() + currentProduct.slice(1)} • ${designLabel}`;
        
        const designRow = document.getElementById('row-design');
        if (designCost > 0) {
            designRow.classList.remove('hidden');
            document.getElementById('label-design').textContent = designLabel.replace(/\(.*\)/,'').trim();
            document.getElementById('price-design').textContent = `+$${(designCost * qty).toFixed(2)}`;
        } else { designRow.classList.add('hidden'); }

        document.getElementById('list-addons').innerHTML = addons.map(a => 
            `<div class="flex justify-between text-rustic-500"><span>${a.name}</span><span class="font-bold">+$${(a.cost * qty).toFixed(2)}</span></div>`
        ).join('');

        const discRow = document.getElementById('row-discount');
        if (discountAmt > 0) {
            discRow.classList.remove('hidden');
            document.getElementById('price-discount').textContent = `-$${discountAmt.toFixed(2)}`;
        } else { discRow.classList.add('hidden'); }

        document.getElementById('live-total').textContent = `$${total.toFixed(2)}`;
    }

    // Image Upload
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('empty-state').classList.add('hidden');
                const img = document.getElementById(`img-${currentProduct}`);
                if(img) { img.src = evt.target.result; img.classList.remove('opacity-0'); }
                const select = document.getElementById('select-design');
                select.value = 'custom';
                calculateTotal();
            };
            reader.readAsDataURL(file);
        }
    });

    function setProductColor(color) {
        productColors[currentProduct] = color;
        if(currentProduct === 'tshirt') document.querySelectorAll('.tshirt-body').forEach(el => el.style.fill = color);
        else if(currentProduct === 'tote') document.querySelectorAll('.tote-body').forEach(el => el.style.fill = color);
        else document.querySelectorAll('.tumbler-body').forEach(el => el.style.backgroundColor = color);
    }

    function submitOrder() {
        // Gather all details
        const qty = document.getElementById('input-qty').value;
        const design = document.getElementById('select-design').options[document.getElementById('select-design').selectedIndex].text;
        const total = document.getElementById('live-total').textContent;
        const product = currentProduct;
        
        let addons = [];
        document.querySelectorAll('.addon-cb:checked').forEach(cb => {
            addons.push(cb.nextElementSibling.textContent.trim());
        });

        const orderDetails = `ORDER DETAILS:\nProduct: ${product}\nQuantity: ${qty}\nDesign Option: ${design}\nAdd-ons: ${addons.length > 0 ? addons.join(', ') : 'None'}\nEstimated Total: ${total}`;
        
        const nameVisible = document.querySelector('#quoteForm input[name="name_visible"]');
    if (nameVisible && !nameVisible.value) {
        nameVisible.value = "<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>";
    }
        // Open Quote Modal and pre-fill the message
        openQuoteModal();
        
        // Wait for modal to open, then fill form
        setTimeout(() => {
            const textarea = document.querySelector('#quoteForm textarea[name="message"]');
            if(textarea) {
                textarea.value = orderDetails + "\n\n(Note: Please upload your image via email or reply to this order confirmation if you have a custom design.)";
                textarea.focus();
            }
        }, 300);
    }

    // Hero Rotator
    const heroImages = ["blazn-pics/roses%20tumbler%20design.png", "blazn-pics/gnome%20with%20heart%20tumbler%20design.png", "blazn-pics/tote1.png", "blazn-pics/t-shirt1.png", "blazn-pics/winebag1.png"];
    let heroIdx = 0;
    const heroImg = document.getElementById('hero-main-img');
    if(heroImg) {
        setInterval(() => {
            heroImg.style.opacity = 0;
            setTimeout(() => {
                heroIdx = (heroIdx + 1) % heroImages.length;
                heroImg.src = heroImages[heroIdx];
                heroImg.style.opacity = 1;
            }, 400);
        }, 4500);
    }

    updateProductView();
    calculateTotal();
</script>
</body>
</html>