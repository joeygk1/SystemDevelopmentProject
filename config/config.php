<?php
// Database configuration for XAMPP localhost
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Default XAMPP MySQL user
define('DB_PASS', ''); // Default XAMPP MySQL password (empty)
define('DB_NAME', 'magicsole'); // Your database name

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>