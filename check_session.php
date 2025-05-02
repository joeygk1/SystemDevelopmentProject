<?php
session_start();

$logMessage = function($message) {
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] $message\n", FILE_APPEND);
};

$logMessage("check_session.php called");
$logMessage("Session ID: " . session_id());
$logMessage("Session Data: " . json_encode($_SESSION));

$response = ['loggedIn' => false, 'isAdmin' => false];

// Check if session variables are set for login and admin role
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $response['loggedIn'] = true;
    $response['isAdmin'] = true;
}

$logMessage("Logged In: " . ($response['loggedIn'] ? 'true' : 'false'));
$logMessage("Is Admin: " . ($response['isAdmin'] ? 'true' : 'false'));

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>