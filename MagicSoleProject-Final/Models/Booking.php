<?php
include_once "Models/Model.php";

class Booking extends Model
{
    private $bookingId;
    private $clientId;
    private $dropoffDate;
    private $pickupDate;
    private $shoesQuantity;
    private $status;
    private $name;
    private $total;

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
        $this->bookingId = $param->booking_id;
        $this->clientId = $param->client_id;
        $this->dropoffDate = $param->dropoff_date;
        $this->pickupDate = $param->pickup_date;
        $this->shoesQuantity = $param->shoes_quantity;
        $this->status = $param->status;
        $this->name = $param->name;
        $this->total = $param->total_price;
    }

    public static function list() {
        $list = [];
        $sql = "SELECT b.`booking_id`, b.`client_id`, CONCAT(CONCAT(c.`firstName`,' '), c.`lastName`) AS name,
            b.`dropoff_date`, b.`status`, b.`shoes_quantity`, b.`pickup_date`, p.`total_price`
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
        return $this->bookingId;
    }
    public function getTotal()
    {
        return '$'.$this->total;
    }
    public function getName()
    {
        return $this->name;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getDropoffDate()
    {
        return $this->dropoffDate;
    }

    public function getPickupDate()
    {
        return $this->pickupDate;
    }

    public function getShoesQuantity()
    {
        return $this->shoesQuantity;
    }

    public function getStatus()
    {
        return $this->status;
    }

    function bookAppointment(){
        try {
            $conn = Model::connect();
            $sql = "INSERT INTO `bookings` (
                                        `client_id`,
                                        `dropoff_date`,
                                        `pickup_date`,
                                        `shoes_quantity`,
                                        `status`
                                    )   
                                    VALUES (
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
                "Pending"
            ]);
        }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }

    }
}
