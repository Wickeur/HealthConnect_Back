<?php
header("Content-Type: application/json");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'api') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

require_once "../src/ApiController.php";

$controller = new ApiController();
$controller->processRequest($_SERVER["REQUEST_METHOD"], $uri);
