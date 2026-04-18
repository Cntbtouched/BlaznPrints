<?php 
session_start();
require 'config/db.php'; 

// Redirect if already logged in
if (isset($_SESSION['user_id'])) { 
    header('Location: index.php'); 
    exit; 
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    
    if ($pdo) {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered. Try logging in instead.';
        } else {
            // Hash password and insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
            try {
                $stmt->execute([$name, $email, $phone, $hash]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: ' . ($_GET['return'] ?? 'index.php')); 
                exit;
            } catch(PDOException $e) { 
                $error = 'Registration failed. Please try again.'; 
            }
        }
    } else {
        $error = 'Database connection unavailable. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | BlaznPrints</title>
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
</head>
<body class="bg-rustic-50 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-6">
            <a href="index.php" class="text-2xl font-display font-bold text-rustic-900">Blazn<span class="text-blaze-500">Prints</span></a>
            <h2 class="text-2xl font-display font-bold mt-4">Join the Herd 🐂</h2>
            <p class="text-rustic-500 text-sm mt-1">Create an account to order custom prints</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-rustic-700 mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="Diana Jordan">
            </div>
            <div>
                <label class="block text-sm font-bold text-rustic-700 mb-1">Email *</label>
                <input type="email" name="email" required class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="your@email.com">
            </div>
            <div>
                <label class="block text-sm font-bold text-rustic-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="(936) 207-8565">
            </div>
            <div>
                <label class="block text-sm font-bold text-rustic-700 mb-1">Password *</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-rustic-200 rounded-lg focus:outline-none focus:border-blaze-500" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-blaze-500 text-white py-3 rounded-lg font-bold hover:bg-blaze-600 transition" style="position:relative;z-index:999!important;display:block!important;opacity:1!important">Create Account</button>
        </form>
        
        <p class="text-center mt-6 text-sm text-gray-500">
            Already have an account? <a href="login.php" class="text-blaze-500 font-bold hover:underline">Login</a>
        </p>
        <p class="text-center mt-2 text-xs">
            <a href="index.php" class="text-gray-400 hover:text-gray-600">← Back to Home</a>
        </p>
    </div>
</body>
</html>