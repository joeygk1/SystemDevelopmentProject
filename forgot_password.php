<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize response array
$response = ['success' => false, 'error' => ''];

// Set content type to JSON
header('Content-Type: application/json');

// Debug: Log environment variables and POST data
$env_path = __DIR__ . '/.env';
file_put_contents('debug.log', "Env file exists (forgot_password.php): " . (file_exists($env_path) ? 'Yes' : 'No') . "\n", FILE_APPEND);
file_put_contents('debug.log', "POST data (forgot_password.php): " . print_r($_POST, true) . "\n", FILE_APPEND);

// Get Gmail credentials from environment variables
$gmail_username = $_ENV['GMAIL_USERNAME'] ?? '';
$gmail_password = $_ENV['GMAIL_APP_PASSWORD'] ?? '';
file_put_contents('debug.log', "GMAIL_USERNAME (forgot_password.php): $gmail_username\n", FILE_APPEND);
file_put_contents('debug.log', "GMAIL_APP_PASSWORD (forgot_password.php): $gmail_password\n", FILE_APPEND);

if (empty($gmail_username) || empty($gmail_password)) {
    $response['error'] = 'Gmail credentials are missing in .env file. Please contact the administrator.';
    file_put_contents('debug.log', "Error: Gmail credentials missing (forgot_password.php)\n", FILE_APPEND);
    echo json_encode($response);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['reset-email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validate email
    if (empty($email)) {
        $response['error'] = 'Email is required';
        file_put_contents('debug.log', "Error: Email empty (forgot_password.php)\n", FILE_APPEND);
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Invalid email format';
        file_put_contents('debug.log', "Error: Invalid email format (forgot_password.php)\n", FILE_APPEND);
        echo json_encode($response);
        exit;
    }

    try {
        // Check if email exists in users table
        $stmt = $db->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $response['error'] = 'Email not found';
            file_put_contents('debug.log', "Error: Email not found (forgot_password.php): $email\n", FILE_APPEND);
            echo json_encode($response);
            exit;
        }

        // Generate a secure reset token
        $token = bin2hex(random_bytes(32)); // 64-character hex string
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Store the token in the password_resets table
        $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);
        file_put_contents('debug.log', "Reset token generated (forgot_password.php): $token for email: $email\n", FILE_APPEND);

        // Create the reset link
        $reset_link = "http://localhost/SystemDevelopmentProject/reset_password.php?token=" . urlencode($token);
        file_put_contents('debug.log', "Reset link (forgot_password.php): $reset_link\n", FILE_APPEND);

        // Send the reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $gmail_username;
            $mail->Password = $gmail_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom($gmail_username, 'Magic Sole');
            $mail->addAddress($user['email']);
            $mail->isHTML(true);
            $mail->Subject = 'Magic Sole Password Reset';
            $mail->Body = "Hello {$user['username']},<br><br>You requested a password reset for your Magic Sole account.<br>Click the link below to reset your password:<br><a href='$reset_link'>Reset Password</a><br><br>This link will expire in 1 hour.<br>If you did not request this, please ignore this email.";
            $mail->AltBody = "Hello {$user['username']},\n\nYou requested a password reset for your Magic Sole account.\nClick the link below to reset your password:\n$reset_link\n\nThis link will expire in 1 hour.\nIf you did not request this, please ignore this email.";
            $mail->send();
            file_put_contents('debug.log', "Password reset email sent successfully to {$user['email']}\n", FILE_APPEND);

            $response['success'] = true;
        } catch (Exception $e) {
            $response['error'] = 'Failed to send reset email. Please try again or contact support.';
            file_put_contents('debug.log', "PHPMailer Error (forgot_password.php): {$mail->ErrorInfo}\n", FILE_APPEND);
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
        file_put_contents('debug.log', "Database error (forgot_password.php): " . $e->getMessage() . "\n", FILE_APPEND);
    }
} else {
    $response['error'] = 'Invalid request method';
    file_put_contents('debug.log', "Error: Invalid request method (forgot_password.php)\n", FILE_APPEND);
}

echo json_encode($response);
?>