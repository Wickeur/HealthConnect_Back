<?php
header("Content-Type: application/json");
require_once '../config/config.php';
require_once '../src/ApiController.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$apiController = new ApiController($pdo);
$apiController->handleRequest($requestMethod, $requestUri);
?>