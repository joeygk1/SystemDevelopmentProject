<?php
include_once "Models/Model.php";

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
    private $phone; 
    private $username;
    private $payment_method;

    public function __construct($param = null)
    {
        file_put_contents('debug.log', "Booking.php - Constructor called with param: " . (is_object($param) ? "object" : (is_int($param) ? $param : "null")) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        if (is_object($param)) {
            $this->setProperties($param);
        } elseif (is_int($param)) {
            $conn = Model::connect();
            $sql = "SELECT * FROM `bookings` WHERE `booking_id` = :bookingId";
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
        $this->client_id = $param->client_id ?? null; // client_id is id from users table
        $this->dropoff_date = $param->dropoff_date ?? null;
        $this->pickup_date = $param->pickup_date ?? null;
        $this->shoes_quantity = $param->shoes_quantity ?? 0;
        $this->status = $param->status ?? 'Pending';
        $this->name = $param->name ?? '';
        $this->total_Price = $param->total_Price ?? 0.00;
        $this->phone = $param->phone ?? '';
        $this->username = $param->username ?? ''; // Map users column to username
        $this->payment_method = $param->payment_method ?? '';
    }

    public static function list()
    {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, b.`name`, b.`dropoff_date`, b.`status`, 
                b.`shoes_quantity`, b.`pickup_date`, b.`total_Price`, 
                p.`payment_method`, u.`phone`, u.`username`
                FROM `bookings` b
                LEFT JOIN `payments` p ON b.booking_id = p.booking_id
                LEFT JOIN `users` u ON b.client_id = u.id
                WHERE b.client_id = :client_id
                ORDER BY b.booking_id DESC";
        $connection = Model::connect();
        $stmt = $connection->prepare($sql);
        $client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0;
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        file_put_contents('debug.log', "Booking.php - Executing list query: $sql with client_id: $client_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        try {
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $booking = new Booking($row);
                array_push($list, $booking);
            }
            file_put_contents('debug.log', "Booking.php - List result: " . print_r($list, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Booking.php - PDOException in list: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            throw $e;
        }
        return $list;
    }

    public function updateClientDetails($data)
{
    try {
        $conn = Model::connect();
        $conn->beginTransaction();

        // Update bookings table
        $sql = "UPDATE `bookings` SET 
                    `name` = :name,
                    `dropoff_date` = :dropoff_date
                WHERE `booking_id` = :booking_id AND `client_id` = :client_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':dropoff_date', $data['dropoff_date'], PDO::PARAM_STR);
        $stmt->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
        $stmt->bindValue(':client_id', $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0, PDO::PARAM_INT);
        $stmt->execute();

        // Update payments table (only if payment_method is provided)
        if (!empty($data['payment_method'])) {
            $sql_payment = "UPDATE `payments` SET 
                                `payment_method` = :payment_method 
                            WHERE `booking_id` = :booking_id";
            $stmt_payment = $conn->prepare($sql_payment);
            $stmt_payment->bindValue(':payment_method', $data['payment_method'], PDO::PARAM_STR);
            $stmt_payment->bindValue(':booking_id', $this->booking_id, PDO::PARAM_INT);
            $stmt_payment->execute();
        }

        $conn->commit();

        // Update object properties
        $this->name = $data['name'];
        $this->dropoff_date = $data['dropoff_date'];
        if (!empty($data['payment_method'])) {
            $this->payment_method = $data['payment_method'];
        }

        return ['success' => true, 'message' => 'Booking updated successfully'];

    } catch (PDOException $e) {
        $conn->rollBack();
        file_put_contents('debug.log', "Booking.php - PDOException in updateClientDetails: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
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
            $is_payed = 0; // Default: not paid
            $payment_date = date('Y-m-d'); // Current date
            $deposit_amount = (float)($_POST['depositAmount'] ?? 0.00); // Assuming form provides this

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
            if (isset($_POST['services']) && isset($_POST['prices']) && is_array($_POST['services']) && is_array($_POST['prices'])) {
                $services = $_POST['services'];
                $prices = $_POST['prices'];
                if (count($services) === count($prices)) {
                    foreach ($services as $index => $service_name) {
                        $price = (float)$prices[$index];
                        $sql_service = "INSERT INTO `services` (`service_name`, `price`) VALUES (?, ?)";
                        $stmt_service = $conn->prepare($sql_service);
                        $stmt_service->execute([$service_name, $price]);
                        $service_id = $conn->lastInsertId();
                        $sql_link = "INSERT INTO `booking_service` (`booking_id`, `service_id`) VALUES (?, ?)";
                        $stmt_link = $conn->prepare($sql_link);
                        $stmt_link->execute([$booking_id, $service_id]);
                    }
                } else {
                    throw new Exception("Mismatch between services and prices arrays.");
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
}