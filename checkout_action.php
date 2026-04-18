<?php
ob_start();
header('Content-Type: application/json');
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to place an order.']);
    exit;
}

try {
    $name = trim($_POST['name'] ?? $_POST['name_visible'] ?? $_SESSION['user_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $product = trim($_POST['product'] ?? 'custom');
    $qty = intval($_POST['quantity'] ?? 1);
    $total = floatval($_POST['total'] ?? 0);
    $design = trim($_POST['design'] ?? '');
    $addons = trim($_POST['addons'] ?? '');
    $notes = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Name and Email are required.']);
        exit;
    }

    $is_first = 1;
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? OR email = ?");
        $stmt->execute([$_SESSION['user_id'], $email]);
        $is_first = ($stmt->fetchColumn() == 0) ? 1 : 0;
    }

    $to = 'Support@blaznprints.com';
    $subject = "New Order #" . time() . ($is_first ? " (🆕 FIRST ORDER)" : "");
    $body = "NEW ORDER DETAILS\n=================\nCustomer: $name\nEmail: $email\nPhone: $phone\nFirst Order: " . ($is_first ? "YES" : "NO") . "\n\nProduct: $product\nQuantity: $qty\nTotal: $$total\nDesign: $design\nAdd-ons: $addons\n\nNotes:\n$notes";
    $headers = "From: no-reply@blaznprints.com\r\nReply-To: $email";

    $sent = @mail($to, $subject, $body, $headers);

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, email, phone, shipping_name, total, is_first_order, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $email, $phone, $name, $total, $is_first, $notes]);
        } catch(Exception $e) { error_log("DB insert failed: " . $e->getMessage()); }
    }

    if ($sent) {
        echo json_encode(['status' => 'success', 'message' => "Order placed! Diana will review it shortly."]);
    } else {
        echo json_encode(['status' => 'success', 'message' => "Order received! Please call Diana at (936) 207-8565 to confirm."]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error. Please try again or call Diana.']);
}
?>