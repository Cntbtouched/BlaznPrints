<?php
require_once 'config.php';

$galleryDir = realpath(GALLERY_DIR);
$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$scanned = scandir($galleryDir);
$added = 0;

foreach ($scanned as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed) && !str_contains($file, '.DS_Store')) {
        // Check if already in gallery
        $exists = false;
        foreach ($galleryItems as $item) {
            if ($item['file'] === $file) { $exists = true; break; }
        }
        if (!$exists) {
            $galleryItems[] = [
                'file' => $file,
                'category' => 'tumblers', // Default category, you can change in dashboard
                'date' => date('Y-m-d')
            ];
            $added++;
        }
    }
}

file_put_contents(DATA_DIR . 'gallery.json', json_encode($galleryItems, JSON_PRETTY_PRINT));
echo "<h2>✅ Import Complete!</h2><p>Added <strong>$added</strong> images to the gallery.</p>";
echo "<p>⚠️ <strong>IMPORTANT:</strong> Delete this `import.php` file from your server now.</p>";
echo "<a href='dashboard.php'>← Back to Dashboard</a>";
?>