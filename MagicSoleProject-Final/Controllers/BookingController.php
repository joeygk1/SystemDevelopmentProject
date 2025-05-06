<?php

include_once "Controllers/Controller.php";

include_once "Models/Booking.php";

class BookingController extends Controller{

    function route()
    {
        global $controller;
        $controller = ucfirst($controller);
        $path = $_SERVER['SCRIPT_NAME'];
        $action = isset($_GET['action']) ? $_GET['action'] : "booking";
        $id = isset($_GET['id']) ? intval($_GET['id']) : -1;

        switch($action){
            case "booking":
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




        }
    }
}