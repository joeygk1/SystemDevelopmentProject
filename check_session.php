<?php
session_start();
header('Content-Type: application/json');

// Initialize response
$response = ['loggedIn' => false];

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $response = [
        'loggedIn' => true,
        'userId' => $_SESSION['user_id'],
        'isAdmin' => isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true,
        'clientEmail' => $_SESSION['clientEmail'] ?? null
    ];
}

// Output JSON and exit
echo json_encode($response);
exit;
?>