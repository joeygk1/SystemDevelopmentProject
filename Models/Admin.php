<?php
include_once "Model.php";
require 'vendor/autoload.php';
require 'config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Admin extends Model
{
    private $id;
    private $username;
    private $email;
    private $phone;
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
        $this->username = $param->username ?? null;
        $this->email = $param->email ?? null;
        $this->phone = $param->phone ?? null; // Map phone column
        $this->password = $param->password ?? null;
        $this->role = $param->role ?? null;
        $this->verification_code = $param->verification_code ?? null;
        $this->otp = $param->otp ?? null;
        $this->otp_expiry = $param->otp_expiry ?? null;
    }


    static public function verifyOtp()
    {

        $path = $_SERVER['SCRIPT_NAME'];


        $email = $_SESSION['2fa_email'] ?? 'your email';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

            $otp = trim($_POST['otp'] ?? '');
            file_put_contents('debug.log', "OTP entered: $otp\n", FILE_APPEND);

            if (empty($otp)) {
                $error = 'OTP is required';
                $_POST['error'] = 'OTP is required';
                file_put_contents('debug.log', "Error: OTP empty\n", FILE_APPEND);
            } elseif (time() > $_SESSION['2fa_expires']) {
                $error = 'OTP has expired. Please try again.';
                unset($_SESSION['2fa_user_id'], $_SESSION['2fa_otp'], $_SESSION['2fa_email'], $_SESSION['2fa_role'], $_SESSION['2fa_expires']);
                file_put_contents('debug.log', "Error: OTP expired\n", FILE_APPEND);
                $_POST['error'] = 'OTP expired. Please try again.';
                header('Location:'.dirname($path).'/client/login');
                exit;
            } elseif ($otp === $_SESSION['2fa_otp']) {
                // Successful login
                $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
                $_SESSION['role'] = $_SESSION['2fa_role'];
                $email = $_SESSION['2fa_email'];

                $isAdmin = ($_SESSION['role'] === 'admin');
                $clientEmail = ($isAdmin ? '' : $email);

                // Clear 2FA session data
                unset($_SESSION['2fa_user_id'], $_SESSION['2fa_otp'], $_SESSION['2fa_email'], $_SESSION['2fa_role'], $_SESSION['2fa_expires']);
                file_put_contents('debug.log', "Login successful for $email\n", FILE_APPEND);

                $redirectUrl = $isAdmin ? dirname($path).'/admin/admin-home' : dirname($path).'/client/home';
                $isAdminJs = $isAdmin ? 'true' : 'false';
                $_SESSION['token'] = $_SESSION['user_id'];
                echo <<<EOD
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Redirecting...</title>
            </head>
            <body>
                <script>
                    const isAdmin = '$isAdminJs';
                    const clientEmail = '$clientEmail';
            
                    if (isAdmin === 'true') {
                        localStorage.setItem('isAdmin', 'true');
                        localStorage.removeItem('clientEmail');
                    } else {
                        localStorage.setItem('clientEmail', clientEmail);
                        localStorage.removeItem('isAdmin');
                    }
            
                    window.location.href = '$redirectUrl';
                </script>
            </body>
            </html>
        EOD;

            } else {
                $error = 'Invalid OTP';
                $_POST['error'] = 'Invalid OTP';
                file_put_contents('debug.log', "Error: Invalid OTP\n", FILE_APPEND);
            }
        }
    }



    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getVerificationCode() { return $this->verification_code; }
}