<?php
$to = 'Support@blaznprints.com';
$subject = 'Test Email from BlaznPrints';
$message = 'If you receive this, PHP mail() is working!';
$headers = 'From: no-reply@blaznprints.com' . "\r\n" .
           'Reply-To: no-reply@blaznprints.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Email sent successfully!";
} else {
    echo "❌ Failed to send email. Check IONOS mail settings or use SMTP.";
}
?>