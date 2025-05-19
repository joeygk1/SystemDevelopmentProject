<?php
include_once "Model.php";

class Booking extends Model
{
    private $booking_id;
    private $client_id;
    private $dropoff_date;
    private $pickup_date;
    private $shoes_quantity;
    private $status;
    private $name;
    private $total_Price;
    private $payment_method;
    private $username; // From users table
    private $phone; // From users table
    private $services; // Array of services

    public function __construct($param = null)
    {
        file_put_contents('debug.log', "Booking.php - Constructor called with param: " . (is_object($param) ? "object" : (is_int($param) ? $param : "null")) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        if (is_object($param)) {
            $this->setProperties($param);
        } elseif (is_int($param)) {
            $conn = Model::connect();
            $sql = "SELECT b.*, u.username, u.phone, GROUP_CONCAT(s.service_name) as services 
                    FROM `bookings` b 
                    LEFT JOIN `users` u ON b.client_id = u.id 
                    LEFT JOIN `booking_service` bs ON b.booking_id = bs.booking_id 
                    LEFT JOIN `services` s ON bs.service_id = s.service_id 
                    WHERE b.`booking_id` = :bookingId 
                    GROUP BY b.booking_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":bookingId", $param, PDO::PARAM_INT);
            file_put_contents('debug.log', "Booking.php - Executing query: $sql with booking_id: $param at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if ($row) {
                file_put_contents('debug.log', "Booking.php - Found booking: " . print_r($row, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $this->setProperties($row);
            } else {
                file_put_contents('debug.log', "Booking.php - No booking found for booking_id: $param at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            }
        }
    }

    public function setProperties($param = null)
    {
        file_put_contents('debug.log', "Booking.php - setProperties called with param: " . print_r($param, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        $this->booking_id = $param->booking_id ?? null;
        $this->client_id = $param->client_id ?? null;
        $this->dropoff_date = $param->dropoff_date ?? null;
        $this->pickup_date = $param->pickup_date ?? null;
        $this->shoes_quantity = $param->shoes_quantity ?? 0;
        $this->status = $param->status ?? 'Pending';
        $this->name = $param->name ?? '';
        $this->total_Price = $param->total_Price ?? 0.00;
        $this->payment_method = $param->payment_method ?? '';
        $this->username = $param->username ?? '';
        $this->phone = $param->phone ?? '';
        $this->services = !empty($param->services) ? explode(',', $param->services) : [];
        file_put_contents('debug.log', "Booking.php - setProperties assigned phone: " . ($this->phone !== '' ? $this->phone : 'empty') . ", username: " . ($this->username !== '' ? $this->username : 'empty') . ", services: " . json_encode($this->services) . " for booking_id: " . ($this->booking_id ?? 'unknown') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    }

    public static function list()
    {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, b.`name`, b.`dropoff_date`, b.`status`, 
                b.`shoes_quantity`, b.`pickup_date`, b.`total_Price`, 
                u.`username`, u.`phone`, p.`payment_method`,
                GROUP_CONCAT(s.service_name) as services
                FROM `bookings` b
                LEFT JOIN `payments` p ON b.booking_id = p.booking_id
                LEFT JOIN `users` u ON b.client_id = u.id
                LEFT JOIN `booking_service` bs ON b.booking_id = bs.booking_id
                LEFT JOIN `services` s ON bs.service_id = s.service_id
                WHERE b.client_id = :client_id
                GROUP BY b.booking_id
                ORDER BY b.booking_id DESC";
        $connection = Model::connect();
        $stmt = $connection->prepare($sql);
        $client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0;
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        file_put_contents('debug.log', "Booking.php - Executing list query: $sql with client_id: $client_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        try {
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                file_put_contents('debug.log', "Booking.php - Fetched row with booking_id: " . ($row->booking_id ?? 'unknown') . ", client_id: " . ($row->client_id ?? 'unknown') . ", phone: " . ($row->phone !== '' ? $row->phone : 'empty') . ", username: " . ($row->username !== '' ? $row->username : 'empty') . ", services: " . ($row->services ?? 'none') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $booking = new Booking();
                $booking->setProperties($row);
                array_push($list, $booking);
            }
            file_put_contents('debug.log', "Booking.php - List result: " . count($list) . " bookings at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Booking.php - PDOException in list: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            throw $e;
        }
        return $list;
    }

    public static function listAll()
    {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, b.`name`, b.`dropoff_date`, b.`status`, 
                b.`shoes_quantity`, b.`pickup_date`, b.`total_Price`, 
                u.`username`, u.`phone`, p.`payment_method`,
                GROUP_CONCAT(s.service_name) as services
                FROM `bookings` b
                LEFT JOIN `payments` p ON b.booking_id = p.booking_id
                LEFT JOIN `users` u ON b.client_id = u.id
                LEFT JOIN `booking_service` bs ON b.booking_id = bs.booking_id
                LEFT JOIN `services` s ON bs.service_id = s.service_id
                GROUP BY b.booking_id
                ORDER BY b.booking_id DESC";
        $connection = Model::connect();
        $stmt = $connection->prepare($sql);
        file_put_contents('debug.log', "Booking.php - Executing listAll query: $sql at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        try {
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                file_put_contents('debug.log', "Booking.php - Fetched row with booking_id: " . ($row->booking_id ?? 'unknown') . ", client_id: " . ($row->client_id ?? 'unknown') . ", phone: " . ($row->phone !== '' ? $row->phone : 'empty') . ", username: " . ($row->username !== '' ? $row->username : 'empty') . ", services: " . ($row->services ?? 'none') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                $booking = new Booking();
                $booking->setProperties($row);
                array_push($list, $booking);
            }
            file_put_contents('debug.log', "Booking.php - listAll result: " . count($list) . " bookings at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Booking.php - PDOException in listAll: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            throw $e;
        }
        return $list;
    }

    public function updateClientDetails($data)
    {
        try {
            $conn = Model::connect();
            $conn->beginTransaction();

            // Validate status
            $validStatuses = ['Pending', 'Completed', 'Cancelled'];
            if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
                throw new Exception("Invalid status: {$data['status']}");
            }

            // Update bookings table
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            $sql = "UPDATE `bookings` SET 
                        `name` = :name,
                        `dropoff_date` = :dropoff_date,
                        `status` = :status,
                        `total_Price` = :total_Price
                    WHERE `booking_id` = :booking_id" . ($isAdmin ? "" : " AND `client_id` = :client_id");
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':dropoff_date', $data['dropoff_date'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'] ?? $this->status, PDO::PARAM_STR);
            $stmt->bindValue(':total_Price', $data['total_Price'] ?? $this->total_Price, PDO::PARAM_STR);
            $stmt->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
            if (!$isAdmin) {
                $stmt->bindValue(':client_id', $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0, PDO::PARAM_INT);
            }
            $stmt->execute();

            // Update users table (phone and username)
            if (!empty($data['phone']) || !empty($data['username'])) {
                $sql_user = "UPDATE `users` SET 
                                `phone` = :phone,
                                `username` = :username
                             WHERE `id` = :client_id";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bindValue(':phone', $data['phone'] ?? $this->phone, PDO::PARAM_STR);
                $stmt_user->bindValue(':username', $data['username'] ?? $this->username, PDO::PARAM_STR);
                $stmt_user->bindValue(':client_id', $this->client_id, PDO::PARAM_INT);
                $stmt_user->execute();
            }

            // Update payments table (if payment_method provided)
            if (!empty($data['payment_method'])) {
                $sql_payment = "UPDATE `payments` SET 
                                    `payment_method` = :payment_method,
                                    `total_price` = :total_price
                                WHERE `booking_id` = :booking_id";
                $stmt_payment = $conn->prepare($sql_payment);
                $stmt_payment->bindValue(':payment_method', $data['payment_method'], PDO::PARAM_STR);
                $stmt_payment->bindValue(':total_price', $data['total_Price'] ?? $this->total_Price, PDO::PARAM_STR);
                $stmt_payment->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
                $stmt_payment->execute();
            }

            // Update services (delete existing and insert new)
            if (isset($data['services']) && is_array($data['services'])) {
                // Delete existing services
                $sql_delete = "DELETE FROM `booking_service` WHERE `booking_id` = :booking_id";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
                $stmt_delete->execute();

                // Insert new services
                foreach ($data['services'] as $service_name) {
                    // Check if service exists
                    $sql_service = "SELECT `service_id` FROM `services` WHERE `service_name` = :service_name";
                    $stmt_service = $conn->prepare($sql_service);
                    $stmt_service->bindValue(':service_name', $service_name, PDO::PARAM_STR);
                    $stmt_service->execute();
                    $service_id = $stmt_service->fetchColumn();

                    if (!$service_id) {
                        // Insert new service (use price from booking form logic)
                        $servicePrices = ['cleaning' => 50, 'repaint' => 80, 'icysole' => 20, 'redye' => 80];
                        $price = $servicePrices[$service_name] ?? 0;
                        $sql_insert_service = "INSERT INTO `services` (`service_name`, `price`) VALUES (:service_name, :price)";
                        $stmt_insert_service = $conn->prepare($sql_insert_service);
                        $stmt_insert_service->bindValue(':service_name', $service_name, PDO::PARAM_STR);
                        $stmt_insert_service->bindValue(':price', $price, PDO::PARAM_STR);
                        $stmt_insert_service->execute();
                        $service_id = $conn->lastInsertId();
                    }

                    // Link service to booking
                    $sql_link = "INSERT INTO `booking_service` (`booking_id`, `service_id`) VALUES (:booking_id, :service_id)";
                    $stmt_link = $conn->prepare($sql_link);
                    $stmt_link->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
                    $stmt_link->bindValue(':service_id', $service_id, PDO::PARAM_INT);
                    $stmt_link->execute();
                }
            }

            $conn->commit();

            // Update object properties
            $this->name = $data['name'];
            $this->dropoff_date = $data['dropoff_date'];
            $this->status = $data['status'] ?? $this->status;
            $this->total_Price = $data['total_Price'] ?? $this->total_Price;
            if (!empty($data['payment_method'])) {
                $this->payment_method = $data['payment_method'];
            }
            if (!empty($data['phone'])) {
                $this->phone = $data['phone'];
            }
            if (!empty($data['username'])) {
                $this->username = $data['username'];
            }
            if (isset($data['services']) && is_array($data['services'])) {
                $this->services = $data['services'];
            }

            file_put_contents('debug.log', "Booking.php - updateClientDetails successful for booking_id: {$this->booking_id}, phone updated: " . ($data['phone'] ?? 'not provided') . ", username updated: " . ($data['username'] ?? 'not provided') . ", services updated: " . json_encode($data['services'] ?? []) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true, 'message' => 'Booking updated successfully'];

        } catch (Exception $e) {
            $conn->rollBack();
            file_put_contents('debug.log', "Booking.php - Exception in updateClientDetails: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function delete()
    {
        try {
            $conn = Model::connect();
            $sql = "DELETE FROM `bookings` WHERE `booking_id` = :booking_id AND `client_id` = :client_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
            $stmt->bindValue(':client_id', $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0, PDO::PARAM_INT);

            file_put_contents('debug.log', "Booking.php - Executing delete query: $sql for booking ID: $this->booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            file_put_contents('debug.log', "Booking.php - Delete affected $rowCount rows for booking ID: $this->booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

            if ($rowCount > 0) {
                return ['success' => true, 'message' => 'Booking deleted successfully'];
            }
            return ['success' => false, 'message' => 'Booking not found or not authorized'];
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Booking.php - PDOException in delete: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function bookAppointment()
    {
        try {
            $conn = Model::connect();
            $conn->beginTransaction();

            // Insert into bookings table
            $sql = "INSERT INTO `bookings` (
                        `client_id`,
                        `dropoff_date`,
                        `pickup_date`,
                        `shoes_quantity`,
                        `status`,
                        `name`,
                        `total_Price`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0;
            $dropoff_date = $_POST['date'] . ' ' . $_POST['timeSlot'];
            $shoes_quantity = (int)($_POST['shoeCount'] ?? 1);
            $name = trim($_POST['name'] ?? '');
            $total_price = (float)($_POST['totalCost'] ?? 0.00);
            // Log phone and username if sent, but don't store in bookings
            $phone = trim($_POST['phone'] ?? '');
            $username = trim($_POST['username'] ?? '');

            // Log POST data for debugging
            file_put_contents('debug.log', "Booking.php - bookAppointment POST data: " . print_r($_POST, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            if (!empty($phone)) {
                file_put_contents('debug.log', "Booking.php - Received phone: $phone (not stored in bookings) at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            }
            if (!empty($username)) {
                file_put_contents('debug.log', "Booking.php - Received username: $username (not stored in bookings) at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            }

            // Validate required fields
            if ($client_id <= 0) {
                throw new Exception("Invalid client ID");
            }
            if (empty($dropoff_date) || !strtotime($dropoff_date)) {
                throw new Exception("Invalid dropoff date/time");
            }
            if ($shoes_quantity <= 0) {
                throw new Exception("Shoes quantity must be at least 1");
            }
            if (empty($name)) {
                throw new Exception("Name is required");
            }
            if ($total_price <= 0) {
                throw new Exception("Total price must be greater than 0");
            }

            $params = [
                $client_id,
                $dropoff_date,
                null, // pickup_date
                $shoes_quantity,
                "Pending",
                $name,
                $total_price
            ];
            file_put_contents('debug.log', "Booking.php - Booking data: " . print_r($params, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute($params);
            $booking_id = $conn->lastInsertId();

            // Insert into payments table
            $sql_payment = "INSERT INTO `payments` (
                                `booking_id`,
                                `payment_method`,
                                `is_payed`,
                                `total_price`,
                                `payment_date`,
                                `deposit_amount`
                            ) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_payment = $conn->prepare($sql_payment);
            $payment_method = trim($_POST['payment'] ?? 'cash');
            $is_payed = 0;
            $payment_date = date('Y-m-d');
            $deposit_amount = (float)($_POST['depositAmount'] ?? 0.00);

            // Validate payment fields
            if (!in_array($payment_method, ['cash', 'etransfer'])) {
                throw new Exception("Invalid payment method");
            }
            if ($deposit_amount < 0) {
                throw new Exception("Deposit amount cannot be negative");
            }

            $payment_params = [
                $booking_id,
                $payment_method,
                $is_payed,
                $total_price,
                $payment_date,
                $deposit_amount
            ];
            file_put_contents('debug.log', "Booking.php - Payment data: " . print_r($payment_params, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt_payment->execute($payment_params);

            // Insert services and link to booking_service
            $services = [];
            for ($i = 1; $i <= $shoes_quantity; $i++) {
                if (isset($_POST['services_' . $i]) && is_array($_POST['services_' . $i])) {
                    $services = array_merge($services, $_POST['services_' . $i]);
                }
            }
            if (!empty($services)) {
                $servicePrices = ['cleaning' => 50, 'repaint' => 80, 'icysole' => 20, 'redye' => 80];
                foreach ($services as $service_name) {
                    $price = $servicePrices[$service_name] ?? 0;
                    $sql_service = "INSERT INTO `services` (`service_name`, `price`) VALUES (?, ?)";
                    $stmt_service = $conn->prepare($sql_service);
                    $stmt_service->execute([$service_name, $price]);
                    $service_id = $conn->lastInsertId();
                    $sql_link = "INSERT INTO `booking_service` (`booking_id`, `service_id`) VALUES (?, ?)";
                    $stmt_link = $conn->prepare($sql_link);
                    $stmt_link->execute([$booking_id, $service_id]);
                }
            }

            $conn->commit();
            file_put_contents('debug.log', "Booking.php - bookAppointment completed successfully for booking_id: $booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true, 'message' => 'Booking and payment created successfully'];
        } catch (Exception $e) {
            if ($conn) {
                $conn->rollBack();
            }
            $error_message = "Booking error: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            file_put_contents('debug.log', "Booking.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => $error_message];
        }
    }

    public function getBookingId()
    {
        return $this->booking_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getDropoffDate()
    {
        return $this->dropoff_date;
    }

    public function getPickupDate()
    {
        return $this->pickup_date;
    }

    public function getShoesQuantity()
    {
        return $this->shoes_quantity;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getTotalPrice()
    {
        return $this->total_Price;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    public function getServices()
    {
        return $this->services;
    }
}
?>