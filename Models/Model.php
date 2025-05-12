<?php
class Model {
    public static function connect() {
        $server = "localhost";
        $user = "root";
        $pass = "";
        $db = "magicsole";

        try {
            $connect = new PDO("mysql:host=$server;dbname=$db", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Optional: Set default fetch mode
            ]);
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }

        return $connect;
    }
}
?>
