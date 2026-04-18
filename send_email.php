<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

try {
    $name = trim($_POST['name'] ?? $_POST['name_visible'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Name, Email, and Message are required.']);
        exit;
    }

    $to = 'Support@blaznprints.com';
    $subject = "New Inquiry from $name";
    $body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
    $headers = "From: no-reply@blaznprints.com\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();

    $sent = @mail($to, $subject, $body, $headers);

    if ($sent) {
        echo json_encode(['status' => 'success', 'message' => "Thanks $name! Diana will reply to $email within 24 hours."]);
    } else {
        error_log("Mail failed for $email");
        echo json_encode(['status' => 'success', 'message' => "Message received! Please call Diana at (936) 207-8565 if you don't hear back."]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error. Please try again later.']);
}
?>