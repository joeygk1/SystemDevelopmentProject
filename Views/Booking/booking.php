<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path = $_SERVER['SCRIPT_NAME'];

// Debug: Log session variables
error_log("Session clientEmail: " . (isset($_SESSION['clientEmail']) ? $_SESSION['clientEmail'] : 'Not set'));

// Feedback variables
$error = '';
$success = '';

include_once "Models/Booking.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted: " . print_r($_POST, true));
    error_log("Files submitted: " . print_r($_FILES, true));

    $email = isset($_SESSION['clientEmail']) ? $_SESSION['clientEmail'] : null;
    if (!$email) {
        $error = "Client email not found in session. Please log in again.";
        error_log("Redirecting to login due to missing clientEmail");
        header("Location: " . dirname($path) . "/client/login");
        exit();
    }

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
    $instagram = filter_input(INPUT_POST, 'instagram', FILTER_SANITIZE_STRING);
    $shoeCount = filter_input(INPUT_POST, 'shoeCount', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $timeSlot = filter_input(INPUT_POST, 'timeSlot', FILTER_SANITIZE_STRING);
    $payment = filter_input(INPUT_POST, 'payment', FILTER_SANITIZE_STRING);
    $totalCost = filter_input(INPUT_POST, 'totalCost', FILTER_VALIDATE_FLOAT);
    $depositAmount = filter_input(INPUT_POST, 'depositAmount', FILTER_VALIDATE_FLOAT) ?? 0.00;

    // Parse name
    $nameParts = explode(' ', trim($name), 2);
    $firstName = $nameParts[0];
    $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

    // Validate required fields
    if (!$firstName || !$phone || !$shoeCount || !$date || !$timeSlot || !$payment) {
        $error = "All required fields (Name, Phone, Shoe Count, Date, Time Slot, Payment) must be filled.";
        error_log("Validation failed: Required fields missing - " . json_encode($_POST));
    } elseif ($shoeCount < 1 || $shoeCount > 10) {
        $error = "Shoe count must be between 1 and 10.";
        error_log("Validation failed: Invalid shoe count - $shoeCount");
    } else {
        $shoeServices = [];
        $shoeImages = [];

        for ($i = 1; $i <= $shoeCount; $i++) {
            $services = isset($_POST["services_{$i}"]) ? $_POST["services_{$i}"] : [];
            $shoeServices[$i] = $services;

            if (isset($_FILES['shoeImage']['name'][$i-1]) && $_FILES['shoeImage']['error'][$i-1] === UPLOAD_ERR_OK) {
                $uploadDir = '../../Uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid() . '-' . basename($_FILES['shoeImage']['name'][$i-1]);
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['shoeImage']['tmp_name'][$i-1], $filePath)) {
                    $shoeImages[$i] = $filePath;
                } else {
                    $shoeImages[$i] = null;
                    error_log("Failed to upload image for shoe $i");
                }
            } else {
                $shoeImages[$i] = null;
            }
        }

        $serviceMap = ['cleaning' => 1, 'repaint' => 2, 'icysole' => 3, 'redye' => 4];

        $hasServices = false;
        foreach ($shoeServices as $services) {
            if (!empty($services)) {
                $hasServices = true;
                break;
            }
        }

        if (!$hasServices) {
            $error = "At least one service must be selected for each shoe pair.";
            error_log("Validation failed: No services selected");
        } else {
            $dropoffDateTime = date('Y-m-d H:i:s', strtotime("$date $timeSlot"));
            $pickupDateTime = date('Y-m-d H:i:s', strtotime("$date $timeSlot + 3 days"));

            $bookingModel = new Booking();
            $bookingId = $bookingModel->createBooking(
                $firstName,
                $lastName,
                $phone,
                $email,
                $dropoffDateTime,
                $pickupDateTime,
                $shoeCount,
                $shoeServices,
                $shoeImages,
                $serviceMap,
                $payment,
                $totalCost,
                $depositAmount
            );

            if ($bookingId) {
                $success = "Booking and payment recorded successfully! Your booking ID is $bookingId.";
                error_log("Booking successful: ID $bookingId, Total: $totalCost, Deposit: $depositAmount");
                // Redirect to itself to display the success message
                $_SESSION['success'] = $success;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Failed to create booking or payment. Please check the error log.";
                error_log("Booking failed for email $email - Check database or logs");
            }
        }
    }
}

