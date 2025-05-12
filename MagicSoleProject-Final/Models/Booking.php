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
        $this->booking_id = $param->booking_id;
        $this->client_id = $param->client_id;
        $this->dropoff_date = $param->dropoff_date;
        $this->pickup_date = $param->pickup_date;
        $this->shoes_quantity = $param->shoes_quantity;
        $this->status = $param->status;
        $this->name = $param->name;
        $this->total_Price = $param->total_Price;
    }

    public static function list() {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, CONCAT(CONCAT(c.`firstName`,' '), c.`lastName`) AS name,
            b.`dropoff_date`, b.`status`, b.`shoes_quantity`, b.`pickup_date`, p.`total_Price`
            FROM `bookings` b
            LEFT JOIN `clients` c ON (b.client_id = c.client_id )
            LEFT JOIN `payments` p ON (b.`booking_id` = p.`booking_id`)
            ORDER BY `booking_id` DESC";

        $connection = Model::connect();
        $stmt = $connection->query($sql);

        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $booking = new Booking($row);
            array_push($list, $booking);
        }

        return $list;
    }

    public function update($newStatus)
    {
        $conn = Model::connect();
        $sql = "UPDATE `bookings` SET `status` = :status WHERE `booking_id` = :bookingId";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':status', $newStatus['status'], PDO::PARAM_STR);
        $stmt->bindValue(':bookingId', $this->bookingId, PDO::PARAM_INT);

        $stmt->execute();
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

    public function delete()
    {
        $conn = Model::connect();

        $sql = "DELETE FROM `bookings` WHERE `booking_id` = ?";

        $stmt = $conn->prepare($sql);

        $stmt ->execute([$this->getBookingId()]);
    }
    function bookAppointment(){
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
                                    )   
                                    VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?
                                    ) ";
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
        }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }

    }
}
