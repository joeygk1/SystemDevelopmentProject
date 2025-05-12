<?php
session_start();
require 'config.php';

// Initialize variables
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Invalid or missing token.';
}

// Validate token and check if it exists and is not expired
if (!$error) {
    try {
        $stmt = $db->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            $error = 'Invalid token.';
        } elseif (strtotime($reset['expires_at']) < time()) {
            $error = 'This password reset link has expired.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Both password fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        try {
            // Update the user's password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $reset['email']]);

            // Delete the reset token
            $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $success = 'Your password has been reset successfully. You can now <a href="login.php">log in</a> with your new password.';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Magic Sole</title>
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
            height: 100vh;
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

        .reset-section {
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

        .reset-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
            color: #1a1a1a;
        }

        .reset-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .reset-form label {
            font-size: 1.1rem;
            color: #333;
        }

        .reset-form input {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .reset-form input:focus {
            border: 1px solid #d4af37;
        }

        .reset-form button {
            background: #1a1a1a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .reset-form button:hover {
            background: #333;
            transform: scale(1.05);
        }

        .error-message, .success-message {
            font-size: 0.9rem;
            text-align: center;
            margin-top: 10px;
        }

        .error-message {
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
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
            }

            footer {
                position: relative;
                width: 100%;
                left: 0;
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
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="index.html">
            <img src="MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="index.html">Home</a>
        <a href="services.html">Services</a>
        <a href="about.html">About Us</a>
        <a href="policies.html">Policies</a>
        <a href="booking.html">Booking</a>
        <a href="gallery.html">Gallery</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Reset Password</h1>
        </div>
    </section>

    <section class="reset-section">
        <h2>Reset Your Password</h2>
        <?php if ($error) { ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php } elseif ($success) { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } else { ?>
            <form class="reset-form" method="POST">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php } ?>
    </section>
</div>
</body>
</html>