<?php
include_once "Models/Model.php";

class Client extends Model {
    private $clientId;
    private $firstName;
    private $lastName;
    private $password;
    private $email;
    private $phone;
    private $instagram;

    public function __construct($param = null){
        if (is_object($param)) {
            $this->setProperties($param);
        } elseif (is_int($param)) {
            $conn = Model::connect(); // Assumes PDO connection

            $sql = "SELECT * FROM `clients` WHERE `clientId` = :clientId;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':clientId', $param, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if ($row) {
                $this->setProperties($row);
            }
        }
    }

    public function setProperties($param = null){
        $this->clientId = $param->clientId;
        $this->firstName = $param->firstName;
        $this->lastName = $param->lastName;
        $this->password = $param->password;
        $this->email = $param->email;
        $this->phone = $param->phone;
        if (isset($param->instagram)) {
            $this->instagram = $param->instagram;
        }
        else{
            $this->instagram = null;
        }
    }

    public function getClientId(){
        return $this->clientId;
    }

    public function getFirstName(){
        return $this->firstName;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function getInstagram(){
        if ($this->instagram == "" || $this->instagram == null) {
            return null;
        }
        return $this->instagram;
    }

    public function login(){
        // Implement login logic
    }

    public function isClient($email){
        // Implement email check logic
    }

    public function view_bookings(){
        $arr = [];
        $conn = Model::connect();
        $sql = "SELECT * FROM `bookings` WHERE `client_id` = ?";
        $stmt = $conn->prepare($sql);
        $clientId = $_SESSION["user_id"];
        $stmt->bindParam(1, $clientId, PDO::PARAM_INT);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
            $booking = new Booking($row);
            array_push($arr, $booking);
        }
        return $arr;
    }
}
?>
