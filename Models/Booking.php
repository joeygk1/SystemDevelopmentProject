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
        $this->client_id = $param->client_id ?? null;
        $this->dropoff_date = $param->dropoff_date ?? null;
        $this->pickup_date = $param->pickup_date ?? null;
        $this->shoes_quantity = $param->shoes_quantity ?? 0;
        $this->status = $param->status ?? 'Pending';
        $this->name = $param->name ?? '';
        $this->total_Price = $param->total_Price ?? 0.00;
        $this->phone = $param->phone ?? '';
        $this->username = $param->username ?? '';
        $this->payment_method = $param->payment_method ?? 'cash';
    }

    public static function list()
    {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, b.`name`, b.`dropoff_date`, b.`status`, 
                b.`shoes_quantity`, b.`pickup_date`, b.`total_Price`, b.`phone`, b.`username`, b.`payment_method`
                FROM `bookings` b
                WHERE b.client_id = :client_id
                ORDER BY b.booking_id DESC";
        $connection = Model::connect();
        $stmt = $connection->prepare($sql);
        $client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0;
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        file_put_contents('debug.log', "Booking.php - Executing list query: $sql with client_id: $client_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $booking = new Booking($row);
            array_push($list, $booking);
        }
        file_put_contents('debug.log', "Booking.php - List result: " . print_r($list, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        return $list;
    }

    public function update($newStatus)
    {
        $conn = Model::connect();
        $sql = "UPDATE `bookings` SET `status` = :status WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);
        $status = $newStatus['status'] ?? 'Pending';
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':bookingId', $this->booking_id, PDO::PARAM_INT);
        file_put_contents('debug.log', "Booking.php - Executing update query: $sql with status: $status, booking_id: {$this->booking_id} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        $stmt->execute();
    }

    public function updateClientDetails($data)
    {
        file_put_contents('debug.log', "Booking.php - updateClientDetails called with data: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        // Validate required fields
        if (!isset($data['booking_id']) || (int)$data['booking_id'] <= 0) {
            throw new Exception('Invalid or missing booking_id: ' . ($data['booking_id'] ?? 'null'));
        }
        if (!isset($data['name']) || empty(trim($data['name']))) {
            throw new Exception('Name is required');
        }
        if (!isset($data['dropoff_time']) || empty(trim($data['dropoff_time']))) {
            throw new Exception('Dropoff time is required');
        }
        if (!isset($data['payment_method']) || empty(trim($data['payment_method']))) {
            throw new Exception('Payment method is required');
        }

        $dropoffTime = strtotime($data['dropoff_time']);
        if ($dropoffTime === false || $dropoffTime === -1) {
            throw new Exception('Invalid dropoff time format: ' . ($data['dropoff_time'] ?? 'null'));
        }

        try {
            $conn = Model::connect();
            file_put_contents('debug.log', "Booking.php - Database connection established at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        } catch (PDOException $e) {
            throw new Exception('Failed to connect to database: ' . $e->getMessage());
        }

        $sql = "UPDATE `bookings` 
                SET `name` = :name, `phone` = :phone, `username` = :username, 
                    `dropoff_date` = :dropoff_date, `payment_method` = :payment_method 
                WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':username', $data['username'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':dropoff_date', date('Y-m-d H:i:s', $dropoffTime), PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $data['payment_method'], PDO::PARAM_STR);
        $stmt->bindValue(':bookingId', (int)$data['booking_id'], PDO::PARAM_INT);

        file_put_contents('debug.log', "Booking.php - Executing update query: $sql with params: name=" . ($data['name'] ?? 'null') . ", phone=" . ($data['phone'] ?? 'null') . ", username=" . ($data['username'] ?? 'null') . ", dropoff_date=" . date('Y-m-d H:i:s', $dropoffTime) . ", payment_method=" . ($data['payment_method'] ?? 'null') . ", booking_id=" . ($data['booking_id'] ?? 'null') . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        try {
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            file_put_contents('debug.log', "Booking.php - updateClientDetails affected $rowCount rows at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            if ($rowCount === 0) {
                throw new Exception('No rows updated. Booking ID ' . $data['booking_id'] . ' may not exist or data unchanged.');
            }
            file_put_contents('debug.log', "Booking.php - updateClientDetails completed successfully at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true, 'message' => 'Booking updated successfully'];
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
            file_put_contents('debug.log', "Booking.php - PDOException: $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            throw new Exception($error_message);
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

    public function delete()
    {
        $conn = Model::connect();
        file_put_contents('debug.log', "Booking.php - Deleting related records for booking_id: {$this->booking_id} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        $sql = "DELETE FROM `booking_services` WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':bookingId', $this->booking_id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM `payments` WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':bookingId', $this->booking_id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM `bookings` WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':bookingId', $this->booking_id, PDO::PARAM_INT);
        $stmt->execute();

        file_put_contents('debug.log', "Booking.php - Delete completed for booking_id: {$this->booking_id} at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        return ['success' => true, 'message' => 'Booking deleted successfully'];
    }

    public function bookAppointment()
    {
        try {
            $conn = Model::connect();
            $conn->beginTransaction();
            $sql = "INSERT INTO `bookings` (
                        `client_id`,
                        `dropoff_date`,
                        `pickup_date`,
                        `shoes_quantity`,
                        `status`,
                        `name`,
                        `total_Price`,
                        `phone`,
                        `username`,
                        `payment_method`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 0;
            $params = [
                $client_id,
                $_POST['date'] . ' ' . $_POST['timeSlot'],
                null,
                (int)$_POST['shoeCount'],
                "Pending",
                $_POST['name'] ?? '',
                (float)$_POST['totalCost'],
                $_POST['phoneNumber'] ?? '',
                $_POST['instagram'] ?? '',
                $_POST['payment'] ?? 'cash'
            ];
            file_put_contents('debug.log', "Booking.php - Booking data: " . print_r($params, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute($params);
            $booking_id = $conn->lastInsertId();
            $sql_payment = "INSERT INTO `payments` (`booking_id`, `total_Price`) VALUES (?, ?)";
            $stmt_payment = $conn->prepare($sql_payment);
            $stmt_payment->execute([$booking_id, (float)$_POST['totalCost']]);
            if (isset($_POST['services']) && isset($_POST['prices']) && is_array($_POST['services']) && is_array($_POST['prices'])) {
                $services = $_POST['services'];
                $prices = $_POST['prices'];
                if (count($services) === count($prices)) {
                    foreach ($services as $index => $service_name) {
                        $price = $prices[$index];
                        $sql_service = "INSERT INTO `services` (`service_name`, `price`) VALUES (?, ?)";
                        $stmt_service = $conn->prepare($sql_service);
                        $stmt_service->execute([$service_name, $price]);
                        $service_id = $conn->lastInsertId();
                        $sql_link = "INSERT INTO `booking_services` (`booking_id`, `service_id`) VALUES (?, ?)";
                        $stmt_link = $conn->prepare($sql_link);
                        $stmt_link->execute([$booking_id, $service_id]);
                    }
                } else {
                    throw new PDOException("Mismatch between services and prices arrays.");
                }
            }
            $conn->commit();
            file_put_contents('debug.log', "Booking.php - bookAppointment completed successfully for booking_id: $booking_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true, 'message' => 'Booking created successfully'];
        } catch (PDOException $e) {
            if ($conn) {
                $conn->rollBack();
            }
            $error_message = "Booking error: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            file_put_contents('debug.log', "Booking.php - $error_message at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => $error_message];
        }
    }
}