// Check for success message in session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Magic Sole</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; display: flex; }
        header { background-color: #1a1a1a; color: white; padding: 2rem 1rem; display: flex; flex-direction: column; align-items: center; width: 250px; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); animation: slideInLeft 1s ease-out; }
        .logo img { width: 120px; margin-bottom: 2rem; }
        nav { display: flex; flex-direction: column; gap: 20px; width: 100%; }
        nav a { color: #e3e3e3; text-decoration: none; font-size: 1.4rem; padding: 10px; border-radius: 8px; text-align: center; }
        nav a:hover { background: #f9c303; color: #1a1a1a; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 50px; background-image: url('<?php echo dirname($path);?>/Images/Sneakers.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; min-height: 100vh; }
        .booking-form { background: linear-gradient(135deg, #d4af37, #f9c303); border-radius: 20px; padding: 40px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); color: #1a1a1a; animation: fadeIn 1s ease-out; max-width: 800px; margin: 0 auto; position: relative; z-index: 1; }
        .booking-form h1 { font-size: 2.5rem; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        .payment-methods, .time-slots { display: flex; flex-wrap: wrap; gap: 10px; }
        .time-slot { padding: 8px 15px; background: #fff; border-radius: 5px; cursor: pointer; }
        .time-slot:hover { background: #f9c303; }
        .request-btn { background: #1a1a1a; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 1.2rem; cursor: pointer; width: 100%; margin-top: 20px; transition: background 0.3s; }
        .request-btn:hover { background: #333; }
        footer { font-size: 0.9rem; color: white; text-align: center; padding: 1rem 0; position: fixed; bottom: 0; left: 0; width: 250px; background-color: #1a1a1a; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); }
        .shoe-service-group { margin-bottom: 20px; padding: 15px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .shoe-service-group h3 { margin-bottom: 10px; }
        .service-checkboxes { display: flex; flex-wrap: wrap; gap: 15px; }
        .total-cost { font-size: 1.2rem; font-weight: bold; margin: 20px 0; }
        .error-message, .success-message { position: fixed; top: 0; left: 260px; width: calc(100% - 260px); background: rgba(255,255,255,0.95); color: #000; z-index: 9999; padding: 10px; font-size: 14px; border-bottom: 1px solid #ccc; max-height: 300px; overflow-y: auto; }
        .error-message { color: red; }
        .success-message { color: green; }
        @keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
        <a href="<?php echo dirname($path);?>/client/home">Home</a>
        <a href="<?php echo dirname($path);?>/client/services">Services</a>
        <a href="<?php echo dirname($path);?>/client/about">About</a>
        <a href="<?php echo dirname($path);?>/client/policies">Policies</a>
        <a href="<?php echo dirname($path);?>/booking/booking">Booking</a>
        <a href="<?php echo dirname($path);?>/client/gallery">Gallery</a>
        <?php
        if (!isset($_SESSION['token']) || $_SESSION['token'] === null) {
            ?>
            <a href="<?php echo dirname($path);?>/client/login">Login</a>
            <?php
        } else {
            ?>
            <a href="<?php echo dirname($path);?>/client/client-orders">Orders</a>
            <a href="<?php echo dirname($path);?>/client/logout">Logout</a>
            <?php
        }
        ?>
    </nav>
    <footer>
        <p>© 2025 Magic Sole. All rights reserved.</p>
    </footer>
</header>

<div class="main-content">
    <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <section class="booking-form">
        <h1>Book Now!</h1>
        <form id="bookingForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phoneNumber" required value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>" placeholder="e.g., 123-456-7890">
            </div>
            <div class="form-group">
                <label for="instagram">Instagram Username (optional)</label>
                <input type="text" id="instagram" name="instagram" value="<?php echo isset($_POST['instagram']) ? htmlspecialchars($_POST['instagram']) : ''; ?>" placeholder="@username">
            </div>
            <div class="form-group">
                <label for="shoeCount">Number of Shoe Pairs *</label>
                <select id="shoeCount" name="shoeCount" required>
                    <option value="">Select number</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo isset($_POST['shoeCount']) && $_POST['shoeCount'] == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div id="shoeServices"></div>
            <div class="form-group">
                <label for="date">Booking Date *</label>
                <input type="date" id="date" name="date" required value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Time Slot *</label>
                <div class="time-slots" id="timeSlots">
                    <?php if (isset($_POST['timeSlot'])): ?>
                        <?php foreach (["10:00:00", "11:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00"] as $slot): ?>
                            <label style="margin-right: 15px;">
                                <input type="radio" name="timeSlot" value="<?php echo $slot; ?>" required <?php echo $_POST['timeSlot'] === $slot ? 'checked' : ''; ?>>
                                <?php echo substr($slot, 0, -3); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Payment Method *</label>
                <div class="payment-methods">
                    <label><input type="radio" name="payment" value="cash" required <?php echo isset($_POST['payment']) && $_POST['payment'] === 'cash' ? 'checked' : ''; ?>> Cash</label>
                    <label><input type="radio" name="payment" value="etransfer" <?php echo isset($_POST['payment']) && $_POST['payment'] === 'etransfer' ? 'checked' : ''; ?>> E-transfer</label>
                </div>
            </div>
            <div class="form-group">
                <label for="depositAmount">Deposit Amount (optional)</label>
                <input type="number" id="depositAmount" name="depositAmount" step="0.01" min="0" value="<?php echo isset($_POST['depositAmount']) ? htmlspecialchars($_POST['depositAmount']) : '0.00'; ?>" placeholder="Enter deposit amount">
            </div>
            <div class="form-group total-cost">
                <label>Total Cost: $</label>
                <input id="totalCost" name="totalCost" value="<?php echo isset($_POST['totalCost']) ? htmlspecialchars($_POST['totalCost']) : '0'; ?>" readonly style="width: 100px; display: inline-block;">
            </div>
            <button type="submit" class="request-btn">Request Booking</button>
        </form>
    </section>
</div>

<script>
    const servicePrices = { cleaning: 50, repaint: 80, icysole: 20, redye: 80 };
    const timeSlots = ["10:00:00", "11:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00"];

    function generateShoeServices(count) {
        const container = document.getElementById('shoeServices');
        container.innerHTML = '';
        for (let i = 1; i <= count; i++) {
            const shoeDiv = document.createElement('div');
            shoeDiv.className = 'shoe-service-group';
            shoeDiv.innerHTML = `
                <h3>Shoe Pair ${i} - Select Services *</h3>
                <div class="service-checkboxes">
                    <label><input type="checkbox" name="services_${i}[]" value="cleaning" data-price="50"> Cleaning ($50)</label>
                    <label><input type="checkbox" name="services_${i}[]" value="repaint" data-price="80"> Re-paint ($80)</label>
                    <label><input type="checkbox" name="services_${i}[]" value="icysole" data-price="20"> Icy-sole ($20)</label>
                    <label><input type="checkbox" name="services_${i}[]" value="redye" data-price="80"> Re-dye ($80)</label>
                    <label>Shoe Image (optional)</label><input type="file" name="shoeImage[]">
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
        document.getElementById('totalCost').value = total.toFixed(2);
    }

    document.getElementById('shoeCount').addEventListener('change', function() {
        generateShoeServices(this.value);
    });

    document.getElementById('shoeServices').addEventListener('change', updateTotal);

    document.getElementById('date').addEventListener('change', function() {
        const selectedDate = document.getElementById('date').value;
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
            label.appendChild(document.createTextNode(" " + slot.substring(0, 5)));
            timeSlotsDiv.appendChild(label);
        });
    });

    document.getElementById('date').min = new Date().toISOString().split('T')[0];
</script>
</body>
</html>