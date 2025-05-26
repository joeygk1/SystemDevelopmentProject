<?php

include_once "Controllers/Controller.php";

include_once "Models/Client.php";
include_once "Models/Booking.php";

class ClientController extends Controller{

    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "home";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch($action){
            case "home":
                $this->render($controller,$action);
                break;

            case "services":
                $this->render($controller,$action);

                break;

            case "about":
                $this->render($controller,$action);

                break;

            case "policies":
                $this->render($controller,$action);

                break;

            case "gallery":
                $this->render($controller,$action);

                break;
            case "login":
                if(isset($_POST['email']) && isset($_POST['password'])){

                }
                $this->render($controller,$action);

                break;
            case "client-orders":
                $client = new Client();
                $data = $client->view_bookings();
                $this->render($controller,$action,$data);

                break;
            case "register":
                $this->render($controller,$action);

                break;
            case "help":
                $file = 'UserGuides/ClientGuide.pdf';

                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="'.$file.'"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');
                readfile($file);
                break;
            case "logout":
    session_start(); // Always start session before modifying it

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

    // Clear localStorage and redirect via JS
    $home = dirname($_SERVER['SCRIPT_NAME']) . '/client/login';

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
            window.location.href = "$home";
        </script>
    </body>
    </html>
    EOD;

    exit;

        }
    }
}