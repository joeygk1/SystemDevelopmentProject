<?php
include_once "Controllers/Controller.php";
include_once "Models/Booking.php";
include_once "Models/Service.php";
require_once 'config/config.php'; // Changed to require_once

class AdminController extends Controller
{
    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "bookings";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['2fa_role'] ?? null;
        if ($role !== 'admin') {
            header('Location: ' . dirname($path) . '/client/login');
            exit;
        }

        $bookingModel = new Booking();

        switch ($action) {
            case "bookings":
                // Handle delete action
                if (isset($_GET['delete']) && $id > 0) {
                    $bookingModel->deleteBookingAdmin($id);
                    header('Location: ' . dirname($path) . '/admin/bookings');
                    exit;
                }
                // Handle update action (redirect to booking form)
                if (isset($_GET['update']) && $id > 0) {
                    header('Location: ' . dirname($path) . '/admin/update_booking?id=' . $id);
                    exit;
                }

                $bookings = $bookingModel->getAllBookings();
                $this->render($controller, $action, ['bookings' => $bookings]);
                break;

            case "update_booking":
                $serviceModel = new Service();
                $services = $serviceModel->getAllServices();
                $data = ['services' => $services];

                if ($id > 0) {
                    $booking = $bookingModel->getBookingByIdAdmin($id);
                    if ($booking) {
                        $data['booking'] = $booking;
                    } else {
                        $data['error'] = "Booking not found.";
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $dropoff_date = $_POST['dropoff_date'] ?? '';
                    $pickup_date = $_POST['pickup_date'] ?? null;
                    $shoes_quantity = intval($_POST['shoes_quantity'] ?? 1);
                    $service_ids = $_POST['services'] ?? [];
                    $shoe_names = $_POST['shoe_names'] ?? [];
                    $total_price = 0;

                    foreach ($service_ids as $service_id) {
                        $service = $serviceModel->getServiceById($service_id);
                        if ($service) {
                            $total_price += $service['price'];
                        }
                    }
                    $total_price *= $shoes_quantity;

                    $bookingModel->updateBookingAdmin($id, $dropoff_date, $pickup_date, $shoes_quantity, $total_price, $service_ids, $shoe_names);
                    $data['success'] = "Booking updated successfully!";
                    $data['services'] = $serviceModel->getAllServices();
                }

                $this->render($controller, $action, $data);
                break;

            case "verify_otp":
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
                    $submitted_otp = trim($_POST['otp']);
                    $stored_otp = $_SESSION['2fa_otp'] ?? '';
                    $expires = $_SESSION['2fa_expires'] ?? 0;

                    if (time() > $expires) {
                        $data['error'] = "OTP has expired. Please log in again.";
                        unset($_SESSION['2fa_otp']);
                        unset($_SESSION['2fa_expires']);
                    } elseif ($submitted_otp === $stored_otp) {
                        $_SESSION['token'] = bin2hex(random_bytes(32));
                        $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
                        $_SESSION['role'] = $_SESSION['2fa_role'];
                        unset($_SESSION['2fa_otp']);
                        unset($_SESSION['2fa_expires']);
                        header('Location: ' . dirname($path) . '/admin/bookings');
                        exit;
                    } else {
                        $data['error'] = "Invalid OTP. Please try again.";
                    }
                }
                $this->render($controller, $action, $data ?? []);
                break;
        }
    }
}
?>