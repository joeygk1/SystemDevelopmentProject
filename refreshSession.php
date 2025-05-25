<?php
require_once 'session_config.php';
session_start();

// Function to log debug information
function debugLog($message) {
    $logFile = 'debug.log';
    $currentTime = date('Y-m-d H:i:s');
    $logMessage = "[$currentTime] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

debugLog("refresh_session.php called");
debugLog("Session ID: " . session_id());
debugLog("Session Data before refresh: " . json_encode($_SESSION));

// Check if session data exists
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // Update session last activity time
    $_SESSION['last_activity'] = time();
    debugLog("Session refreshed successfully");
    debugLog("Session Data after refresh: " . json_encode($_SESSION));
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    debugLog("Session data missing, cannot refresh");
    header('Content-Type: application/json');
    echo json_encode(['success' => false]);
}
?>
