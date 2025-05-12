<?php
session_start();

// Check if the user is logged in and an admin
$isValidSession = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Output a simple response for .htaccess to evaluate
if ($isValidSession) {
    echo "valid";
} else {
    echo "invalid";
}
exit;
?>
