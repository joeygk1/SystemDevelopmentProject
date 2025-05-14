<?php
include_once "Controllers/Controller.php";
include_once "Models/Booking.php";
include_once "Models/Model.php";

class BookingController extends Controller{

    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "booking";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch($action){
            case "booking":
                if(empty($_POST)){
                    $this->render($controller,$action);
                }
                else{
                    $booking = new Booking();
                    $booking->bookAppointment();
                    $newUrl = dirname($path).'/booking/booking';
                    header("Location: ".$newUrl);
                }
                break;

            case "delete":
                $booking = new Booking($id);
                $booking->delete();
                $newUrl = dirname($path).'/client/client-orders';
                header("Location: ".$newUrl);
                break;

            case "about":
                $this->render($controller,$action);
                break;

            case "updateStatus":
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    try {
                        $input = json_decode(file_get_contents('php://input'), true);
                        if (isset($input['booking_id']) && isset($input['status'])) {
                            $bookingId = intval($input['booking_id']);
                            $status = trim($input['status']);
                            $booking = new Booking($bookingId);
                            $booking->update(['status' => $status]);
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Missing booking data.']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()]);
                    }
                    exit;
                }
                echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
                exit;
                break;

            case "policies":
                $this->render($controller,$action);
                break;

            case "gallery":
                $this->render($controller,$action);
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
                            echo json_encode(['success' => false, 'message' => 'Missing required fields (booking_id, name, dropoff_time, payment_method)']);
                            exit;
                        }

                        $bookingId = intval($input['booking_id']);
                        $booking = new Booking($bookingId);
                        if (!$booking->getBookingId()) {
                            http_response_code(404);
                            echo json_encode(['success' => false, 'message' => 'Booking not found']);
                            exit;
                        }

                        $result = $booking->updateClientDetails(
                            $input['name'],
                            $input['phone'],
                            $input['username'],
                            $input['dropoff_time'],
                            $input['payment_method']
                        );

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
                echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
                exit;
                break;
        }
    }
}