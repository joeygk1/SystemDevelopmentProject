<?php
// Clear all session data
$_SESSION = [];
session_destroy();

// Clear localStorage via JavaScript
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
        window.location.href = 'login.php';
    </script>
</body>
</html>
EOD;
exit;
?>