<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking - Magic Sole</title>
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

        .booking-section {
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.5s;
        }

        .booking-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
            color: #1a1a1a;
        }

        .booking-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .booking-form label {
            font-size: 1.1rem;
            color: #333;
        }

        .booking-form input, .booking-form select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .booking-form input:focus, .booking-form select:focus {
            border: 1px solid #d4af37;
        }

        .booking-form button {
            background: #1a1a1a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .booking-form button:hover {
            background: #333;
            transform: scale(1.05);
        }

        .message {
            text-align: center;
            margin-top: 10px;
            font-size: 1rem;
        }

        .success {
            color: #2ecc71;
        }

        .error {
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
        <a href="<?php echo dirname($path);?>/admin/bookings">
            <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
        </a>
    </div>
    <nav>
        <a href="<?php echo dirname($path);?>/admin/bookings">Bookings</a>
        <a href="<?php echo dirname($path);?>/client/logout">Logout</a>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Update Booking</h1>
            <p>Modify the booking details below.</p>
        </div>
    </section>

    <section class="booking-section">
        <h2>Update Booking</h2>
        <?php if (isset($error)) { ?>
            <p class="message error"><?php echo htmlspecialchars($error); ?></p>
        <?php } elseif (isset($success)) { ?>
            <p class="message success"><?php echo htmlspecialchars($success); ?></p>
        <?php } ?>

        <?php if (isset($booking)) { ?>
            <form class="booking-form" method="POST">
                <label for="dropoff_date">Drop-off Date</label>
                <input type="datetime-local" id="dropoff_date" name="dropoff_date" value="<?php echo htmlspecialchars($booking['dropoff_date']); ?>" required>

                <label for="pickup_date">Pickup Date (Optional)</label>
                <input type="datetime-local" id="pickup_date" name="pickup_date" value="<?php echo htmlspecialchars($booking['pickup_date'] ?? ''); ?>">

                <label for="shoes_quantity">Number of Shoes</label>
                <input type="number" id="shoes_quantity" name="shoes_quantity" min="1" value="<?php echo htmlspecialchars($booking['shoes_quantity']); ?>" required>

                <label>Services</label>
                <?php
                $selected_services = explode(',', $booking['service_ids']);
                foreach ($services as $service) {
                    $checked = in_array($service['service_id'], $selected_services) ? 'checked' : '';
                ?>
                    <label>
                        <input type="checkbox" name="services[]" value="<?php echo $service['service_id']; ?>" <?php echo $checked; ?>>
                        <?php echo htmlspecialchars($service['service_name']) . " ($" . $service['price'] . ")"; ?>
                    </label>
                <?php } ?>

                <label>Shoe Names (One per line)</label>
                <textarea name="shoe_names[]" rows="3"><?php echo htmlspecialchars(implode("\n", explode(',', $booking['shoe_names']))); ?></textarea>

                <button type="submit">Update Booking</button>
            </form>
        <?php } else { ?>
            <p class="message error">Booking not found.</p>
        <?php } ?>
    </section>

    <div class="bottom-logo">
        <img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo">
    </div>
</div>
</body>
</html>