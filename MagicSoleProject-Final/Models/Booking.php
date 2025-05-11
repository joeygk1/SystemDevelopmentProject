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
    }

    public function getBookingId()
    {
        return $this->bookingId;
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
}
