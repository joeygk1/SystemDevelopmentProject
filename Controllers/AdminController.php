<?php

include_once "Controllers/Controller.php";

include_once "Models/Admin.php";

class AdminController extends Controller{

    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "home";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch($action){
            case "admin-home":
                $this->render($controller,$action);
                break;

            case "order-status":
                $this->render($controller,$action);

                break;

            case "view-orders":
                $this->render($controller,$action);

                break;

            case "admin-gallery":
                $this->render($controller,$action);

                break;
            case "verify_otp":
                $this->render($controller,$action);

                break;
            case "logout":

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
                        // window.location.href = 'login.php';
                    </script>
                </body>
                </html>
                EOD;
                header('Location:'.dirname($path).'/client/login');
                break;

        }
    }
}