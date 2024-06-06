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
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
    exit;
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Extraction de la partie de l'URL après le domaine
$resource = $uri[1] ?? '';
$id = $uri[2] ?? null;

// Determine the controller to use
switch ($resource) {
    case 'role':
        $controller = new RoleController($conn, $requestMethod, $id);
        break;
    case 'user':
        $controller = new UserController($conn, $requestMethod, $id);
        break;
    case 'medicalFile':
        $controller = new MedicalFileController($conn, $requestMethod, $id);
        break;
    case 'RDV':
        $controller = new RDVController($conn, $requestMethod, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route non reconnue']);
        exit;
}

$controller->processRequest();
?>
