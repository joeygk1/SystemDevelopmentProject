<?php
class Model{

    public static function connect(){
        $server = "localhost";
        $user = "root";
        $pass = "";
        $db = "magicsole";

        $connect = new mysqli($server, $user, $pass, $db);

        if($connect->connect_error){
            die("Connection error! I can't deal with this anymore<br>" . $connect->connect_error);
        }
        return $connect;
    }

}

?>
