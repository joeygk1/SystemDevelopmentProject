<?php
require 'vendor/autoload.php'; // Make sure this path is correct
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Gmail credentials (replace with your real Gmail + App Password)
    $mail->Username = 'joeyayoubdisalvo@gmail.com';
    $mail->Password = 'ijcbgdekkqiokahg'; // App password from Google

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "SMTP DEBUG [$level]: $str\n";
    };

    // Recipients
    $mail->setFrom('joeyayoubdisalvo@gmail.com', 'Magic Sole');
    $mail->addAddress('joey.ayoubdisalvo@icloud.com', 'Test User'); // Replace with your test email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a test email sent using Gmail SMTP and PHPMailer.';

    $mail->send();
    echo "✅ Email has been sent successfully.";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
