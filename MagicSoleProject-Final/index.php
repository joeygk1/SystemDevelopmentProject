
<?php

session_start();
$_SESSION['token'] = null;
 $controller = (isset($_GET['controller'])) ? $_GET['controller'] : 'client';

 $controllerClassName = ucfirst($controller) . 'Controller';
 include_once "Controllers/" . $controllerClassName . ".php";
 $ct = new $controllerClassName();
$ct->route();



?>