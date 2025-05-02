<?php
// User.php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Save the verification code in the database
    public function saveVerificationCode($email, $code) {
        $stmt = $this->conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
        return $stmt->execute([$code, $email]);
    }

    // Retrieve the verification code from the database
    public function getVerificationCode($email) {
        $stmt = $this->conn->prepare("SELECT verification_code FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }
}
