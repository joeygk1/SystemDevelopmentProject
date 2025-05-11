<?php

include_once "Controllers/Controller.php";

include_once "Models/Client.php";

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
                $this->render($controller,$action);

                break;
            case "register":
                $this->render($controller,$action);

                break;




        }
    }
}