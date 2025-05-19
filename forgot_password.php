<?php
session_start();
require 'vendor/autoload.php';
require 'config/config.php';
require 'Models/Model.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable('config');
$dotenv->load();

// Initialize response array
$response = ['success' => false, 'error' => ''];

// Set content type to JSON
header('Content-Type: application/json');

// Debug: Log environment variables and POST data
$env_path = __DIR__ . '/config/.env';
file_put_contents('debug.log', "forgot_password.php - Env file exists: " . (file_exists($env_path) ? 'Yes' : 'No') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents('debug.log', "forgot_password.php - POST data: " . print_r($_POST, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Get Gmail credentials from environment variables
$gmail_username = $_ENV['GMAIL_USERNAME'] ?? '';
$gmail_password = $_ENV['GMAIL_APP_PASSWORD'] ?? '';
file_put_contents('debug.log', "forgot_password.php - GMAIL_USERNAME: " . ($gmail_username ? 'Set' : 'Empty') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents('debug.log', "forgot_password.php - GMAIL_APP_PASSWORD: " . ($gmail_password ? 'Set' : 'Empty') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if (empty($gmail_username) || empty($gmail_password)) {
    $response['error'] = 'Gmail credentials are missing in .env file. Please contact the administrator.';
    file_put_contents('debug.log', "forgot_password.php - Error: Gmail credentials missing at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode($response);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['reset-email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validate email
    if (empty($email)) {
        $response['error'] = 'Email is required';
        file_put_contents('debug.log', "forgot_password.php - Error: Email empty at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Invalid email format';
        file_put_contents('debug.log', "forgot_password.php - Error: Invalid email format at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        echo json_encode($response);
        exit;
    }

    try {
        // Check if email exists in users table
        $conn = Model::connect();
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $response['error'] = 'Email not found';
            file_put_contents('debug.log', "forgot_password.php - Error: Email not found: $email at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            echo json_encode($response);
            exit;
        }

        // Generate a secure reset token
        $token = bin2hex(random_bytes(32)); // 64-character hex string
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Store the token in the password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = ?, expires_at = ?");
        $stmt->execute([$email, $token, $expires_at, $token, $expires_at]);
        file_put_contents('debug.log', "forgot_password.php - Reset token generated: $token for email: $email at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        // Create the reset link
        $base_url = 'http://localhost/MagicSoleProject/';
        $reset_link = $base_url . "reset_password.php?token=" . urlencode($token);
        file_put_contents('debug.log', "forgot_password.php - Reset link: $reset_link at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

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
            file_put_contents('debug.log', "forgot_password.php - Password reset email sent successfully to {$user['email']} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

            $response['success'] = true;
        } catch (Exception $e) {
            $response['error'] = 'Failed to send reset email. Please try again or contact support.';
            file_put_contents('debug.log', "forgot_password.php - PHPMailer Error: {$mail->ErrorInfo} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        }
    } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
        file_put_contents('debug.log', "forgot_password.php - Database error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    }
} else {
    $response['error'] = 'Invalid request method';
    file_put_contents('debug.log', "forgot_password.php - Error: Invalid request method at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
}

echo json_encode($response);
?>