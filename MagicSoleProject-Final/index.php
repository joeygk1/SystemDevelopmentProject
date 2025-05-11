
<?php
//include_once 'Models/Model.php';
//$conn = Model::connect();
//$username = "jaimejoe";
//$email = "billonesjaimejose@gmail.com";
//$password = "brand55";
//$hashed_password = password_hash($password, PASSWORD_BCRYPT);
//$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
//$stmt->execute([$username, $email, $hashed_password]);

session_start();
 $controller = (isset($_GET['controller'])) ? $_GET['controller'] : 'client';

 $controllerClassName = ucfirst($controller) . 'Controller';
 include_once "Controllers/" . $controllerClassName . ".php";
 $ct = new $controllerClassName();
$ct->route();



?>