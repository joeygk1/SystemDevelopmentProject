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
    <title>Manage Bookings - Magic Sole Admin</title>
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
        .pagination button:disabled { background: #666; cursor: not-allowed; }
        .pagination button:hover:not(:disabled) { background: #333; }
        .pagination span { padding: 8px 16px; font-size: 1rem; color: #1a1a1a; }
        .loading-spinner { display: none; text-align: center; margin: 20px 0; }
        .loading-spinner i { font-size: 2rem; color: #d4af37; animation: spin 1s linear infinite; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; position: relative; opacity: 0; transform: scale(0.8); animation: modalFadeIn 0.3s forwards; }
        .modal-content h3 { font-size: 1.8rem; margin-bottom: 20px; color: #1a1a1a; }
        .modal-content .close-btn { position: absolute; top: 10px; right: 15px; font-size: 1.5rem; color: #1a1a1a; cursor: pointer; }
        .modal-content label { display: block; margin-bottom: 5px; font-weight: 500; color: #1a1a1a; }
        .modal-content input, .modal-content select { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; outline: none; }
        .modal-content input:focus, .modal-content select:focus { border: 1px solid #d4af37; }
        .modal-content input[readonly], .modal-content select[readonly] { background: #f5f5f5; cursor: not-allowed; }
        .modal-content button { padding: 10px 20px; background: #f9c303; color: #1a1a1a; border: none; border-radius: 5px; cursor: pointer; }
        .modal-content button:hover { background: #d4af37; }
        .service-checkboxes { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
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
        <a href="<?php echo dirname($path); ?>/admin/view-orders">View Bookings</a>
        <a href="<?php echo dirname($path); ?>/admin/order-status">Manage Bookings</a>
        <a href="<?php echo dirname($path); ?>/admin/admin-gallery">Manage Gallery</a>
        <a href="<?php echo dirname($path); ?>/admin/logout">Logout</a>
    </nav>
    <footer><p>© 2025 Magic Sole. All rights reserved.</p></footer>
</header>
<div class="main-content">
    <section class="hero"><div class="hero-content"><h1>Manage Bookings</h1><p>Update booking statuses here.</p></div></section>
    <div class="orders-section">
        <h2>All Bookings</h2>
        <div class="search-filter">
            <input type="text" id="search-input" placeholder="Search by Order ID, name, phone, or date...">
            <select id="status-filter">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <div class="loading-spinner" id="loading-spinner"><i class="fas fa-spinner"></i></div>
        <table class="orders-table" id="orders-table">
            <thead><tr><th>Order ID</th><th>Drop Off Date</th><th>Total</th><th>Status</th><th>Name</th><th>Phone</th><th>Username</th><th>Payment Method</th><th>Services</th><th>Actions</th></tr></thead>
            <tbody id="orders-tbody">
                <?php if (empty($data)) { ?>
                    <tr><td colspan="10">No bookings found.</td></tr>
                <?php } else { 
                    file_put_contents('debug.log', "order-status.php - Entering foreach loop, count: " . count($data) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    foreach ($data as $booking) { 
                        $phoneValue = $booking->getPhone();
                        $services = $booking->getServices();
                        $servicesStr = !empty($services) ? implode(', ', $services) : 'N/A';
                        file_put_contents('debug.log', "order-status.php - Rendering booking ID: " . $booking->getBookingId() . ", Phone: " . ($phoneValue !== null && $phoneValue !== '' ? $phoneValue : 'empty') . ", Username: " . ($booking->getUsername() ?: 'empty') . ", Services: " . $servicesStr . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                ?>
                    <tr>
                        <td data-order-id="<?php echo htmlspecialchars($booking->getBookingId()); ?>"><?php echo htmlspecialchars($booking->getBookingId()); ?></td>
                        <td data-date="<?php echo htmlspecialchars($booking->getDropoffDate()); ?>"><?php echo htmlspecialchars($booking->getDropoffDate()); ?></td>
                        <td data-total="<?php echo htmlspecialchars($booking->getTotalPrice()); ?>">$<?php echo number_format($booking->getTotalPrice(), 2); ?></td>
                        <td data-status="<?php echo htmlspecialchars($booking->getStatus()); ?>"><?php echo htmlspecialchars($booking->getStatus()); ?></td>
                        <td data-name="<?php echo htmlspecialchars($booking->getName()); ?>"><?php echo htmlspecialchars($booking->getName()); ?></td>
                        <td data-phone="<?php echo htmlspecialchars($phoneValue); ?>"><?php echo htmlspecialchars($phoneValue) ?: 'N/A'; ?></td>
                        <td data-username="<?php echo htmlspecialchars($booking->getUsername()); ?>"><?php echo htmlspecialchars($booking->getUsername()) ?: 'N/A'; ?></td>
                        <td data-payment-method="<?php echo htmlspecialchars($booking->getPaymentMethod()); ?>"><?php echo htmlspecialchars($booking->getPaymentMethod()) ?: 'N/A'; ?></td>
                        <td data-services="<?php echo htmlspecialchars($servicesStr); ?>"><?php echo htmlspecialchars($servicesStr); ?></td>
                        <td>
                            <button class="action-btn update-btn" onclick="openUpdateModal('<?php echo htmlspecialchars($booking->getBookingId()); ?>', '<?php echo htmlspecialchars($booking->getName()); ?>', '<?php echo htmlspecialchars($booking->getDropoffDate()); ?>', '<?php echo htmlspecialchars($booking->getPaymentMethod() ?: 'cash'); ?>', '<?php echo htmlspecialchars($booking->getStatus()); ?>', '<?php echo htmlspecialchars(json_encode($services)); ?>')">Update</button>
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
            <h3>Update Booking Status</h3>
            <label for="update-booking-id">Booking ID</label>
            <input type="text" id="update-booking-id" readonly>
            <label for="update-name">Name</label>
            <input type="text" id="update-name" readonly>
            <label for="update-dropoff-date">Dropoff Date</label>
            <input type="datetime-local" id="update-dropoff-date" readonly>
            <label for="update-payment-method">Payment Method</label>
            <select id="update-payment-method" disabled>
                <option value="cash">Cash</option>
                <option value="etransfer">E-Transfer</option>
            </select>
            <label for="update-status">Status</label>
            <select id="update-status">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            <label>Services</label>
            <div class="service-checkboxes">
                <label><input type="checkbox" name="services[]" value="cleaning" disabled> Cleaning ($50)</label>
                <label><input type="checkbox" name="services[]" value="repaint" disabled> Re-paint ($80)</label>
                <label><input type="checkbox" name="services[]" value="icysole" disabled> Icy-sole ($20)</label>
                <label><input type="checkbox" name="services[]" value="redye" disabled> Re-dye ($80)</label>
            </div>
            <label for="update-total">Total</label>
            <input type="text" id="update-total" readonly>
            <button onclick="saveBooking()">Save</button>
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

    function openUpdateModal(bookingId, name, dropoffDate, paymentMethod, status, servicesJson) {
        const services = JSON.parse(servicesJson);
        document.getElementById('update-booking-id').value = bookingId;
        document.getElementById('update-name').value = name;
        document.getElementById('update-dropoff-date').value = dropoffDate ? new Date(dropoffDate).toISOString().slice(0, 16) : '';
        document.getElementById('update-payment-method').value = paymentMethod || 'cash';
        document.getElementById('update-status').value = status;

        document.querySelectorAll('.service-checkboxes input').forEach(checkbox => {
            checkbox.checked = services.includes(checkbox.value);
        });

        document.getElementById('update-total').value = `$${parseFloat(document.querySelector('[data-order-id="' + bookingId + '"]')?.closest('tr')?.querySelector('[data-total]')?.getAttribute('data-total') || 0).toFixed(2)}`;

        document.getElementById('update-modal').style.display = 'flex';
    }

    function closeUpdateModal() {
        document.getElementById('update-modal').style.display = 'none';
    }

    function saveBooking() {
        const bookingId = document.getElementById('update-booking-id').value;
        const status = document.getElementById('update-status').value;
        const loadingSpinner = document.getElementById('loading-spinner');

        if (!status) {
            alert('Please select a status');
            return;
        }

        const data = {
            action: 'update',
            booking_id: bookingId,
            status: status
        };

        loadingSpinner.style.display = 'block';

        fetch('/MagicSoleProject/admin/order-status', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                });
            }
            return response.json();
        })
        .then(result => {
            loadingSpinner.style.display = 'none';
            if (result.success) {
                alert('Booking status updated successfully');
                location.reload();
            } else {
                console.error('Update failed:', result.message);
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            loadingSpinner.style.display = 'none';
            console.error('AJAX error:', error.message);
            alert('An error occurred: ' + error.message);
        });
    }

    document.getElementById('search-input').addEventListener('input', filterOrders);
    document.getElementById('status-filter').addEventListener('change', filterOrders);
    updatePagination();
</script>
</body>
</html>
<?php ob_end_flush(); ?>