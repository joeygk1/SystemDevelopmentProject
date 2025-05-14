<?php
$path = $_SERVER['SCRIPT_NAME'];
//session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Orders - Magic Sole</title>
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

        .orders-section {
            max-width: 1400px;
            margin: 40px auto;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards 0.5s;
        }

        .orders-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #1a1a1a;
            text-align: center;
        }

        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: space-between;
        }

        .search-filter input, .search-filter select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            flex: 1;
        }

        .search-filter input:focus, .search-filter select:focus {
            border: 1px solid #d4af37;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow-x: auto;
        }

        .orders-table th, .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        .orders-table th {
            background: linear-gradient(135deg, #d4af37, #f9c303);
            color: #1a1a1a;
            font-weight: 600;
        }

        .orders-table tr {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s forwards;
        }

        .orders-table tr:nth-child(1) { animation-delay: 0.7s; }
        .orders-table tr:nth-child(2) { animation-delay: 0.8s; }
        .orders-table tr:nth-child(3) { animation-delay: 0.9s; }

        .orders-table tr:hover {
            background: #f5f7fa;
            cursor: pointer;
        }

        .orders-table .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-right: 5px;
            display: inline-block;
        }

        .orders-table .update-btn {
            background-color: #f9c303;
            color: #1a1a1a;
        }

        .orders-table .update-btn:hover {
            background-color: #d4af37;
        }

        .orders-table .delete-btn {
            background-color: #ff4444;
            color: #fff;
        }

        .orders-table .delete-btn:hover {
            background-color: #cc0000;
        }

        .orders-table td:last-child {
            min-width: 150px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button:hover {
            background: #333;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .loading-spinner i {
            font-size: 2rem;
            color: #d4af37;
            animation: spin 1s linear infinite;
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
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
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
        }

        .modal-content p {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .modal-content .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            color: #1a1a1a;
            cursor: pointer;
        }

        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .modal-content input, .modal-content select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
        }

        .modal-content input:focus, .modal-content select:focus {
            border: 1px solid #d4af37;
        }

        .modal-content input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .modal-content button {
            padding: 10px 20px;
            background: #f9c303;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #d4af37;
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
                flex-wrap: wrap;
            }

            footer {
                position: relative;
                width: 100%;
                left: 0;
            }

            .orders-section {
                max-width: 100%;
            }

            .orders-table {
                font-size: 0.9rem;
            }

            .orders-table th, .orders-table td {
                padding: 10px;
                white-space: normal;
                font-size: 0.8rem;
            }

            .orders-table td:last-child {
                min-width: 120px;
            }

            .search-filter {
                flex-direction: column;
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

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes modalFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
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
        <a href="<?php echo dirname($path);?>/client/home">Home</a>
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
    <section class="hero">
        <div class="hero-content">
            <h1>View Your Orders</h1>
            <p>Manage your bookings here.</p>
        </div>
    </section>
    <div class="orders-section">
        <h2>Your Bookings</h2>
        <div class="search-filter">
            <input type="text" id="search-input" placeholder="Search by order ID...">
            <select id="status-filter">
                <option value="">All Statuses</option>
                <option value="Processing">Processing</option>
                <option value="Ready for Pickup">Ready for Pickup</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <div class="loading-spinner" id="loading-spinner">
            <i class="fas fa-spinner"></i>
        </div>
        <table class="orders-table" id="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Username</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <?php if (empty($data)) { ?>
                    <tr>
                        <td colspan="9">No bookings found.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($data as $booking) { ?>
                        <tr>
                            <td data-order-id="<?php echo $booking->getBookingId(); ?>"><?php echo $booking->getBookingId(); ?></td>
                            <td data-date="<?php echo $booking->getDropoffDate(); ?>"><?php echo $booking->getDropoffDate(); ?></td>
                            <td data-total="<?php echo $booking->getTotalPrice(); ?>"><?php echo $booking->getTotalPrice(); ?></td>
                            <td data-status="<?php echo $booking->getStatus(); ?>"><?php echo $booking->getStatus(); ?></td>
                            <td data-name="<?php echo $booking->getName(); ?>"><?php echo $booking->getName(); ?></td>
                            <td data-phone="<?php echo $booking->getPhone() ?? ''; ?>"><?php echo $booking->getPhone() ?? ''; ?></td>
                            <td data-username="<?php echo $booking->getUsername() ?? ''; ?>"><?php echo $booking->getUsername() ?? ''; ?></td>
                            <td data-payment-method="<?php echo $booking->getPaymentMethod() ?? 'cash'; ?>"><?php echo $booking->getPaymentMethod() ?? 'cash'; ?></td>
                            <td>
                                <button class="action-btn update-btn" onclick="openUpdateModal('<?php echo $booking->getBookingId(); ?>')">Update</button>
                                <button class="action-btn delete-btn" onclick="deleteOrder('<?php echo $booking->getBookingId(); ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <div class="pagination">
            <button onclick="changePage(-1)">Previous</button>
            <button onclick="changePage(1)">Next</button>
        </div>
    </div>
    <div class="modal" id="order-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <span id="modal-order-id"></span></p>
            <p><strong>Date:</strong> <span id="modal-date"></span></p>
            <p><strong>Items:</strong> <span id="modal-items"></span></p>
            <p><strong>Total:</strong> <span id="modal-total"></span></p>
            <p><strong>Status:</strong> <span id="modal-status"></span></p>
        </div>
    </div>
    <div class="modal" id="update-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeUpdateModal()">×</span>
            <h3>Update Booking Details</h3>
            <label for="update-order-id">Order ID</label>
            <input type="text" id="update-order-id" readonly>
            <label for="update-name">Name</label>
            <input type="text" id="update-name">
            <label for="update-phone">Phone Number</label>
            <input type="tel" id="update-phone" pattern="[0-9]{10}" placeholder="1234567890">
            <label for="update-username">Username</label>
            <input type="text" id="update-username">
            <label for="update-dropoff-time">Drop-off Time</label>
            <input type="time" id="update-dropoff-time">
            <label for="update-payment-method">Payment Method</label>
            <select id="update-payment-method">
                <option value="cash">Cash</option>
                <option value="etransfer">E-Transfer</option>
            </select>
            <button onclick="saveOrder()">Save</button>
        </div>
    </div>
</div>

<script>
    const clientEmail = localStorage.getItem('clientEmail') || 'client@example.com';

    let currentPage = 1;
    const rowsPerPage = 5;
    let selectedOrderId = null;

    function showOrderDetails(order) {
        document.getElementById('modal-order-id').textContent = order.orderId;
        document.getElementById('modal-date').textContent = order.date;
        document.getElementById('modal-items').textContent = order.items;
        document.getElementById('modal-total').textContent = order.total;
        document.getElementById('modal-status').textContent = order.status;
        document.getElementById('order-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('order-modal').style.display = 'none';
    }

    function openUpdateModal(orderId) {
        console.log('Opening modal for orderId:', orderId);
        selectedOrderId = orderId;
        const row = document.querySelector(`tr td[data-order-id="${orderId}"]`)?.closest('tr');
        if (row) {
            document.getElementById('update-order-id').value = orderId;
            document.getElementById('update-name').value = row.querySelector('[data-name]')?.getAttribute('data-name') || '';
            document.getElementById('update-phone').value = row.querySelector('[data-phone]')?.getAttribute('data-phone') || '';
            document.getElementById('update-username').value = row.querySelector('[data-username]')?.getAttribute('data-username') || '';
            const dropoffDate = row.querySelector('[data-date]')?.getAttribute('data-date') || '';
            // Extract time if present, otherwise use a default time
            let time = '';
            if (dropoffDate) {
                const parts = dropoffDate.split(' ');
                time = parts.length > 1 ? parts[1] : '12:00'; // Default to 12:00 if no time is present
            }
            document.getElementById('update-dropoff-time').value = time;
            document.getElementById('update-payment-method').value = row.querySelector('[data-payment-method]')?.getAttribute('data-payment-method') || 'cash';
            document.getElementById('update-modal').style.display = 'flex';
        } else {
            console.log('Row not found for orderId:', orderId);
        }
    }

    function closeUpdateModal() {
        document.getElementById('update-modal').style.display = 'none';
        selectedOrderId = null;
    }

    function saveOrder() {
        const orderId = document.getElementById('update-order-id').value;
        const name = document.getElementById('update-name').value.trim();
        const phone = document.getElementById('update-phone').value.trim();
        const username = document.getElementById('update-username').value.trim();
        const dropoffTime = document.getElementById('update-dropoff-time').value;
        const paymentMethod = document.getElementById('update-payment-method').value;
        const loadingSpinner = document.getElementById('loading-spinner');

        // Validate all required fields
        if (!orderId || !name || !dropoffTime || !paymentMethod) {
            alert('Please fill in all required fields (Order ID, Name, Drop-off Time, Payment Method).');
            return;
        }

        loadingSpinner.style.display = 'block';
        fetch('<?php echo dirname($path); ?>/booking/updateClientDetails', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                booking_id: orderId,
                name: name,
                phone: phone,
                username: username,
                dropoff_time: dropoffTime,
                payment_method: paymentMethod
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    throw new Error(`Network response was not ok: ${text}`);
                });
            }
            return res.json();
        })
        .then(response => {
            loadingSpinner.style.display = 'none';
            if (response.success) {
                closeUpdateModal();
                location.reload();
            } else {
                alert(response.message || 'Failed to update booking details.');
            }
        })
        .catch(error => {
            loadingSpinner.style.display = 'none';
            console.error('Fetch error in update:', error.message);
            alert('An error occurred: ' + error.message);
        });
    }

    function deleteOrder(orderId) {
        if (confirm('Are you sure you want to delete this booking?')) {
            const redirectBase = "<?php echo dirname($path) ?>/booking/delete/";
            window.location.replace(redirectBase + orderId);
        }
    }

    function changePage(direction) {
        currentPage += direction;
        if (currentPage < 1) currentPage = 1;
        filterOrders();
    }

    document.getElementById('search-input').addEventListener('input', filterOrders);
    document.getElementById('status-filter').addEventListener('change', filterOrders);

    function filterOrders() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;

        const rows = document.querySelectorAll('#orders-tbody tr');
        rows.forEach(row => {
            const orderId = row.querySelector('[data-order-id]').textContent.toLowerCase();
            const status = row.querySelector('[data-status]').textContent;
            const matchesSearch = orderId.includes(searchTerm);
            const matchesStatus = statusFilter ? status === statusFilter : true;
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    const spinner = document.getElementById('loading-spinner');
    spinner.style.display = 'none';
</script>
</body>
</html>
<?php
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