<?php

    class Controller {

        function route(){
        }

        function render($controller,$view,$data=[]){
            extract($data);

            include_once "Views/$controller/$view.php";
        }
    }


?>