<?php
session_start();
session_unset();
session_destroy();
echo "Session cleared. <a href='login.php'>Return to login</a>";
?>