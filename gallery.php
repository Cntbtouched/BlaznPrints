<?php 
$galleryPath = __DIR__ . '/admin/data/gallery.json';
$galleryItems = file_exists($galleryPath) ? json_decode(file_get_contents($galleryPath), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Full Gallery | BlaznPrints</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/playfair-display@5/700.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/lato@5/400.css">
<script>tailwind.config = { theme: { extend: { fontFamily: { display: ['Playfair Display', 'serif'], sans: ['Lato', 'sans-serif'] }, colors: { blaze: { 500: '#f97316' }, rustic: { 50: '#fafaf9', 900: '#1c1917' } } } } }</script>
<style>body{font-family:'Lato',sans-serif} .gallery-item{transition:all .3s ease} .gallery-item:hover{transform:scale(1.03);box-shadow:0 20px 40px rgba(0,0,0,0.2)} .active{background:#f97316;color:white}</style>
</head>
<body class="bg-rustic-50">
<nav class="bg-rustic-900 text-white px-6 py-4 flex justify-between items-center">
    <a href="index.php" class="text-xl font-display font-bold">Blazn<span class="text-blaze-500">Prints</span></a>
    <a href="index.php" class="text-sm hover:text-blaze-500">← Back to Home</a>
</nav>
<section class="py-16 max-w-7xl mx-auto px-4">
    <h1 class="text-4xl font-display font-bold text-center mb-8">Full <span class="text-blaze-500">Gallery</span></h1>
    <div class="flex flex-wrap justify-center gap-3 mb-10">
        <button onclick="filterG('all')" class="gallery-btn active px-5 py-2 rounded-full text-sm font-bold transition bg-blaze-500 text-white">All</button>
        <button onclick="filterG('tumblers')" class="gallery-btn px-5 py-2 rounded-full text-sm font-bold bg-white text-rustic-700 transition">Tumblers</button>
        <button onclick="filterG('tees')" class="gallery-btn px-5 py-2 rounded-full text-sm font-bold bg-white text-rustic-700 transition">T-Shirts</button>
        <button onclick="filterG('totes')" class="gallery-btn px-5 py-2 rounded-full text-sm font-bold bg-white text-rustic-700 transition">Tote Bags</button>
        <button onclick="filterG('winebags')" class="gallery-btn px-5 py-2 rounded-full text-sm font-bold bg-white text-rustic-700 transition">Wine Bags</button>
    </div>
    <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6" id="galleryGrid">
        <?php if(!empty($galleryItems)): foreach($galleryItems as $item): ?>
        <div class="gallery-item rounded-2xl overflow-hidden shadow-lg cursor-pointer break-inside-avoid" data-cat="<?=$item['category']?>" onclick="openLB(this)">
            <img src="blazn-pics/<?=$item['file']?>" alt="BlaznPrints Work" class="w-full h-auto object-cover">
        </div>
        <?php endforeach; else: ?>
            <p class="text-center col-span-3 text-gray-500">No images uploaded yet. Log in to Admin Dashboard to add some!</p>
        <?php endif; ?>
    </div>
</section>
<div id="lb" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
    <img id="lbImg" src="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl">
</div>
<script>
function filterG(c){
    document.querySelectorAll('.gallery-btn').forEach(b=>{b.classList.remove('active','bg-blaze-500','text-white');b.classList.add('bg-white','text-rustic-700')});
    event.target.classList.add('active','bg-blaze-500','text-white');event.target.classList.remove('bg-white','text-rustic-700');
    document.querySelectorAll('[data-cat]').forEach(i=>{i.style.display=(c==='all'||i.dataset.cat===c)?'block':'none'});
}
function openLB(el){document.getElementById('lbImg').src=el.querySelector('img').src;document.getElementById('lb').classList.remove('hidden')}
</script>
</body>
</html>

