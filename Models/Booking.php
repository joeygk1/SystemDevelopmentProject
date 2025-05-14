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
        if (is_object($param)) {
            $this->setProperties($param);
        } elseif (is_int($param)) {
            $conn = Model::connect();

            $sql = "SELECT * FROM `bookings` WHERE `booking_id` = :bookingId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":bookingId", $param, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if ($row) {
                $this->setProperties($row);
            }
        }
    }

    public function setProperties($param = null)
    {
        $this->booking_id = $param->booking_id ?? null;
        $this->client_id = $param->client_id ?? null;
        $this->dropoff_date = $param->dropoff_date ?? null;
        $this->pickup_date = $param->pickup_date ?? null;
        $this->shoes_quantity = $param->shoes_quantity ?? null;
        $this->status = $param->status ?? null;
        $this->name = property_exists($param, 'name') ? $param->name : null;
        $this->total_Price = property_exists($param, 'total_Price') ? $param->total_Price : null;
        $this->phone = property_exists($param, 'phone') ? $param->phone : null;
        $this->username = property_exists($param, 'username') ? $param->username : null;
        $this->payment_method = property_exists($param, 'payment_method') ? $param->payment_method : 'cash';
    }

    public function updateBookingClientSide($bookingId, $date, $items, $total)
    {
        try {
            $conn = Model::connect();
            $sql = "UPDATE bookings SET dropoff_date = ?, shoes_quantity = ?, total_Price = ? WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$date, $items, $total, $bookingId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function list() {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, CONCAT(CONCAT(c.`firstName`,' '), c.`lastName`) AS name,
            b.`dropoff_date`, b.`status`, b.`shoes_quantity`, b.`pickup_date`, p.`total_Price`,
            b.`phone`, b.`username`, b.`payment_method`
            FROM `bookings` b
            LEFT JOIN `clients` c ON (b.client_id = c.client_id)
            LEFT JOIN `payments` p ON (b.`booking_id` = p.`booking_id`)
            WHERE b.`client_id` = :clientId
            ORDER BY `booking_id` DESC";

        $connection = Model::connect();
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':clientId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $booking = new Booking($row);
            array_push($list, $booking);
        }

        return $list;
    }

    public function update($newStatus)
    {
        try {
            $conn = Model::connect();
            $sql = "UPDATE `bookings` SET `status` = :status WHERE `booking_id` = :bookingId";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':status', $newStatus['status'], PDO::PARAM_STR);
            $stmt->bindValue(':bookingId', $this->booking_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in Booking::update(): " . $e->getMessage());
            throw $e;
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

    public function getPhone() {
        return $this->phone ?? null;
    }

    public function getUsername() {
        return $this->username ?? null;
    }

    public function getPaymentMethod() {
        return $this->payment_method ?? 'cash';
    }

    public function delete()
    {
        $conn = Model::connect();

        $sql = "DELETE FROM `bookings` WHERE `booking_id` = ?";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$this->getBookingId()]);
    }

    public function bookAppointment()
{
    header('Content-Type: application/json');

    try {
        $conn = Model::connect();
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
        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['date'] . ' ' . $_POST['timeSlot'],
            null,
            $_POST['shoeCount'],
            "Pending",
            $_POST['name'],
            $_POST['totalCost']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Booking created successfully."
        ]);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            "success" => false,
            "error" => $e->getMessage()
        ]);
    }
}

}