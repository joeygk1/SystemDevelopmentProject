<?php
ob_start();
include_once "Models/Model.php";
$path = $_SERVER['SCRIPT_NAME'];

require 'vendor/autoload.php';
require 'config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable('config');
$dotenv->load();

$error = '';

// Debug: Log environment file existence
$env_path = __DIR__ . '/.env';
file_put_contents('debug.log', "login.php - Env file exists: " . (file_exists($env_path) ? 'Yes' : 'No') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Use $_ENV for Gmail credentials
$gmail_username = $_ENV['GMAIL_USERNAME'] ?? '';
$gmail_password = $_ENV['GMAIL_APP_PASSWORD'] ?? '';
file_put_contents('debug.log', "login.php - GMAIL_USERNAME: " . ($gmail_username ? 'Set' : 'Empty') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents('debug.log', "login.php - GMAIL_APP_PASSWORD: " . ($gmail_password ? 'Set' : 'Empty') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if (empty($gmail_username) || empty($gmail_password)) {
    $error = 'Gmail credentials are missing in .env file. Please contact the administrator.';
    file_put_contents('debug.log', "login.php - Error: Gmail credentials missing at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['reset-email'])) {
    // Debug: Log POST data
    file_put_contents('debug.log', "login.php - POST data: " . print_r($_POST, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // Step 1: Verify email and password
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
        file_put_contents('debug.log', "login.php - Error: Email or password empty at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
        file_put_contents('debug.log', "login.php - Error: Invalid email format at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    } else {
        try {
            $conn = Model::connect();
            $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Generate 6-digit OTP
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                $_SESSION['2fa_user_id'] = $user['id'];
                $_SESSION['2fa_otp'] = $otp;
                $_SESSION['2fa_email'] = $user['email'];
                $_SESSION['2fa_role'] = $user['role'];
                $_SESSION['2fa_expires'] = time() + 600; // 10 minutes
                $_SESSION['token'] = bin2hex(random_bytes(16)); // Session token

                // Debug: Log OTP
                file_put_contents('debug.log', "login.php - OTP generated: $otp for email: {$user['email']} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

                // Send OTP via Gmail SMTP
                if (!empty($gmail_username) && !empty($gmail_password)) {
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
                        $mail->Subject = 'Your Magic Sole 2FA Code';
                        $mail->Body = "Hello {$user['username']},<br><br>Your one-time code is: <b>$otp</b><br>This code expires in 10 minutes.";
                        $mail->AltBody = "Hello {$user['username']},\n\nYour one-time code is: $otp\nThis code expires in 10 minutes.";
                        $mail->send();
                        file_put_contents('debug.log', "login.php - Email sent successfully to {$user['email']} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

                        // Redirect to OTP verification page
                        $newURL = dirname($path) . '/admin/verify_otp';
                        header('Location: ' . $newURL);
                        exit;
                    } catch (Exception $e) {
                        $error = 'Failed to send OTP. Please try again or contact support.';
                        file_put_contents('debug.log', "login.php - PHPMailer Error: {$mail->ErrorInfo} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    }
                }
            } else {
                $error = 'Invalid email or password';
                file_put_contents('debug.log', "login.php - Error: Invalid email or password at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            }
        } catch (PDOException $e) {
            $error = 'Database error: Unable to process login';
            file_put_contents('debug.log', "login.php - Database error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login - Magic Sole</title>
    <style>
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
            min-height: 100vh;
        }
        header {
            background-color: #1a1a1a;
            color: white;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 250px;
            height: 95vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            animation: slideInLeft 1s ease-out;
        }
        .logo img {
            width: 120px;
            margin-bottom: 2rem;
        }
        nav {
            display: flex;
            overflow-y: auto;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }
        nav a {
            color: #e3e3e3;
            text-decoration: none;
            font-size: 1.4rem;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        nav a:hover {
            background: #f9c303;
            color: #1a1a1a;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 50px;
            position: relative;
        }
        .hero {
            background: linear-gradient(135deg, #d4af37, #f9c303);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            color: #1a1a1a;
            animation: fadeIn 1s ease-out;
            text-align: center;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }
        .login-section {
            padding: 30px;
            max-width: 500px;
            margin: 40px auto;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.5s;
        }
        .login-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
            color: #1a1a1a;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .login-form label {
            font-size: 1.1rem;
            color: #333;
        }
        .login-form input {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }
        .login-form input:focus {
            border: 1px solid #d4af37;
        }
        .login-form button {
            background: #1a1a1a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        .login-form button:hover {
            background: #333;
            transform: scale(1.05);
        }
        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }
        .forgot-password a {
            color: #d4af37;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            text-align: center;
            margin-top: 10px;
            display: none;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s forwards;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
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
        .modal-content .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            color: #1a1a1a;
            cursor: pointer;
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
        .modal-success-message, .modal-error-message {
            margin-top: 10px;
            font-size: 0.9rem;
            text-align: center;
            display: none;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s forwards;
        }
        .modal-success-message {
            color: #2ecc71;
        }
        .modal-error-message {
            color: #e74c3c;
        }
        footer {
            font-size: 0.9rem;
            color: white;
            text-align: center;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 250px;
            background-color: #1a1a1a;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        .bottom-logo {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .bottom-logo img {
            width: 200px;
            max-width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s forwards 0.5s;
        }
        .bottom-logo img:hover {
            transform: scale(1.1);
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
            header {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }
            nav {
                flex-direction: row;
                justify-content: center;
                gap: 15px;
                flex-wrap: wrap;
            }
            footer {
                position: relative;
                width: 100%;
                left: 0;
            }
            .bottom-logo {
                margin-top: 20px;
            }
        }
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes modalFadeIn {
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="<?php echo dirname($path); ?>/client/home">
            <img src="<?php echo dirname($path); ?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path); ?>/client/home">Home</a>
        <a href="<?php echo dirname($path); ?>/client/services">Services</a>
        <a href="<?php echo dirname($path); ?>/client/about">About</a>
        <a href="<?php echo dirname($path); ?>/client/policies">Policies</a>
        <a href="<?php echo dirname($path); ?>/booking/booking">Booking</a>
        <a href="<?php echo dirname($path); ?>/client/gallery">Gallery</a>
        <?php if (!isset($_SESSION['token'])) { ?>
            <a href="<?php echo dirname($path); ?>/client/login">Login</a>
            <a href="<?php echo dirname($path); ?>/client/register">Register</a>
        <?php } else { ?>
            <a href="<?php echo dirname($path); ?>/client/client-orders">Orders</a>
            <a href="<?php echo dirname($path); ?>/client/logout">Logout</a>
        <?php } ?>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>
<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Login</h1>
            <p>Access your account to manage bookings and more!</p>
        </div>
    </section>
    <section class="login-section">
        <h2>Login to Your Account</h2>
        <form class="login-form" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
            <div class="forgot-password">
                <a href="#" onclick="openForgotPasswordModal()">Forgot Password?</a>
            </div>
        </form>
        <?php if ($error) { ?>
            <p class="error-message" style="display: block;"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
    </section>
    <div class="modal" id="forgot-password-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeForgotPasswordModal()">×</span>
            <h3>Forgot Password</h3>
            <p>Enter your email address to receive a password reset link.</p>
            <form id="forgot-password-form">
                <input type="email" id="reset-email" name="reset-email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p class="modal-success-message" id="success-message">A password reset link has been sent to your email.</p>
            <p class="modal-error-message" id="error-message"></p>
        </div>
    </div>
    <div class="bottom-logo">
        <img src="<?php echo dirname($path); ?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
    </div>
</div>
<script>
    // Redirect if already logged in
    <?php if (isset($_SESSION['token'])) { ?>
        window.location.href = '<?php echo dirname($path); ?>/client/client-orders';
    <?php } ?>

    // Forgot Password Modal functionality
    function openForgotPasswordModal() {
        document.getElementById('forgot-password-modal').style.display = 'flex';
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';
        document.getElementById('reset-email').value = '';
        document.body.style.overflow = 'hidden';
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgot-password-modal').style.display = 'none';
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';
        document.getElementById('reset-email').value = '';
        document.body.style.overflow = 'auto';
    }

    document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const email = document.getElementById('reset-email').value;
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errorMessage.textContent = 'Please enter a valid email address';
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
            return;
        }

        fetch('/MagicSoleProject/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'reset-email=' + encodeURIComponent(email)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}, Text: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';
                document.getElementById('reset-email').value = '';
                setTimeout(closeForgotPasswordModal, 2000);
            } else {
                errorMessage.textContent = data.error || 'Failed to send reset link';
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            errorMessage.textContent = `An error occurred: ${error.message}. Please try again.`;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
            // Log to console
            console.log('Error:', 'login.php - Fetch error: ' + error.message);
        });
    });
</script>
</body>
</html>