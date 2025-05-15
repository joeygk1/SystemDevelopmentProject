<?php
session_start();
include_once "Models/Booking.php";

file_put_contents('debug.log', "updateStatus.php - Script reached at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents('debug.log', "updateStatus.php - Invalid request method: " . $_SERVER['REQUEST_METHOD'] . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method: ' . $_SERVER['REQUEST_METHOD']]);
    exit;
}

$raw_input = file_get_contents('php://input');
file_put_contents('debug.log', "updateStatus.php - Raw input: $raw_input at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

$data = json_decode($raw_input, true);
if (!$data || !isset($data['booking_id']) || !isset($data['status'])) {
    file_put_contents('debug.log', "updateStatus.php - Invalid or missing data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data']);
    exit;
}

try {
    $booking = new Booking((int)$data['booking_id']);
    if (!$booking->getBookingId()) {
        throw new Exception('Booking not found for booking_id: ' . $data['booking_id']);
    }
    $booking->update(['status' => $data['status']]);
    file_put_contents('debug.log', "updateStatus.php - Status updated for booking_id: {$data['booking_id']} to {$data['status']} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    file_put_contents('debug.log', "updateStatus.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $error_message]);
}
exit;
?>