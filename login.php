<?php 
session_start();
require 'config/db.php'; 
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: ' . ($_GET['return'] ?? 'index.php')); exit;
        } else { $error = 'Invalid email or password.'; }
    } else { $error = 'Database connection unavailable.'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | BlaznPrints</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/playfair-display@5/700.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/lato@5/400.css">
<script>tailwind.config = { theme: { extend: { fontFamily: { display: ['Playfair Display', 'serif'], sans: ['Lato', 'sans-serif'] }, colors: { blaze: { 500: '#f97316', 600: '#ea580c' }, rustic: { 50: '#fafaf9', 900: '#1c1917' } } } } }</script>
<style>
/* Nuclear CSS Reset for Login Button */
button[type="submit"] {
    position: relative !important;
    z-index: 9999 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    transform: none !important;
    color: white !important;
    background: #f97316 !important;
}
button[type="submit"]:hover { background: #ea580c !important; }
</style>
</head>
<body class="bg-rustic-50 min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <div class="text-center mb-6">
        <a href="index.php" class="text-2xl font-display font-bold text-rustic-900">Blazn<span class="text-blaze-500">Prints</span></a>
        <h2 class="text-2xl font-display font-bold mt-4">Welcome Back 🤠</h2>
        <p class="text-rustic-500 text-sm mt-1">Sign in to place orders</p>
    </div>
    <?php if($error): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-bold text-rustic-700 mb-1">Email *</label>
            <input type="email" name="email" required class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="your@email.com">
        </div>
        <div>
            <label class="block text-sm font-bold text-rustic-700 mb-1">Password *</label>
            <input type="password" name="password" required class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="••••••••">
        </div>
        <!-- Nuclear Button -->
        <button type="submit" class="w-full bg-blaze-500 text-white py-3 rounded-lg font-bold hover:bg-blaze-600 transition">Login</button>
    </form>
    <p class="text-center mt-6 text-sm text-gray-500">New here? <a href="register.php" class="text-blaze-500 font-bold hover:underline">Create Account</a></p>
    <p class="text-center mt-2 text-xs"><a href="index.php" class="text-gray-400 hover:text-gray-600">← Back to Home</a></p>
</div>
<script>
// Debug: Log if button is rendered
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.querySelector('button[type="submit"]');
    if(btn) {
        console.log('✅ Login button found:', btn);
        console.log('✅ Computed styles:', window.getComputedStyle(btn));
    } else {
        console.error('❌ Login button NOT found in DOM');
    }
});
</script>
</body>
</html>