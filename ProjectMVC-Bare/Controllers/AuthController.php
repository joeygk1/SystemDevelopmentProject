<?php
// AuthController.php

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User(Database::getConnection());
    }

    // Handle user login
    public function login() {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Check if user exists and password is correct
            if ($this->userModel->checkLogin($email, $password)) {
                // Generate a random 6-digit verification code
                $code = rand(100000, 999999);
                
                // Save the code to the database
                $this->userModel->saveVerificationCode($email, $code);

                // Send verification code via email
                require '../Mailer/send_code.php';
                sendLoginCode($email, $code);

                // Store email in session and redirect to verify page
                $_SESSION['pending_user'] = $email;
                header('Location: /verify_code');
                exit;
            } else {
                echo "Invalid login credentials.";
            }
        }
    }

    // Verify the 2FA code
    public function verifyCode() {
        session_start();
        if (!isset($_SESSION['pending_user'])) {
            header('Location: /login');
            exit;
        }

        $email = $_SESSION['pending_user'];
        $inputCode = $_POST['code'];

        // Retrieve the verification code from the database
        $storedCode = $this->userModel->getVerificationCode($email);

        if ($inputCode === $storedCode) {
            unset($_SESSION['pending_user']);
            $_SESSION['user'] = $email;
            header('Location: /dashboard');
        } else {
            echo "Invalid code. Please try again.";
        }
    }
}
