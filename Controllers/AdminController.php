<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once "Controllers/Controller.php";
include_once "Models/Admin.php";
include_once "Models/Booking.php";

// Set timezone to EDT
date_default_timezone_set('America/New_York');

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
                            if (!isset($data['booking_id']) || !is_numeric($data['booking_id']) || empty($data['status'])) {
                                file_put_contents('debug.log', "AdminController.php - Invalid or missing data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                throw new Exception("Missing required fields: booking_id or status");
                            }

                            $booking_id = (int)$data['booking_id'];
                            $status = trim($data['status']);
                            $validStatuses = ['Pending', 'Completed', 'Cancelled'];
                            if (!in_array($status, $validStatuses)) {
                                file_put_contents('debug.log', "AdminController.php - Invalid status: $status for booking ID: $booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                throw new Exception("Invalid status: $status");
                            }

                            $booking = new Booking($booking_id);
                            if (!$booking->getBookingId()) {
                                file_put_contents('debug.log', "AdminController.php - Booking not found for ID: $booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                throw new Exception("Booking not found");
                            }

                            file_put_contents('debug.log', "AdminController.php - Attempting to update booking ID: $booking_id with status: $status at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            $response = $booking->updateStatus($status);
                            if (!$response['success']) {
                                file_put_contents('debug.log', "AdminController.php - Update failed for booking ID: $booking_id, response: " . print_r($response, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                throw new Exception($response['message'] ?? "Update failed");
                            }

                            // Send email based on status
                            $email = $booking->getEmail();
                            if ($email) {
                                $mail = new PHPMailer(true);
                                try {
                                    $mail->isSMTP();
                                    $mail->Host = 'smtp.gmail.com';
                                    $mail->SMTPAuth = true;
                                    $mail->Username = 'joeyayoubdisalvo@gmail.com';
                                    $mail->Password = 'ijcbgdekkqiokahg';
                                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                    $mail->Port = 587;

                                    $mail->setFrom('no-reply@magicsole.com', 'Magic Sole');
                                    $mail->addAddress($email, $booking->getName());

                                    if ($status === 'Pending') {
                                        $mail->Subject = "Your Magic Sole Booking is Pending";
                                        $mail->Body = "Dear {$booking->getName()},\n\nYour booking (ID: {$booking->getBookingId()}) is Pending. We will notify you when it progresses. Thank you for choosing Magic Sole!\n\nBest regards,\nMagic Sole Team";
                                    } elseif ($status === 'Completed') {
                                        $mail->Subject = "Your Magic Sole Booking is Complete";
                                        $mail->Body = "Dear {$booking->getName()},\n\nYour booking (ID: {$booking->getBookingId()}) has been completed. Thank you for choosing Magic Sole!\n\nBest regards,\nMagic Sole Team";
                                    } elseif ($status === 'Cancelled') {
                                        $mail->Subject = "Your Magic Sole Booking has been Cancelled";
                                        $mail->Body = "Dear {$booking->getName()},\n\nYour booking (ID: {$booking->getBookingId()}) has been cancelled. If this was unintentional, please contact us. Thank you for your understanding.\n\nBest regards,\nMagic Sole Team";
                                    }

                                    $mail->send();
                                    file_put_contents('debug.log', "AdminController.php - Email sent to $email for booking ID: $booking_id with status: $status at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                } catch (Exception $e) {
                                    file_put_contents('debug.log', "AdminController.php - Email sending failed for $email: {$mail->ErrorInfo} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                }
                            } else {
                                file_put_contents('debug.log', "AdminController.php - No email found for booking ID: $booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            }

                            file_put_contents('debug.log', "AdminController.php - Update successful for booking ID: $booking_id, status: $status at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
                        } catch (Exception $e) {
                            $error = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
                            file_put_contents('debug.log', "AdminController.php - order-status error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            echo json_encode($error);
                        }
                    } else {
                        file_put_contents('debug.log', "AdminController.php - Invalid action received: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
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
?>