<?php
ob_start();
$path = $_SERVER['SCRIPT_NAME'];
include_once "Models/Booking.php";

// NEW: Add session debugging to check token
file_put_contents('debug.log', "client-orders.php - Session token: " . (isset($_SESSION['token']) ? 'set' : 'not set') . ", session_id: " . session_id() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
// NEW: Prevent redirect loop
if (isset($_GET['redirected'])) {
    die("Session validation failed. Please log in again. <a href=$path/client/login'>Login</a>");
}

if (!isset($_SESSION['token'])) {
    header('Location: /MagicSoleProject/client/login?redirected=1');
    exit;
}

// Handle AJAX requests for update and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    file_put_contents('debug.log', "client-orders.php - POST request received with data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    if (isset($data['action'])) {
        try {
            if ($data['action'] === 'update') {
                if (!isset($data['booking_id']) || !isset($data['name']) || !isset($data['dropoff_time']) || !isset($data['payment_method'])) {
                    file_put_contents('debug.log', "client-orders.php - Invalid or missing update data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => 'Invalid or missing data']);
                    exit;
                }

                $booking = new Booking((int)$data['booking_id']);
                if (!$booking->getBookingId()) {
                    file_put_contents('debug.log', "client-orders.php - Booking not found for ID: " . $data['booking_id'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => 'Booking not found']);
                    exit;
                }

                // NEW: Add services and total_Price to updateData
                $servicePrices = ['cleaning' => 50, 'repaint' => 80, 'icysole' => 20, 'redye' => 80];
                $total_Price = 0;
                if (isset($data['services']) && is_array($data['services'])) {
                    foreach ($data['services'] as $service) {
                        $total_Price += $servicePrices[$service] ?? 0;
                    }
                }

                $updateData = [
                    'name' => $data['name'],
                    'dropoff_date' => date('Y-m-d H:i:s', strtotime($data['dropoff_time'])),
                    'payment_method' => $data['payment_method'],
                    'phone' => $data['phone'],
                    // NEW: Include services and total_Price
                    'services' => $data['services'] ?? [],
                    'total_Price' => $total_Price
                ];
                file_put_contents('debug.log', "client-orders.php - Updating booking ID: " . $data['booking_id'] . " with data: " . print_r($updateData, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $response = $booking->updateClientDetails($updateData);
                echo json_encode($response);
                exit;
            } elseif ($data['action'] === 'delete') {
                if (!isset($data['booking_id'])) {
                    file_put_contents('debug.log', "client-orders.php - Missing booking_id for delete at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => 'Invalid or missing booking ID']);
                    exit;
                }

                $booking = new Booking((int)$data['booking_id']);
                if (!$booking->getBookingId()) {
                    file_put_contents('debug.log', "client-orders.php - Booking not found for ID: " . $data['booking_id'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => 'Booking not found']);
                    exit;
                }

                file_put_contents('debug.log', "client-orders.php - Deleting booking ID: " . $data['booking_id'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $response = $booking->delete();
                echo json_encode($response);
                exit;
            }
        } catch (Exception $e) {
            $error_message = 'Error: ' . $e->getMessage();
            file_put_contents('debug.log', "client-orders.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => $error_message]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$session_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 'not set';
file_put_contents('debug.log', "client-orders.php - Session client_id/user_id: $session_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
$data = Booking::list();
file_put_contents('debug.log', "client-orders.php - Bookings fetched, count: " . count($data) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Orders - Magic Sole</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; display: flex; }
        header { background-color: #1a1a1a; color: white; padding: 2rem 1rem; display: flex; flex-direction: column; align-items: center; width: 250px; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); animation: slideInLeft 1s ease-out; }
        .logo img { width: 120px; margin-bottom: 2rem; }
        nav { display: flex; flex-direction: column; gap: 20px; width: 100%; overflow-y: auto }
        nav a { color: #e3e3e3; text-decoration: none; font-size: 1.4rem; padding: 10px; border-radius: 8px; text-align: center; }
        nav a:hover { background: #f9c303; color: #1a1a1a; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 50px; }
        .hero { background: linear-gradient(135deg, #d4af37, #f9c303); border-radius: 20px; padding: 40px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); color: #1a1a1a; animation: fadeIn 1s ease-out; text-align: center; }
        .hero-content h1 { font-size: 3.5rem; margin-bottom: 15px; }
        .orders-section { max-width: 1400px; margin: 40px auto; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); padding: 30px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); opacity: 0; transform: translateY(50px); animation: fadeInUp 1s forwards 0.5s; }
        .orders-section h2 { font-size: 2.5rem; margin-bottom: 20px; color: #1a1a1a; text-align: center; }
        .search-filter { display: flex; gap: 15px; margin-bottom: 20px; justify-content: space-between; }
        .search-filter input, .search-filter select { padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 5px; outline: none; flex: 1; }
        .search-filter input:focus, .search-filter select:focus { border: 1px solid #d4af37; }
        .orders-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow-x: auto; }
        .orders-table th, .orders-table td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; white-space: nowrap; }
        .orders-table th { background: linear-gradient(135deg, #d4af37, #f9c303); color: #1a1a1a; font-weight: 600; }
        .orders-table tr { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.5s forwards; }
        .orders-table tr:nth-child(1) { animation-delay: 0.7s; }
        .orders-table tr:nth-child(2) { animation-delay: 0.8s; }
        .orders-table tr:nth-child(3) { animation-delay: 0.9s; }
        .orders-table tr:hover { background: #f5f7fa; cursor: pointer; }
        .orders-table .action-btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; margin-right: 5px; display: inline-block; }
        .orders-table .update-btn { background-color: #f9c303; color: #1a1a1a; }
        .orders-table .update-btn:hover { background-color: #d4af37; }
        .orders-table .delete-btn { background-color: #ff4444; color: #fff; }
        .orders-table .delete-btn:hover { background-color: #cc0000; }
        .orders-table td:last-child { min-width: 150px; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination button { padding: 8px 16px; background: #1a1a1a; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .pagination button:disabled { background: #666; cursor: not-allowed; }
        .pagination button:hover:not(:disabled) { background: #333; }
        .pagination span { padding: 8px 16px; font-size: 1rem; color: #1a1a1a; }
        .loading-spinner { display: none; text-align: center; margin: 20px 0; }
        .loading-spinner i { font-size: 2rem; color: #d4af37; animation: spin 1s linear infinite; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; position: relative; opacity: 0; transform: scale(0.8); animation: modalFadeIn 0.3s forwards; }
        .modal-content h3 { font-size: 1.8rem; margin-bottom: 20px; color: #1a1a1a; }
        .modal-content p { font-size: 1.1rem; margin-bottom: 10px; color: #555; }
        .modal-content .close-btn { position: absolute; top: 10px; right: 15px; font-size: 1.5rem; color: #1a1a1a; cursor: pointer; }
        .modal-content label { display: block; margin-bottom: 5px; font-weight: 500; color: #1a1a1a; }
        .modal-content input, .modal-content select { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .modal-content input:focus, .modal-content select:focus { border: 1px solid #d4af37; }
        .modal-content input[readonly] { background: #f5f5f5; cursor: not-allowed; }
        .modal-content button { padding: 10px 20px; background: #f9c303; color: #1a1a1a; border: none; border-radius: 5px; cursor: pointer; }
        .modal-content button:hover { background: #d4af37; }
        footer { font-size: 0.9rem; color: white; text-align: center; padding: 1rem 0; position: fixed; bottom: 0; left: 0; width: 250px; background-color: #1a1a1a; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); }
        @media (max-width: 768px) { 
            .main-content { margin-left: 0; width: 100%; padding: 20px; } 
            header { width: 100%; height: auto; position: relative; padding: 1rem; } 
            nav { flex-direction: row; justify-content: center; gap: 15px; flex-wrap: wrap; } 
            footer { position: relative; width: 100%; left: 0; } 
            .orders-section { max-width: 100%; } 
            .orders-table { font-size: 0.9rem; } 
            .orders-table th, .orders-table td { padding: 10px; white-space: normal; font-size: 0.8rem; } 
            .orders-table td:last-child { min-width: 120px; } 
            .search-filter { flex-direction: column; } 
        }
        @keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes modalFadeIn { to { opacity: 1; transform: scale(1); } }
        /* NEW: CSS for service checkboxes */
        .service-checkboxes { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="<?php echo dirname($path);?>/client/home"><img src="<?php echo dirname($path);?>/Images/MagicNoBackground.png" alt="Magic Sole Logo"></a></div>
        <nav>
            <a href="<?php echo dirname($path);?>/client/home">Home</a>
            <a href="<?php echo dirname($path);?>/client/services">Services</a>
            <a href="<?php echo dirname($path);?>t/client/about">About</a>
            <a href="<?php echo dirname($path);?>/client/policies">Policies</a>
            <a href="<?php echo dirname($path);?>/booking/booking">Booking</a>
            <a href="<?php echo dirname($path);?>/client/gallery">Gallery</a>
            <a href="<?php echo dirname($path);?>/client/help" target="_blank">Help</a>
            <?php if (!isset($_SESSION['token'])) { ?>
                <a href="<?php echo dirname($path);?>/client/login">Login</a>
                <a href="<?php echo dirname($path);?>/client/register">Register</a>
            <?php } else { ?>
                <a href="<?php echo dirname($path);?>/client/client-orders">Orders</a>
                <a href="<?php echo dirname($path);?>/client/logout">Logout</a>
            <?php } ?>
        </nav>
        <footer><p>© 2025 Magic Sole. All rights reserved.</p></footer>
    </header>
    <div class="main-content">
        <section class="hero"><div class="hero-content"><h1>View Your Bookings</h1><p>Manage your bookings here.</p></div></section>
        <div class="orders-section">
            <h2>Your Bookings</h2>
            <div class="search-filter">
                <input type="text" id="search-input" placeholder="Search by Booking ID, name, phone, or date...">
                <select id="status-filter">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="loading-spinner" id="loading-spinner"><i class="fas fa-spinner"></i></div>
            <table class="orders-table" id="orders-table">
                <thead><tr><th>Booking ID</th><th>Date</th><th>Total</th><th>Status</th><th>Name</th><th>Phone</th><th>Username</th><th>Payment Method</th><th>Services</th><th>Actions</th></tr></thead>
                <tbody id="orders-tbody">
                    <?php if (empty($data)) { ?>
                        <tr><td colspan="10">No bookings found.</td></tr>
                    <?php } else { 
                        file_put_contents('debug.log', "client-orders.php - Entering foreach loop, count: " . count($data) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                        foreach ($data as $booking) { 
                            $phoneValue = $booking->getPhone();
                        // NEW: Get services
                            $services = $booking->getServices();
                            $servicesStr = !empty($services) ? implode(', ', $services) : 'N/A';
                            file_put_contents('debug.log', "client-orders.php - Rendering booking ID: " . $booking->getBookingId() . ", Phone: " . ($phoneValue !== null && $phoneValue !== '' ? $phoneValue : 'empty') . ", Username: " . ($booking->getUsername() ?: 'empty') . ", Services: " . $servicesStr . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            ?>
                            <tr>
                                <td data-order-id="<?php echo htmlspecialchars($booking->getBookingId()); ?>"><?php echo htmlspecialchars($booking->getBookingId()); ?></td>
                                <td data-date="<?php echo htmlspecialchars($booking->getDropoffDate()); ?>"><?php echo htmlspecialchars($booking->getDropoffDate()); ?></td>
                                <td data-total="<?php echo htmlspecialchars($booking->getTotalPrice()); ?>">$<?php echo number_format($booking->getTotalPrice(), 2); ?></td>
                                <td data-status="<?php echo htmlspecialchars($booking->getStatus()); ?>"><?php echo htmlspecialchars($booking->getStatus()); ?></td>
                                <td data-name="<?php echo htmlspecialchars($booking->getName()); ?>"><?php echo htmlspecialchars($booking->getName()); ?></td>
                                <td data-phone="<?php echo htmlspecialchars($phoneValue); ?>"><?php echo htmlspecialchars($phoneValue) ?: 'N/A'; ?></td>
                                <td data-username="<?php echo htmlspecialchars($booking->getUsername()); ?>"><?php echo htmlspecialchars($booking->getUsername()) ?: 'N/A'; ?></td>
                                <td data-payment-method="<?php echo htmlspecialchars($booking->getPaymentMethod()); ?>"><?php echo htmlspecialchars($booking->getPaymentMethod()); ?></td>
                                <!-- NEW: Services column -->
                                <td data-services="<?php echo htmlspecialchars($servicesStr); ?>"><?php echo htmlspecialchars($servicesStr); ?></td>
                                <td>
                                    <!-- NEW: Pass services to openUpdateModal -->
                                    <button class="action-btn update-btn" onclick="openUpdateModal('<?php echo htmlspecialchars($booking->getBookingId()); ?>', '<?php echo htmlspecialchars($booking->getName()); ?>', '<?php echo htmlspecialchars($booking->getDropoffDate()); ?>', '<?php echo htmlspecialchars($phoneValue ?: ''); ?>', '<?php echo htmlspecialchars($booking->getPaymentMethod()); ?>', '<?php echo htmlspecialchars(json_encode($services)); ?>')">Update</button>
                                    <button class="action-btn delete-btn" onclick="deleteOrder('<?php echo htmlspecialchars($booking->getBookingId()); ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <button id="prev-btn" onclick="changePage(-1)" disabled>Previous</button>
                    <span id="page-info">Page 1 of 1</span>
                    <button id="next-btn" onclick="changePage(1)" disabled>Next</button>
                </div>
            </div>
            <div class="modal" id="update-modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeUpdateModal()">×</span>
                    <h3>Update Booking Details</h3>
                    <label for="update-order-id">Order ID</label><input type="text" id="update-order-id" readonly>
                    <label for="update-name">Name</label><input type="text" id="update-name">
                    <label for="update-phone">Phone Number</label><input type="tel" id="update-phone" pattern="[0-9]{10}" placeholder="Enter 10-digit phone number">
                    <label for="update-dropoff-time">Drop-off Time</label><input type="datetime-local" id="update-dropoff-time">
                    <label for="update-payment-method">Payment Method</label>
                    <select id="update-payment-method"><option value="cash">Cash</option><option value="etransfer">E-Transfer</option></select>
                    <!-- NEW: Service checkboxes and total -->
                    <label>Services</label>
                    <div class="service-checkboxes">
                        <label><input type="checkbox" name="services[]" value="cleaning"> Cleaning ($50)</label>
                        <label><input type="checkbox" name="services[]" value="repaint"> Re-paint ($80)</label>
                        <label><input type="checkbox" name="services[]" value="icysole"> Icy-sole ($20)</label>
                        <label><input type="checkbox" name="services[]" value="redye"> Re-dye ($80)</label>
                    </div>
                    <label for="update-total">Total</label>
                    <input type="text" id="update-total" readonly>
                    <button onclick="saveOrder()">Save</button>
                </div>
            </div>
        </div>
        <script>
            let currentPage = 1;
            const rowsPerPage = 5;

            function updatePagination() {
                const allRows = document.querySelectorAll('#orders-tbody tr');
                const visibleRows = Array.from(allRows).filter(row => !row.classList.contains('hidden'));
                const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
                currentPage = Math.min(currentPage, Math.max(1, totalPages));
                if (currentPage < 1) currentPage = 1;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                allRows.forEach(row => {
                    row.style.display = 'none';
                });

                visibleRows.slice(start, end).forEach(row => {
                    row.style.display = '';
                });

                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                const pageInfo = document.getElementById('page-info');

                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage >= totalPages || totalPages === 0;
                pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
            }

            function changePage(direction) {
                currentPage += direction;
                updatePagination();
            }

            function filterOrders() {
                const searchTerm = document.getElementById('search-input').value.toLowerCase();
                const statusFilter = document.getElementById('status-filter').value;
                const rows = document.querySelectorAll('#orders-tbody tr');

                rows.forEach(row => {
                    const orderId = row.querySelector('[data-order-id]')?.textContent.toLowerCase() || '';
                    const name = row.querySelector('[data-name]')?.textContent.toLowerCase() || '';
                    const phone = row.querySelector('[data-phone]')?.textContent.toLowerCase() || '';
                    const date = row.querySelector('[data-date]')?.textContent.toLowerCase() || '';
                    const status = row.querySelector('[data-status]')?.textContent || '';
            // NEW: Include services in filtering
                    const services = row.querySelector('[data-services]')?.textContent.toLowerCase() || '';

                    const matchesSearch = (
                        orderId.includes(searchTerm) ||
                        name.includes(searchTerm) ||
                        phone.includes(searchTerm) ||
                        date.includes(searchTerm) ||
                        services.includes(searchTerm)
                        );
                    const matchesStatus = statusFilter ? status === statusFilter : true;

                    row.classList.toggle('hidden', !(matchesSearch && matchesStatus));
                });

                currentPage = 1;
                updatePagination();
            }

    // NEW: Modified openUpdateModal to handle services
            function openUpdateModal(orderId, name, dropoffDate, phone, paymentMethod, servicesJson) {
                console.log('Opening modal for orderId:', orderId);
                selectedOrderId = orderId;
                const services = servicesJson ? JSON.parse(servicesJson) : [];
                const row = document.querySelector(`tr td[data-order-id="${orderId}"]`)?.closest('tr');
                if (row) {
                    document.getElementById('update-order-id').value = orderId;
                    document.getElementById('update-name').value = name || row.querySelector('[data-name]')?.getAttribute('data-name') || '';
                    document.getElementById('update-phone').value = phone || row.querySelector('[data-phone]')?.getAttribute('data-phone') || '';
                    document.getElementById('update-dropoff-time').value = dropoffDate ? new Date(dropoffDate).toISOString().slice(0, 16) : '';
                    document.getElementById('update-payment-method').value = paymentMethod || row.querySelector('[data-payment-method]')?.getAttribute('data-payment-method') || 'cash';
            // NEW: Set service checkboxes
                    document.querySelectorAll('.service-checkboxes input').forEach(checkbox => {
                        checkbox.checked = services.includes(checkbox.value);
                    });
            // NEW: Calculate initial total
                    updateTotal();
                    document.getElementById('update-modal').style.display = 'flex';
                } else {
                    alert('Order not found.');
                }
            }

            function closeUpdateModal() {
                document.getElementById('update-modal').style.display = 'none';
                selectedOrderId = null;
            }

    // NEW: Function to update total based on selected services
            function updateTotal() {
                const servicePrices = { cleaning: 50, repaint: 80, icysole: 20, redye: 80 };
                let total = 0;
                document.querySelectorAll('.service-checkboxes input:checked').forEach(checkbox => {
                    total += servicePrices[checkbox.value] || 0;
                });
                document.getElementById('update-total').value = `$${total.toFixed(2)}`;
            }

            function saveOrder() {
                const orderId = document.getElementById('update-order-id').value;
                const name = document.getElementById('update-name').value.trim();
                const phone = document.getElementById('update-phone').value.trim();
                const dropoffTime = document.getElementById('update-dropoff-time').value;
                const paymentMethod = document.getElementById('update-payment-method').value;
                const loadingSpinner = document.getElementById('loading-spinner');

        // NEW: Get selected services
                const services = Array.from(document.querySelectorAll('.service-checkboxes input:checked')).map(cb => cb.value);
                const total = parseFloat(document.getElementById('update-total').value.replace('$', '')) || 0;

                console.log('saveOrder inputs:', { orderId, name, phone, dropoffTime, paymentMethod, services, total });

                if (!orderId || !name || !dropoffTime || !paymentMethod) {
                    console.log('Validation failed: Missing required fields');
                    alert('Please fill in all required fields.');
                    return;
                }

        // NEW: Require at least one service
                if (!services.length) {
                    console.log('Validation failed: No services selected');
                    alert('Please select at least one service.');
                    return;
                }

                const phonePattern = /^[0-9]{10}$/;
                if (phone && !phonePattern.test(phone)) {
                    console.log('Validation failed: Invalid phone number:', phone);
                    alert('Please enter a valid 10-digit phone number.');
                    return;
                }

                const payload = { 
                    action: 'update',
                    booking_id: orderId, 
                    name, 
                    phone,
                    dropoff_time: dropoffTime, 
                    payment_method: paymentMethod,
            // NEW: Include services and total
                    services,
                    total_Price: total
                };
                console.log('Sending update request with payload:', payload);

                loadingSpinner.style.display = 'block';

                fetch('client-orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                })
                .then(res => {
                    console.log('Fetch response status:', res.status);
                    console.log('Fetch response headers:', [...res.headers.entries()]);
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(`HTTP error! Status: ${res.status}, Response: ${text}`);
                        });
                    }
                    return res.json();
                })
                .then(response => {
                    console.log('Parsed JSON response:', response);
                    loadingSpinner.style.display = 'none';
                    if (response.success) {
                        closeUpdateModal();
                        location.reload();
                    } else {
                        console.log('Update failed with message:', response.message);
                        alert(response.message || 'Failed to update booking.');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error.message);
                    loadingSpinner.style.display = 'none';
                    alert('An error occurred: ' + error.message);
                });
            }

            function deleteOrder(orderId) {
                console.log('Deleting orderId:', orderId);
                if (confirm('Are you sure you want to delete this booking?')) {
                    const loadingSpinner = document.getElementById('loading-spinner');
                    loadingSpinner.style.display = 'block';
                    const payload = { action: 'delete', booking_id: orderId };
                    fetch('client-orders', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => {
                        console.log('Delete response status:', res.status);
                        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
                        return res.json();
                    })
                    .then(response => {
                        console.log('Parsed delete response:', response);
                        loadingSpinner.style.display = 'none';
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to delete booking.');
                        }
                    })
                    .catch(error => {
                        console.error('Delete fetch error:', error.message);
                        loadingSpinner.style.display = 'none';
                        alert('An error occurred: ' + error.message);
                    });
                }
            }

            document.getElementById('search-input').addEventListener('input', filterOrders);
            document.getElementById('status-filter').addEventListener('change', filterOrders);

    // NEW: Add event listeners for service checkboxes
            document.querySelectorAll('.service-checkboxes input').forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });

            const spinner = document.getElementById('loading-spinner');
            spinner.style.display = 'none';
            updatePagination();
        </script>
    </body>
    </html>