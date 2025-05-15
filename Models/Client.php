<?php
include_once "Model.php";

class Client extends Model
{
    private $id;
    private $admin_id;
    private $username;
    private $email;
    private $password;
    private $role;
    private $verification_code;
    private $otp;
    private $otp_expiry;

    public function __construct($param = null)
    {
        if (is_object($param)) {
            $this->setProperties($param);
        } elseif (is_int($param)) {
            $conn = Model::connect();
            $sql = "SELECT * FROM `users` WHERE `id` = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $param, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if ($row) {
                $this->setProperties($row);
            }
        }
    }

    public function setProperties($param = null)
    {
        $this->id = $param->id ?? null;
        $this->admin_id = $param->admin_id ?? null;
        $this->username = $param->username ?? null;
        $this->email = $param->email ?? null;
        $this->password = $param->password ?? null;
        $this->role = $param->role ?? null;
        $this->verification_code = $param->verification_code ?? null;
        $this->otp = $param->otp ?? null;
        $this->otp_expiry = $param->otp_expiry ?? null;
    }

    public function getClientByEmail($email)
    {
        $conn = Model::connect();
        $sql = "SELECT * FROM `users` WHERE `email` = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row) {
            $this->setProperties($row);
            error_log("Found client: " . $this->getEmail());
            return $this;
        }
        error_log("No client found for email: " . $email);
        return null;
    }

    public function updateOtp($clientId, $otp, $otp_expiry)
    {
        $conn = Model::connect();
        $sql = "UPDATE `users` SET `otp` = :otp, `otp_expiry` = :otp_expiry WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":otp", $otp, PDO::PARAM_STR);
        $stmt->bindParam(":otp_expiry", $otp_expiry, PDO::PARAM_STR);
        $stmt->bindParam(":id", $clientId, PDO::PARAM_INT);
        $stmt->execute();
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

    public function verifyOtp($clientId, $otp)
    {
        $conn = Model::connect();
        $sql = "SELECT * FROM `users` WHERE `id` = :id AND `otp` = :otp";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $clientId, PDO::PARAM_INT);
        $stmt->bindParam(":otp", $otp, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        if ($row) {
            $this->setProperties($row);
            $currentTime = date('Y-m-d H:i:s');
            if (strtotime($currentTime) <= strtotime($row->otp_expiry)) {
                $sql = "UPDATE `users` SET `otp` = NULL, `otp_expiry` = NULL WHERE `id` = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":id", $clientId, PDO::PARAM_INT);
                $stmt->execute();
                return true;
            }
        }
        return false;
    }

    public function getId() { return $this->id; }
    public function getAdminId() { return $this->admin_id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getVerificationCode() { return $this->verification_code; }
}