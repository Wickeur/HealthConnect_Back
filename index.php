<?php

require 'Database.php';
require 'RoleController.php';
require 'UserController.php';
require 'MedicalFileController.php';
require 'RDVController.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$id = null;
if (isset($uri[3])) {
    $id = (int) $uri[3];
}

$database = new Database();
$db = $database->getConnection();

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Determine the controller to use
switch ($uri[2]) {
    case 'role':
        $controller = new RoleController($db, $requestMethod, $id);
        break;
    case 'user':
        $controller = new UserController($db, $requestMethod, $id);
        break;
    case 'medicalFile':
        $controller = new MedicalFileController($db, $requestMethod, $id);
        break;
    case 'RDV':
        $controller = new RDVController($db, $requestMethod, $id);
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit;
}

$controller->processRequest();

