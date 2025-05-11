<?php
$path = $_SERVER['SCRIPT_NAME'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Magic Sole</title>
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
            overflow-x: hidden; /* Prevent horizontal overflow from animations */
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

        .about-details {
            padding: 30px;
            text-align: left;
            max-width: 900px;
            margin: 40px auto;
        }

        .about-details h2 {
            font-size: 3rem; /* Increased from 2.5rem to match hero section boldness */
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1a1a1a;
            position: relative;
            padding-bottom: 10px;
            letter-spacing: 1.5px; /* Adds visual appeal */
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.2s; /* Animated entrance */
        }

        .about-details h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #f9c303);
            transition: width 0.3s ease; /* Smooth transition for hover */
        }

        .about-details h2:hover::after {
            width: 100px; /* Expands underline on hover */
        }

        .about-details p {
            font-size: 1.3rem; /* Increased from default for better readability */
            line-height: 1.8;
            margin-bottom: 20px;
            color: #444;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s forwards 0.4s; /* Animated entrance with delay */
        }

        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.6s; /* Animated entrance for team section */
        }

        .team-member {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: fadeInUp 0.8s forwards 0.8s; /* Individual card animation */
        }

        .team-member:nth-child(2) { animation-delay: 1s; }
        .team-member:nth-child(3) { animation-delay: 1.2s; }

        .team-member img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-10px) scale(1.05); /* Enhanced hover effect */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .team-member:hover img {
            transform: rotate(5deg); /* Subtle rotation on hover */
        }

        .team-member h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .team-member p {
            font-size: 1.1rem;
            color: #666;
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

        .social-media-section {
    color: white;
    text-align: center;
    padding: 30px 10px;
    margin-left: 35%;
    margin-top: 50px;
    max-width: 500px;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15); /* Reduced vertical spread from 8px to 4px */
    opacity: 0;
    transform: translateY(50px);
    animation: fadeInUp 1s forwards 1s; /* Animated entrance */
}

        .social-media-section h2 {
            font-size: 2.5rem; /* Increased for consistency */
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .social-icons a img {
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .social-icons a img:hover {
            transform: scale(1.3) rotate(10deg); /* Enhanced hover with rotation */
            box-shadow: 0 6px 15px rgba(249, 195, 3, 0.5);
        }

        #map {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
            border-radius: 20px;
            margin: 20px auto; /* Reduced from 50px to bring closer */
            max-width: 1000px;
            animation: fadeIn 1s ease-out;
            position: relative;
            overflow: hidden;
            gap: 20px;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 1.2s; /* Animated entrance */
        }

        .find-us-section {
            text-align: center;
            padding: 10px; /* Reduced from 40px to bring closer */
            margin-top: 90px; /* Reduced from 30px to bring closer */
            border-radius: 20px;            
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.8s; /* Animated entrance */
        }
        
        .find-us-section h2 {
            margin-top: -20px;
            font-size: 2.5rem; /* Increased for consistency */
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .map-container {
            flex: 1;
            max-width: 500px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .map-container:hover iframe {
            transform: scale(1.02); /* Subtle zoom on hover */
        }

        .gif-container {
            flex: 1;
            max-width: 500px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .gif-container img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .gif-container:hover img {
            transform: scale(1.02); /* Subtle zoom on hover */
        }

        @media (max-width: 768px) {
            #map {
                flex-direction: column;
            }

            .map-container,
            .gif-container {
                max-width: 100%;
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
        </nav>
        <footer>
            <p>© 2025 Magic Sole. All rights reserved.</p>
        </footer>
    </header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>About Us</h1>
            <p>We breathe new life into your sneakers!</p>
        </div>
    </section>
    <div class="about-details">
        <h2>Our Mission</h2>
        <p>At Magic Sole, we specialize in sneaker restoration that brings your worn-out shoes back to life. Our team is passionate about sneaker culture and committed to providing the best care for your kicks.</p>
        <h2>Meet the Team</h2>
        <div class="team-members">
            <div class="team-member">
                <img src="<?php echo dirname($path);?>/Images/Kev.png" alt="Kevin">
                <h3>Quan</h3>
                <p>Sneaker Spray Paint Expert</p>
            </div>
            <div class="team-member">
                <img src="<?php echo dirname($path);?>/Images/Joshua.png" alt="Joshua">
                <h3>Joshua</h3>
                <p>Sneaker Restoration Pro</p>
            </div>
            <div class="team-member">
                <img src="<?php echo dirname($path);?>/Images/Joey.png" alt="Joey">
                <h3>Joey</h3>
                <p>Sneaker Cleaning Expert</p>
            </div>
        </div>
        <h2>Our Vision</h2>
        <p>To be the leading sneaker restoration brand known for our exceptional service and innovative techniques, creating memories with every step.</p>
    </div>

    <div class="social-media-section">
        <h2>Connect with Us</h2>
        <div class="social-icons">
            <a href="https://www.instagram.com/magic.sole" target="_blank" aria-label="Instagram">
                <img src="<?php echo dirname($path);?>/Images/instagram.png" alt="Instagram">
            </a>
            <a href="https://www.tiktok.com/search?q=magic%20sole&t=1740277844045" target="_blank" aria-label="LinkedIn">
                <img src="<?php echo dirname($path);?>/Images/tiktok.png" alt="LinkedIn">
            </a>
        </div>
    </div>

    <div class="find-us-section">
        <h2>Find us</h2>
    </div>

    <section id="map">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5580.2239612157955!2d-73.5923419!3d45.628486699999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4cc91fd17c33d1d7%3A0x88b0e88ca7448d3c!2s7775%20Av.%20Ren%C3%A9-Descartes%2C%20Montr%C3%A9al%2C%20QC%20H1E%203G6!5e0!3m2!1sen!2sca!4v1730674771104!5m2!1sen!2sca" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="gif-container">
            <img src="<?php echo dirname($path);?>/Images/CoolGrey.gif" alt="Cool Grey Sneaker GIF">
        </div>
    </section>
</div>
</body>
</html>