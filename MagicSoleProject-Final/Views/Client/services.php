<?php
$path = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Magic Sole</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #393838;
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

        .services-section {
            padding: 30px;
            max-width: 1000px;
            margin: 40px auto;
        }

        .services-section h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-align: center;
            color: #1a1a1a;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .service-card {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }


        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .service-card p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 15px;
        }

        .service-card .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 15px;
        }

        .service-card .book-now {
            background: #1a1a1a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        .service-card .book-now:hover {
            background-color: #f9c303;
            transform: scale(1.05);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #e0e4ec, #b0c0d8);
        }

        @media (max-width: 768px) {
            .services-grid {
                grid-template-columns: 1fr;
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
    </nav>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Our Services</h1>
            <p>Transform your sneakers with our premium restoration services!</p>
        </div>
    </section>

    <section class="services-section">
        <h2>Explore Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <img src="<?php echo dirname($path);?>/Images/cleaning.jpeg" alt="Sneaker Cleaning">
                <h3>Sneaker Cleaning</h3>
                <p>Deep cleaning to make your sneakers look brand new.</p>
                <div class="price">$50 per pair</div>
                <a href="<?php echo dirname($path);?>/booking/booking"><button class="book-now">Book Now</button></a>
            </div>
            <div class="service-card">
                <img src="<?php echo dirname($path);?>/Images/re-paint.png" alt="Sneaker Repaint">
                <h3>Sneaker Repaint</h3>
                <p>Custom repainting to refresh or completely change the color.</p>
                <div class="price">$80 per pair</div>
                <a href="<?php echo dirname($path);?>/booking/booking"><button class="book-now">Book Now</button></a>
            </div>

            <div class="service-card">
                <img src="<?php echo dirname($path);?>/Images/redye.jpg" alt="Sneaker Re-dye">
                <h3>Sneaker Redye</h3>
                <p>Custom repainting to refresh or completely change the color.</p>
                <div class="price">$80 per pair</div>
                <a href="<?php echo dirname($path);?>/booking/booking"><button class="book-now">Book Now</button></a>
            </div>

            <div class="service-card">
                <img src="<?php echo dirname($path);?>/Images/icysole.jpg" alt="Icy Sole">
                <h3>Icy Sole Treatment</h3>
                <p>Restore yellowed soles to a crystal-clear, icy finish.</p>
                <div class="price">$20 per pair</div>
                <a href="<?php echo dirname($path);?>/booking/booking"><button class="book-now">Book Now</button></a>
            </div>
        </div>
    </section>
</div>
</body>
</html>
