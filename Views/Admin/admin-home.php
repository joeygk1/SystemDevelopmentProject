<?php
$path = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - Magic Sole</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            transition: background 0.3s, color 0.3s;
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

        .summary-section {
            max-width: 1000px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards;
        }

        .summary-card:nth-child(1) { animation-delay: 0.3s; }
        .summary-card:nth-child(2) { animation-delay: 0.5s; }
        .summary-card:nth-child(3) { animation-delay: 0.7s; }

        .summary-card i {
            font-size: 2rem;
            color: #d4af37;
            margin-bottom: 10px;
        }

        .summary-card h3 {
            font-size: 1.2rem;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .admin-options {
            max-width: 600px;
            margin: 40px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .admin-option {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards;
        }

        .admin-option:nth-child(2) { animation-delay: 0.7s; }
        .admin-option:nth-child(3) { animation-delay: 0.9s; }

        .admin-option a {
            text-decoration: none;
            color: #1a1a1a;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .admin-option:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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
        <a href="<?php echo dirname($path);?>/admin/admin-home">
            <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path);?>/admin/admin-home">Admin Home</a>
        <a href="<?php echo dirname($path);?>/admin/view-orders">View Orders</a>
        <a href="<?php echo dirname($path);?>/admin/order-status">Order Status</a>
        <a href="<?php echo dirname($path);?>/admin/admin-gallery">Manage Gallery</a>
        <a href="<?php echo dirname($path);?>/admin/logout">Logout</a>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Admin Dashboard</h1>
            <p>Manage your orders, statuses, and gallery content.</p>
        </div>
    </section>
    <div class="summary-section">
        <div class="summary-card">
            <i class="fas fa-shopping-cart"></i>
            <h3>Total Orders</h3>
            <p id="total-orders">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-hourglass-half"></i>
            <h3>Pending Orders</h3>
            <p id="pending-orders">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Total Revenue</h3>
            <p id="total-revenue">$0.00</p>
        </div>
    </div>
    <div class="admin-options">
        <div class="admin-option">
            <a href="view-orders.html">View Orders</a>
        </div>
        <div class="admin-option">
            <a href="order-status.html">Order Status</a>
        </div>
        <div class="admin-option">
            <a href="admin-gallery.php">Manage Gallery</a>
        </div>
    </div>
</div>

<script>
    // Lightweight session check for logging (optional, since .htaccess handles redirect)
    fetch('check_session.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Session check response:', data);
        })
        .catch(error => {
            console.error('Error checking session:', error);
        });

    // Logout function
    function logout() {
        fetch('logout.php', { method: 'POST' })
            .then(() => {
                window.location.href = 'login.php';
            })
            .catch(error => {
                console.error('Error during logout:', error);
                window.location.href = 'login.php';
            });
    }

    // Simulate fetching summary data (replace with real API call)
    setTimeout(() => {
        document.getElementById('total-orders').textContent = '0';
        document.getElementById('pending-orders').textContent = '0';
        document.getElementById('total-revenue').textContent = '$0';
    }, 1000);
</script>
</body>
</html>