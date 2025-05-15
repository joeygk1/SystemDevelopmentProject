<?php
session_start();

// Clear session data
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirect to login page
header('Location: /MagicSoleProject/client/login');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Magic Sole</title>
    <script>
        // Client-side redirect as a fallback
        setTimeout(() => {
            window.location.href = "/MagicSoleProject/client/login";
        }, 1000);
    </script>
</head>
<body>
    <p>Logging you out...</p>
</body>
</html>