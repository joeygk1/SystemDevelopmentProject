<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['reset-email'];
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $expires]);

        // Send reset email
        $reset_link = "http://localhost/SystemDevelopmentProject/reset_password.php?token=$token";
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_gmail@gmail.com';
            $mail->Password = 'your_app_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('your_gmail@gmail.com', 'Magic Sole');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a><br>This link expires in 1 hour.";
            $mail->send();

            $success = 'A password reset link has been sent to your email.';
        } catch (Exception $e) {
            $error = "Failed to send reset link: {$mail->ErrorInfo}";
        }
    } else {
        $error = 'No account found with that email.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Magic Sole</title>
    <style>
        /* Reuse login.php styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            position: relative;
            opacity: 0;
            transform: scale(0.8);
            animation: modalFadeIn 0.3s forwards;
        }

        .modal-content h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #1a1a1a;
            text-align: center;
        }

        .modal-content p {
            font-size: 1rem;
            margin-bottom: 15px;
            color: #555;
            text-align: center;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-content input {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .modal-content input:focus {
            border: 1px solid #d4af37;
        }

        .modal-content button {
            background: #f9c303;
            color: #1a1a1a;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #d4af37;
        }

        .error-message, .success-message {
            font-size: 0.9rem;
            text-align: center;
            margin-top: 10px;
            display: none;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s forwards;
        }

        .error-message {
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
        }

        @keyframes modalFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="modal-content">
        <h3>Forgot Password</h3>
        <p>Enter your email address to receive a password reset link.</p>
        <form method="POST">
            <input type="email" id="reset-email" name="reset-email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <?php if ($error) { ?>
            <p class="error-message" style="display: block;"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <?php if ($success) { ?>
            <p class="success-message" style="display: block;"><?php echo htmlspecialchars($success); ?></p>
        <?php } ?>
    </div>
</body>
</html>