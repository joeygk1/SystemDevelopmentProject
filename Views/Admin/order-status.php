<?php
ob_start();
include_once "Models/Booking.php";

if (!isset($_SESSION['token']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MagicSoleProject/admin/login');
    exit;
}

$session_id = $_SESSION['user_id'] ?? 'not set';
file_put_contents('debug.log', "order-status.php - Session user_id: $session_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
$data = Booking::listAll();
file_put_contents('debug.log', "order-status.php - Bookings fetched, count: " . count($data) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
$path = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Magic Sole Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; display: flex; }
        header { background-color: #1a1a1a; color: white; padding: 2rem 1rem; display: flex; flex-direction: column; align-items: center; width: 250px; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); animation: slideInLeft 1s ease-out; }
        .logo img { width: 120px; margin-bottom: 2rem; }
        nav { display: flex; flex-direction: column; gap: 20px; width: 100%; }
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
        .action-btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; }
        .update-btn { background-color: #f9c303; color: #1a1a1a; }
        .update-btn:hover { background-color: #d4af37; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination button { padding: 8px 16px; background: #1a1a1a; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .pagination button:hover { background: #333; }
        .loading-spinner { display: none; text-align: center; margin: 20px 0; }
        .loading-spinner i { font-size: 2rem; color: #d4af37; animation: spin 1s linear infinite; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; position: relative; opacity: 0; transform: scale(0.8); animation: modalFadeIn 0.3s forwards; }
        .modal-content h3 { font-size: 1.8rem; margin-bottom: 20px; color: #1a1a1a; }
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
            .search-filter { flex-direction: column; } 
        }
        @keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes modalFadeIn { to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>
<header>
    <div class="logo"><a href="<?php echo dirname($path); ?>/admin/admin-home"><img src="<?php echo dirname($path); ?>/Images/MagicNoBackground.png" alt="Magic Sole Logo"></a></div>
    <nav>
        <a href="<?php echo dirname($path); ?>/admin/admin-home">Admin Home</a>
        <a href="<?php echo dirname($path); ?>/admin/view-orders">View Orders</a>
        <a href="<?php echo dirname($path); ?>/admin/order-status">Manage Orders</a>
        <a href="<?php echo dirname($path); ?>/admin/admin-gallery">Manage Gallery</a>
        <a href="<?php echo dirname($path); ?>/admin/logout">Logout</a>
    </nav>
    <footer><p>© 2025 Magic Sole. All rights reserved.</p></footer>
</header>
<div class="main-content">
    <section class="hero"><div class="hero-content"><h1>Manage Orders</h1><p>Update booking details here.</p></div></section>
    <div class="orders-section">
        <h2>All Bookings</h2>
        <div class="search-filter">
            <input type="text" id="search-input" placeholder="Search by Order ID...">
            <select id="status-filter">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <div class="loading-spinner" id="loading-spinner"><i class="fas fa-spinner"></i></div>
        <table class="orders-table" id="orders-table">
            <thead><tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th><th>Name</th><th>Phone</th><th>Username</th><th>Payment Method</th><th>Actions</th></tr></thead>
            <tbody id="orders-tbody">
                <?php if (empty($data)) { ?>
                    <tr><td colspan="9">No bookings found.</td></tr>
                <?php } else { 
                    foreach ($data as $booking) { 
                        file_put_contents('debug.log', "order-status.php - Rendering booking ID: " . $booking->getBookingId() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                ?>
                    <tr>
                        <td data-order-id="<?php echo $booking->getBookingId(); ?>"><?php echo $booking->getBookingId(); ?></td>
                        <td data-date="<?php echo $booking->getDropoffDate(); ?>"><?php echo $booking->getDropoffDate(); ?></td>
                        <td data-total="<?php echo $booking->getTotalPrice(); ?>">$<?php echo number_format($booking->getTotalPrice(), 2); ?></td>
                        <td data-status="<?php echo $booking->getStatus(); ?>"><?php echo $booking->getStatus(); ?></td>
                        <td data-name="<?php echo htmlspecialchars($booking->getName()); ?>"><?php echo htmlspecialchars($booking->getName()); ?></td>
                        <td data-phone="<?php echo htmlspecialchars($booking->getPhone()); ?>"><?php echo htmlspecialchars($booking->getPhone()) ?: 'N/A'; ?></td>
                        <td data-username="<?php echo htmlspecialchars($booking->getUsername()); ?>"><?php echo htmlspecialchars($booking->getUsername()) ?: 'N/A'; ?></td>
                        <td data-payment-method="<?php echo $booking->getPaymentMethod(); ?>"><?php echo $booking->getPaymentMethod() ?: 'N/A'; ?></td>
                        <td><button class="action-btn update-btn" onclick="openUpdateModal(<?php echo $booking->getBookingId(); ?>, '<?php echo htmlspecialchars($booking->getName()); ?>', '<?php echo $booking->getDropoffDate(); ?>', '<?php echo $booking->getPaymentMethod(); ?>', '<?php echo $booking->getStatus(); ?>')">Update</button></td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
        <div class="pagination"><button onclick="changePage(-1)">Previous</button><button onclick="changePage(1)">Next</button></div>
    </div>
    <div class="modal" id="update-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeUpdateModal()">×</span>
            <h3>Update Booking</h3>
            <label for="update-booking-id">Booking ID</label>
            <input type="text" id="update-booking-id" readonly>
            <label for="update-name">Name</label>
            <input type="text" id="update-name">
            <label for="update-dropoff-date">Dropoff Date</label>
            <input type="datetime-local" id="update-dropoff-date">
            <label for="update-payment-method">Payment Method</label>
            <select id="update-payment-method">
                <option value="cash">Cash</option>
                <option value="etransfer">E-Transfer</option>
            </select>
            <label for="update-status">Status</label>
            <select id="update-status">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            <button onclick="saveBooking()">Save</button>
        </div>
    </div>
</div>
<script>
    let currentPage = 1;
    const rowsPerPage = 5;

    function changePage(direction) {
        currentPage += direction;
        if (currentPage < 1) currentPage = 1;
        const rows = document.querySelectorAll('#orders-tbody tr:not([style*="display: none"])');
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        document.querySelectorAll('#orders-tbody tr').forEach((row, index) => {
            row.style.display = (index >= start && index < end && row.style.display !== 'none') ? '' : 'none';
        });
    }

    document.getElementById('search-input').addEventListener('input', filterOrders);
    document.getElementById('status-filter').addEventListener('change', filterOrders);

    function filterOrders() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const rows = document.querySelectorAll('#orders-tbody tr');
        rows.forEach(row => {
            const orderId = row.querySelector('[data-order-id]')?.textContent.toLowerCase();
            const status = row.querySelector('[data-status]')?.textContent;
            const matchesSearch = orderId?.includes(searchTerm);
            const matchesStatus = statusFilter ? status === statusFilter : true;
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
        currentPage = 1;
        changePage(0);
    }

    function openUpdateModal(bookingId, name, dropoffDate, paymentMethod, status) {
        document.getElementById('update-booking-id').value = bookingId;
        document.getElementById('update-name').value = name;
        document.getElementById('update-dropoff-date').value = dropoffDate.replace(' ', 'T').slice(0, 16);
        document.getElementById('update-payment-method').value = paymentMethod || 'cash';
        document.getElementById('update-status').value = status;
        document.getElementById('update-modal').style.display = 'flex';
    }

    function closeUpdateModal() {
        document.getElementById('update-modal').style.display = 'none';
    }

    function saveBooking() {
        const bookingId = document.getElementById('update-booking-id').value;
        const name = document.getElementById('update-name').value;
        const dropoffDate = document.getElementById('update-dropoff-date').value;
        const paymentMethod = document.getElementById('update-payment-method').value;
        const status = document.getElementById('update-status').value;

        if (!name || !dropoffDate) {
            alert('Name and Dropoff Date are required.');
            return;
        }

        const data = {
            action: 'update',
            booking_id: bookingId,
            name: name,
            dropoff_date: dropoffDate.replace('T', ' '),
            payment_method: paymentMethod,
            status: status
        };

        fetch('/MagicSoleProject/admin/order-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Booking updated successfully');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the booking.');
        });

        closeUpdateModal();
    }

    const spinner = document.getElementById('loading-spinner');
    spinner.style.display = 'none';
    changePage(0);
</script>
</body>
</html>