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





        }
    }
}