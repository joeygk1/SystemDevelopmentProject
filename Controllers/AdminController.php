<?php
include_once "Controllers/Controller.php";
include_once "Models/Admin.php";
include_once "Models/Booking.php";

class AdminController extends Controller
{
    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "home";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch ($action) {
            case "admin-home":
                $this->render($controller, $action);
                break;

            case "order-status":
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    header('Content-Type: application/json');
                    $raw_input = file_get_contents('php://input');
                    $data = json_decode($raw_input, true);
                    file_put_contents('debug.log', "AdminController.php - order-status POST: Received data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

                    if (isset($data['action']) && $data['action'] === 'update') {
                        try {
                            if (!isset($data['booking_id']) || !isset($data['name']) || !isset($data['dropoff_date']) || !isset($data['payment_method']) || !isset($data['status'])) {
                                throw new Exception("Missing required fields");
                            }
                            $booking = new Booking((int)$data['booking_id']);
                            if (!$booking->getBookingId()) {
                                throw new Exception("Booking not found");
                            }
                            $updateData = [
                                'name' => $data['name'],
                                'dropoff_date' => date('Y-m-d H:i:s', strtotime($data['dropoff_date'])),
                                'payment_method' => $data['payment_method'],
                                'status' => $data['status']
                            ];
                            $response = $booking->updateClientDetails($updateData);
                            file_put_contents('debug.log', "AdminController.php - order-status update response: " . print_r($response, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            echo json_encode($response);
                        } catch (Exception $e) {
                            $error = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
                            file_put_contents('debug.log', "AdminController.php - order-status error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            echo json_encode($error);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid action']);
                    }
                    exit;
                }
                $data = Booking::listAll();
                file_put_contents('debug.log', "AdminController.php - order-status: Fetched " . count($data) . " bookings at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $this->render($controller, $action, $data);
                break;

            case "view-orders":
                $data = Booking::listAll();
                file_put_contents('debug.log', "AdminController.php - view-orders: Fetched " . count($data) . " bookings at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $this->render($controller, $action, $data);
                break;

            case "admin-gallery":
                $this->render($controller, $action);
                break;

            case "verify_otp":
                $this->render($controller, $action);
                break;

            case "logout":
                $_SESSION = [];
                session_destroy();
                $home = dirname($path) . '/client/home';
                echo <<<EOD
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Logging Out...</title>
                </head>
                <body>
                    <script>
                        localStorage.removeItem('isAdmin');
                        localStorage.removeItem('clientEmail');
                        window.location.href = "$home";
                    </script>
                </body>
                </html>
                EOD;
                break;

            default:
                $this->render($controller, 'error', ['message' => 'Invalid action']);
                break;
        }
    }
}