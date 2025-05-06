<?php
include_once  "Models/Model.php";

class Client extends Model{
    private $clientId;
    private $firstName;
    private $lastName;
    private $password;
    private $email;
    private $phone;
    private $instagram;

    public function __construct($param = null){
        if(is_object($param)){
            $this->setProperties($param);
        }
        elseif(is_int($param)){
            $conn = Model::connect();

            $sql = "SELECT * FROM `clients` WHERE `clientId` = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $param);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_object();
            $this->setProperties($row);
        }
    }

    public function setProperties($param = null){
        $this->clientId = $param->clientId;
        $this->firstName = $param->firstName;
        $this->lastName = $param->lastName;
        $this->password = $param->password;
        $this->email = $param->email;
        $this->phone = $param->phone;
        if(isset($param->instagram)){
            $this->instagram = $param->instagram;
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
        if($this->instagram == "" || $this->instagram == null){
            return null;
        }
        return $this->instagram;
    }

    public function login(){

    }
    function isClient($email){

    }
}

?>