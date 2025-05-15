<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');

file_put_contents('debug.log', "updateClientDetails.php - Script reached at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

include_once "Models/Booking.php"; // Adjusted path: from /views/booking/ to /Models/

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents('debug.log', "updateClientDetails.php - Invalid request method: " . $_SERVER['REQUEST_METHOD'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$raw_input = file_get_contents('php://input');
file_put_contents('debug.log', "updateClientDetails.php - Raw input: $raw_input at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

$data = json_decode($raw_input, true);
if (!$data || !isset($data['booking_id']) || !isset($data['name']) || !isset($data['dropoff_time']) || !isset($data['payment_method'])) {
    file_put_contents('debug.log', "updateClientDetails.php - Invalid or missing data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data']);
    exit;
}

try {
    $booking = new Booking((int)$data['booking_id']);
    if (!$booking->getBookingId()) {
        throw new Exception('Booking not found');
    }

    $updateData = [
        'name' => $data['name'],
        'phone' => $data['phone'] ?? null,
        'username' => $data['username'] ?? null,
        'dropoff_date' => date('Y-m-d H:i:s', strtotime($data['dropoff_time'])),
        'payment_method' => $data['payment_method']
    ];
    file_put_contents('debug.log', "updateClientDetails.php - Updating with data: " . print_r($updateData, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    $response = $booking->updateClientDetails($updateData);
    file_put_contents('debug.log', "updateClientDetails.php - Update response: " . print_r($response, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode($response);
} catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
    file_put_contents('debug.log', "updateClientDetails.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error_message]);
} catch (Throwable $t) {
    $error_message = 'Fatal error: ' . $t->getMessage();
    file_put_contents('debug.log', "updateClientDetails.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error_message]);
}
exit;
?>