<?php
session_start();

// Clear all session data
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Clear localStorage and redirect via JavaScript
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logging Out...</title>
</head>
<body>
    <script>
        localStorage.removeItem('isAdmin');
        localStorage.removeItem('clientEmail');
        window.location.href = 'login.php'; // Adjust this path if needed
    </script>
</body>
</html>
EOD;
exit;
