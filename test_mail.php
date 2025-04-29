<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 1025;
    $mail->SMTPAuth = false;
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        file_put_contents('mail_debug.log', "SMTP Debug [$level]: $str\n", FILE_APPEND);
    };
    $mail->setFrom('noreply@magicsole.com', 'Magic Sole');
    $mail->addAddress('client@example.com');
    $mail->isHTML(true);
    $mail->Subject = 'Test OTP Email';
    $mail->Body = 'This is a test OTP email.';
    $mail->send();
    echo 'Email sent successfully';
} catch (Exception $e) {
    echo "Failed to send email: {$mail->ErrorInfo}";
    file_put_contents('mail_debug.log', "PHPMailer Error: {$mail->ErrorInfo}\n", FILE_APPEND);
}
?>