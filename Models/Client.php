<?php
include_once "Model.php";
$path = $_SERVER['SCRIPT_NAME'];
require 'vendor/autoload.php';
require 'config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Client extends Model
{
    private $id;
    private $admin_id;
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
        $this->admin_id = $param->admin_id ?? null;
        $this->username = $param->username ?? null;
        $this->email = $param->email ?? null;
        $this->phone = $param->phone ?? null; // Map phone column
        $this->password = $param->password ?? null;
        $this->role = $param->role ?? null;
        $this->verification_code = $param->verification_code ?? null;
        $this->otp = $param->otp ?? null;
        $this->otp_expiry = $param->otp_expiry ?? null;
    }

    public function register($username, $email, $phone, $password)
    {
        try {
            $conn = Model::connect();
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO `users` (`username`, `email`, `phone`, `password`, `role`)
                    VALUES (:username, :email, :phone, :password, 'client')";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);

            file_put_contents('debug.log', "Client.php - Executing register query: $sql with params: username=$username, email=$email, phone=$phone at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute();
            $user_id = $conn->lastInsertId();
            file_put_contents('debug.log', "Client.php - User registered with ID: $user_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true, 'user_id' => $user_id];
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Client.php - PDOException in register: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateProfile($user_id, $data)
    {
        try {
            $conn = Model::connect();
            $sql = "UPDATE `users` SET `username` = :username, `phone` = :phone WHERE `id` = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
            $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

            file_put_contents('debug.log', "Client.php - Executing updateProfile query: $sql with params: " . print_r($data, true) . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            file_put_contents('debug.log', "Client.php - updateProfile affected $rowCount rows for user ID: $user_id at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => true];
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Client.php - PDOException in updateProfile: " . $e->getMessage() . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
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

    public function view_bookings()
    {
        $arr = [];
        $conn = Model::connect();
        $sql = "SELECT * FROM `bookings` WHERE `client_id` = ?";
        $stmt = $conn->prepare($sql);
        $clientId = $_SESSION["user_id"];
        $stmt->bindParam(1, $clientId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
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

    public function login(){



        $dotenv = Dotenv\Dotenv::createImmutable('config');
        $dotenv->load();

//        $error = '';

        $env_path = __DIR__ . '/.env';
        file_put_contents('debug.log', "Env file exists: " . (file_exists($env_path) ? 'Yes' : 'No') . "\n", FILE_APPEND);
        if (file_exists($env_path)) {
            file_put_contents('debug.log', "Env file contents: " . file_get_contents($env_path) . "\n", FILE_APPEND);
        }

        $gmail_username = $_ENV['GMAIL_USERNAME'] ?? '';
        $gmail_password = $_ENV['GMAIL_APP_PASSWORD'] ?? '';
        file_put_contents('debug.log', "GMAIL_USERNAME: $gmail_username\n", FILE_APPEND);
        file_put_contents('debug.log', "GMAIL_APP_PASSWORD: $gmail_password\n", FILE_APPEND);

        if (empty($gmail_username) || empty($gmail_password)) {
//            $error = 'Gmail credentials are missing in .env file. Please contact the administrator.';
            $_POST['error'] = "Gmail credentials are missing in .env file. Please contact the administrator.";
            file_put_contents('debug.log', "Error: Gmail credentials missing\n", FILE_APPEND);
            return false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
//                $error = 'Email and password are required';
                $_POST['error'] = 'Email and password are required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                $error = 'Invalid email format';
                $_POST['error'] = 'Invalid email format';
            } else {
                try {
                    $conn = Model::connect();
                    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        // Generate 6-digit OTP
                        $otp = sprintf("%06d", mt_rand(100000, 999999));
                        $_SESSION['2fa_user_id'] = $user['id'];
                        $_SESSION['2fa_otp'] = $otp;
                        $_SESSION['2fa_email'] = $user['email'];
                        $_SESSION['2fa_role'] = $user['role'];
                        $_SESSION['2fa_expires'] = time() + 600;

                        // Debug: Log OTP
                        file_put_contents('debug.log', "OTP generated: $otp for email: {$user['email']}\n", FILE_APPEND);

                        // Send OTP via Gmail SMTP
                        if (!empty($gmail_username) && !empty($gmail_password)) {
                            $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = $gmail_username;
                                $mail->Password = $gmail_password;
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                                $mail->Port = 465;
                                $mail->setFrom($gmail_username, 'Magic Sole');
                                $mail->addAddress($user['email']);
                                $mail->isHTML(true);
                                $mail->Subject = 'Your Magic Sole 2FA Code';
                                $mail->Body = "Hello {$user['username']},<br><br>Your one-time code is: <b>$otp</b><br>This code expires in 10 minutes.";
                                $mail->AltBody = "Hello {$user['username']},\n\nYour one-time code is: $otp\nThis code expires in 10 minutes.";
                                $mail->send();
                                file_put_contents('debug.log', "Email sent successfully to {$user['email']}\n", FILE_APPEND);

                                return true;
                            } catch (Exception $e) {
//                                $error = "Failed to send OTP. Please try again or contact support.";
                                $_POST['error'] = 'Failed to send OTP. Please try again or contact support.';
                                file_put_contents('debug.log', "PHPMailer Error: {$mail->ErrorInfo}\n", FILE_APPEND);
                            }
                        }
                    } else {
//                        $error = 'Invalid email or password';
                        $_POST['error'] = 'Invalid email or password';
                    }
                } catch (PDOException $e) {
//                    $error = 'Database error: ' . $e->getMessage();
                    $_POST['error'] = 'Database error: ' . $e->getMessage();
                    file_put_contents('debug.log', "Database error: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }
        }

        if (!empty($error)) {
            $_POST['error'] = $error;
            return false;
        }

        return false;

    }

    public function getId() { return $this->id; }
    public function getAdminId() { return $this->admin_id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getVerificationCode() { return $this->verification_code; }
}