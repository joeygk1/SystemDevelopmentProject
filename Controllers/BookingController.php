<?php
include_once "Controllers/Controller.php";
include_once "Models/Booking.php";
include_once "Models/Model.php";

class BookingController extends Controller
{
    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "booking";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch($action){
            case "booking":
                if (empty($_POST)) {
                    $this->render($controller, $action);
                } else {
                    $booking = new Booking();
                    $booking->bookAppointment();
                    $newUrl = dirname($path) . '/booking/booking';
                    header("Location: " . $newUrl);
                }
                break;

            case "delete":
                $booking = new Booking($id);
                $booking->delete();
                $newUrl = dirname($path) . '/client/client-orders';
                header("Location: " . $newUrl);
                break;

            case "updateClientDetails":
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    try {
                        $input = json_decode(file_get_contents('php://input'), true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
                            exit;
                        }

                        if (!isset($input['booking_id'], $input['name'], $input['dropoff_time'], $input['payment_method']) ||
                            empty($input['booking_id']) || empty($input['name']) || empty($input['dropoff_time']) || empty($input['payment_method'])) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                            exit;
                        }

                        $bookingId = intval($input['booking_id']);
                        $booking = new Booking($bookingId);
                        if (!$booking->getBookingId()) {
                            http_response_code(404);
                            echo json_encode(['success' => false, 'message' => 'Booking not found']);
                            exit;
                        }

                        $currentDate = $booking->getDropoffDate();
                        $newDate = date('Y-m-d H:i:s', strtotime(str_replace(' ', ' ', $input['dropoff_time'], $currentDate)));
                        $result = $booking->update([
                            'name' => $input['name'],
                            'phone' => $input['phone'] ?? null,
                            'username' => $input['username'] ?? null,
                            'dropoff_date' => $newDate,
                            'payment_method' => $input['payment_method']
                        ]);

                        if ($result) {
                            echo json_encode(['success' => true]);
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
                        }
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
                    }
                    exit;
                }
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
                break;

            case "about":
            case "policies":
            case "gallery":
                $this->render($controller, $action);
                break;
        }
    }
}