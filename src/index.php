<?php

require 'RoleController.php';
require 'UserController.php';
require 'MedicalFileController.php';
require 'RDVController.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Connexion à la base de données
$servername = "healthconnect_db";
$username = "user";
$password = "password";
$dbname = "healthconnect";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
}

// Récupération des données à partir de la base de données en fonction de l'URL demandée
$uri = $_SERVER['REQUEST_URI'];

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Determine the controller to use
switch ($uri) {
    case '/role':
        $controller = new RoleController($db, $requestMethod, $id);
        break;
    case '/user':
        $controller = new UserController($db, $requestMethod, $id);
        break;
    case '/medicalFile':
        $controller = new MedicalFileController($db, $requestMethod, $id);
        break;
    case '/RDV':
        $controller = new RDVController($db, $requestMethod, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route non reconnue']);
        break;
}

$controller->processRequest();
