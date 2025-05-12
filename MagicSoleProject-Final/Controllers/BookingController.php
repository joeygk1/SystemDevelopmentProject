<?php

include_once "Controllers/Controller.php";

include_once "Models/Booking.php";
include_once "Models/Model.php";

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
                if(empty($_POST)){
                    $this->render($controller,$action);
                }
                else{

                    $booking = new Booking();
                    $booking->bookAppointment();
                    $newUrl = dirname($path).'/booking/booking';
                    header("Location: ".$newUrl);
                }
                break;

            case "delete":
                $booking = new Booking($id);
                $booking->delete();
//                echo "Booking attempt done"; break;
                $newUrl = dirname($path).'/client/client-orders';
                header("Location: ".$newUrl);

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