<?php
$path = $_SERVER['SCRIPT_NAME'];


$error = '';

if (!isset($_SESSION['2fa_user_id']) || !isset($_SESSION['2fa_otp'])) {
    header('Location:'.dirname($path).'/client/login');
    exit;
}

$email = $_SESSION['2fa_email'] ?? 'your email';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

    $otp = trim($_POST['otp'] ?? '');
    file_put_contents('debug.log', "OTP entered: $otp\n", FILE_APPEND);

    if (empty($otp)) {
        $error = 'OTP is required';
        file_put_contents('debug.log', "Error: OTP empty\n", FILE_APPEND);
    } elseif (time() > $_SESSION['2fa_expires']) {
        $error = 'OTP has expired. Please try again.';
        unset($_SESSION['2fa_user_id'], $_SESSION['2fa_otp'], $_SESSION['2fa_email'], $_SESSION['2fa_role'], $_SESSION['2fa_expires']);
        file_put_contents('debug.log', "Error: OTP expired\n", FILE_APPEND);
        header('Location:'.dirname($path).'/client/login');
        exit;
    } elseif ($otp === $_SESSION['2fa_otp']) {
        // Successful login
        $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
        $_SESSION['role'] = $_SESSION['2fa_role'];
        $email = $_SESSION['2fa_email'];

        $isAdmin = ($_SESSION['role'] === 'admin');
        $clientEmail = ($isAdmin ? '' : $email);

        // Clear 2FA session data
        unset($_SESSION['2fa_user_id'], $_SESSION['2fa_otp'], $_SESSION['2fa_email'], $_SESSION['2fa_role'], $_SESSION['2fa_expires']);
        file_put_contents('debug.log', "Login successful for $email\n", FILE_APPEND);

        $redirectUrl = $isAdmin ? 'admin-home' : 'index';
        $isAdminJs = $isAdmin ? 'true' : 'false';

        echo <<<EOD
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirecting...</title>
        </head>
        <body>
            <script>
                const isAdmin =  $isAdminJs;
                const clientEmail = '$clientEmail';

                if (isAdmin) {
                    localStorage.setItem('isAdmin', 'true');
                    localStorage.removeItem('clientEmail');
                } else {
                    localStorage.setItem('clientEmail', clientEmail);
                    localStorage.removeItem('isAdmin');
                }

                window.location.href = '$redirectUrl';
            </script>
        </body>
        </html>
        EOD;
        exit;
    } else {
        $error = 'Invalid OTP';
        file_put_contents('debug.log', "Error: Invalid OTP\n", FILE_APPEND);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Magic Sole</title>
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

        .otp-section {
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

        .otp-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
            color: #1a1a1a;
        }

        .otp-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .otp-form label {
            font-size: 1.1rem;
            color: #333;
        }

        .otp-form p {
            font-size: 1rem;
            color: #555;
            text-align: center;
        }

        .otp-form input {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .otp-form input:focus {
            border: 1px solid #d4af37;
        }

        .otp-form button {
            background: #1a1a1a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .otp-form button:hover {
            background: #333;
            transform: scale(1.05);
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
        <a href="<?php echo dirname($path);?>/client/home">
            <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path);?>/client/home"> Home</a>
        <a href="<?php echo dirname($path);?>/client/services">Services</a>
        <a href="<?php echo dirname($path);?>/client/about">About</a>
        <a href="<?php echo dirname($path);?>/client/policies">Policies</a>
        <a href="<?php echo dirname($path);?>/booking/booking">Booking</a>
        <a href="<?php echo dirname($path);?>/client/gallery">Gallery</a>
        <?php
        if($_SESSION['token'] == null){
            ?>
            <a href="<?php echo dirname($path);?>/client/login">Login</a>
            <?php
        }
        else{
            ?>
            <a href="<?php echo dirname($path);?>/client/client-orders">Orders</a>
            <?php
        }
        ?>
        <a href="#" id="logout-link" style="display: none;" onclick="logout()">Logout</a>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Verify OTP</h1>
            <p>Enter the code sent to your email to log in.</p>
        </div>
    </section>

    <section class="otp-section">
        <h2>Enter Your OTP</h2>
        <form class="otp-form" method="POST">
            <label for="otp">OTP Code</label>
            <p>We’ve sent a code to <?php echo htmlspecialchars($email); ?>. It expires in 10 minutes.</p>
            <input type="text" id="otp" name="otp" placeholder="Enter the 6-digit code" required>
            <button type="submit">Verify OTP</button>
        </form>
        <?php if ($error) { ?>
            <p class="error-message" style="display: block;"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
    </section>

    <div class="bottom-logo">
        <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
    </div>
</div>

<script>
    // Check if a user is already logged in
    const isAdmin = localStorage.getItem('isAdmin') === 'true';
    const clientEmail = localStorage.getItem('clientEmail');
    const loginLink = document.getElementById('login-link');
    const logoutLink = document.getElementById('logout-link');

    if (isAdmin || clientEmail) {
        loginLink.style.display = 'none';
        logoutLink.style.display = 'block';
    } else {
        loginLink.style.display = 'block';
        logoutLink.style.display = 'none';
    }

    // Logout functionality
    function logout() {
        localStorage.removeItem('isAdmin');
        localStorage.removeItem('clientEmail');
        fetch('logout.php', {
            method: 'POST'
        })
            .then(() => {
                loginLink.style.display = 'block';
                logoutLink.style.display = 'none';
                window.location.href = 'login.php';
            });
    }
</script>
</body>
</html>
