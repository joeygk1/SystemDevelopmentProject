<?php
session_start();

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'client';
$action = isset($_GET['action']) ? $_GET['action'] : 'index'; // Default action fallback

$controllerClassName = ucfirst($controller) . 'Controller';
$controllerFile = "Controllers/" . $controllerClassName . ".php";

if (file_exists($controllerFile)) {
    include_once $controllerFile;
    $ct = new $controllerClassName();

    if (method_exists($ct, 'route')) {
        $ct->route();
    } else {
        die("Route method not found in $controllerClassName.");
    }
} else {
    die("Controller $controllerClassName not found.");
}
?>
