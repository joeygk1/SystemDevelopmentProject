<?php
$path = $_SERVER['SCRIPT_NAME'];
if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div style="
        position: fixed;
        top: 0;
        left: 260px;
        width: calc(100% - 260px);
        background: rgba(255,255,255,0.95);
        color: #000;
        z-index: 9999;
        padding: 10px;
        font-size: 14px;
        border-bottom: 1px solid #ccc;
        max-height: 300px;
        overflow-y: auto;
    ">
        <pre><?php var_dump([
                $_SESSION['user_id'],
                $_POST['date'] . ' ' . $_POST['timeSlot'],
                null,
                $_POST['shoeCount'],
                "Pending"
            ]); ?></pre>
    </div>
<?php endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Magic Sole</title>
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
            background-image: url('<?php echo dirname($path);?>/Images/Sneakers.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            min-height: 100vh;
        }

        .booking-form {
            background: linear-gradient(135deg, #d4af37, #f9c303);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            color: #1a1a1a;
            animation: fadeIn 1s ease-out;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .booking-form h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .payment-methods {
            display: flex;
            gap: 20px;
        }

        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .time-slot {
            padding: 8px 15px;
            background: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .time-slot:hover {
            background: #f9c303;
        }

        .request-btn {
            background: #1a1a1a;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            width: 30%;
            margin-left: 70%;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .request-btn:hover {
            background: #333;
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

        .shoe-service-group {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .shoe-service-group h3 {
            margin-bottom: 10px;
        }

        .service-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .total-cost {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
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
        if(!isset($_SESSION['token'])){
            ?>
            <a href="<?php echo dirname($path);?>/client/login">Login</a>
            <a href="<?php echo dirname($path);?>/client/register">Register</a>
            <?php
        }
        else{
            ?>
            <a href="<?php echo dirname($path);?>/client/client-orders">Orders</a>
            <a href="<?php echo dirname($path);?>/client/logout" >Logout</a>

            <?php
        }
        ?>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <section class="booking-form">
        <h1>Book Now!</h1>
        <form id="bookingForm" method="POST"  enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phoneNumber" required>
            </div>
            <div class="form-group">
                <label for="instagram">Instagram Username (optional) </label>
                <input type="text" id="instagram" placeholder="@username" >
            </div>

            <div class="form-group">
                <label for="shoeCount">Number of Shoe Pairs (Max 10)</label>
                <select id="shoeCount" name="shoeCount" required>
                    <option value="">Select number</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
            </div>

            <div id="shoeServices"></div>

            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label>Available Times</label>
                <div class="time-slots" id="timeSlots"></div>
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-methods">
                    <label><input type="radio" name="payment" value="cash" required> Cash</label>
                    <label><input type="radio" name="payment" value="etransfer"> E-transfer</label>
                </div>
            </div>
<!--            <div class="form-group">-->
<!--                <label for="shoeImages">Upload Shoe Images (Multiple allowed)</label>-->
<!--                <input type="file" id="shoeImages" multiple accept="image/*">-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label for="comments">Comments</label>-->
<!--                <textarea id="comments" rows="4"></textarea>-->
<!--            </div>-->
            <label class="total-cost">
                Total Cost: $<label for="totalCost"></label><input id="totalCost" name="totalCost" value="0" readonly style="width:50px; align-content: center">
            </label>
            <button type="submit" class="request-btn">Request Booking</button>
        </form>
    </section>
</div>

<script>
    const servicePrices = {
        cleaning: 50,
        repaint: 80,
        icysole: 20,
        redye: 80
    };

    // const timeSlots = [
    //     "10:00 AM", "11:00 AM", "1:00 PM", "2:00 PM",
    //     "3:00 PM", "4:00 PM", "5:00 PM"
    // ];

    const timeSlots = [
        "10:00:00", "11:00:00", "13:00:00", "14:00:00",
        "15:00:00", "16:00:00", "17:00:00"
    ];


    function generateShoeServices(count) {
        const container = document.getElementById('shoeServices');
        container.innerHTML = '';

        for (let i = 1; i <= count; i++) {
            const shoeDiv = document.createElement('div');
            shoeDiv.className = 'shoe-service-group';
            shoeDiv.innerHTML = `
                    <h3>Shoe Pair ${i} - Select Services</h3>
                    <div class="service-checkboxes">
                        <label><input type="checkbox" name="services_${i}[]" value="cleaning" data-price="50"> Cleaning ($50)</label>
                        <label><input type="checkbox" name="services_${i}[]" value="repaint" data-price="80"> Re-paint ($80)</label>
                        <label><input type="checkbox" name="services_${i}[]" value="icysole" data-price="20"> Icy-sole ($20)</label>
                        <label><input type="checkbox" name="services_${i}[]" value="redye" data-price="80"> Re-dye ($80)</label>
                        <label>Shoe Image</label><input type="file" name="shoeImage[]">
                    </div>
                `;
            container.appendChild(shoeDiv);
        }
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            total += parseInt(checkbox.dataset.price);
        });
        document.getElementById('totalCost').value = total;
    }

    document.getElementById('shoeCount').addEventListener('change', function() {
        generateShoeServices(this.value);
    });

    document.getElementById('shoeServices').addEventListener('change', updateTotal);


    document.getElementById('date').addEventListener('change', function() {
        const selectedDate = document.getElementById('date').value;
        console.log("Selected date:", selectedDate);

        const timeSlotsDiv = document.getElementById('timeSlots');
        timeSlotsDiv.innerHTML = '';

        timeSlots.forEach((slot, index) => {
            const label = document.createElement('label');
            label.style.marginRight = "15px";

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'timeSlot';
            radio.value = slot;
            radio.required = true;

            label.appendChild(radio);
            label.appendChild(document.createTextNode(" " + slot));

            timeSlotsDiv.appendChild(label);
        });
    });



    // document.getElementById('bookingForm').addEventListener('submit', function(e) {
    //     e.preventDefault();
    //     const selectedServices = [];
    //     const shoeCount = document.getElementById('shoeCount').value;
    //     for (let i = 1; i <= shoeCount; i++) {
    //         const services = Array.from(document.querySelectorAll(`input[name="services_${i}"]:checked`))
    //             .map(input => input.value);
    //         selectedServices.push({ pair: i, services });
    //     }
    //     console.log('Selected services:', selectedServices);
    //     alert('Booking request submitted!');
    // });

    // Set minimum date to today
    document.getElementById('date').min = new Date().toISOString().split('T')[0];
</script>
</body>
</html>
