<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');

file_put_contents('debug.log', "delete.php - Script reached at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

include_once "Models/Booking.php"; /
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents('debug.log', "delete.php - Invalid request method: " . $_SERVER['REQUEST_METHOD'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null;
if (!$bookingId) {
    file_put_contents('debug.log', "delete.php - Invalid or missing booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing booking ID']);
    exit;
}

try {
    $booking = new Booking($bookingId);
    if (!$booking->getBookingId()) {
        throw new Exception('Booking not found');
    }

    file_put_contents('debug.log', "delete.php - Deleting booking ID: $bookingId at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    $response = $booking->delete();
    file_put_contents('debug.log', "delete.php - Delete response: " . print_r($response, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode($response);
} catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
    file_put_contents('debug.log', "delete.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error_message]);
} catch (Throwable $t) {
    $error_message = 'Fatal error: ' . $t->getMessage();
    file_put_contents('debug.log', "delete.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error_message]);
}
exit;
?>