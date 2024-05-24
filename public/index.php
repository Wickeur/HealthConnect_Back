<?php
header("Content-Type: application/json");
require_once 'config.php';
require_once 'functions.php';

// Vérifier si la méthode de la requête est autorisée
$allowedMethods = ['GET', 'POST', 'PUT'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Analyser l'URI pour déterminer l'endpoint demandé
$uriSegments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = $uriSegments[0];

// Définir les endpoints disponibles
$availableEndpoints = ['role', 'roles', 'user', 'users', 'medicalFile', 'RDV'];

// Vérifier si l'endpoint demandé est disponible
if (!in_array($endpoint, $availableEndpoints)) {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint Not Found']);
    exit;
}

// Exécuter les actions en fonction de l'endpoint demandé
switch ($endpoint) {
    case 'role':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uriSegments[1])) {
                $roleId = $uriSegments[1];
                $role = getRoleById($pdo, $roleId);
                if ($role) {
                    echo json_encode($role);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Role not found']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Role ID is required']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = getInputData();
            createRole($pdo, $data);
            http_response_code(201);
            echo json_encode(['message' => 'Role created successfully']);
        }
        break;
    case 'roles':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = getAllRoles($pdo);
            echo json_encode($roles);
        }
        break;
    case 'user':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uriSegments[1])) {
                $userId = $uriSegments[1];
                $user = getUserById($pdo, $userId);
                if ($user) {
                    echo json_encode($user);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'User not found']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = getInputData();
            createUser($pdo, $data);
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = getInputData();
            updateUser($pdo, $data);
            echo json_encode(['message' => 'User updated successfully']);
        }
        break;
    case 'users':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $users = getAllUsers($pdo);
            echo json_encode($users);
        }
        break;
    case 'medicalFile':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uriSegments[1])) {
                $userId = $uriSegments[1];
                $medicalFile = getMedicalFileById($pdo, $userId);
                if ($medicalFile) {
                    echo json_encode($medicalFile);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Medical File not found']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = getInputData();
            createMedicalFile($pdo, $data);
            http_response_code(201);
            echo json_encode(['message' => 'Medical File created successfully']);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = getInputData();
            updateMedicalFile($pdo, $data);
            echo json_encode(['message' => 'Medical File updated successfully']);
        }
        break;
    case 'RDV':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uriSegments[1])) {
                $rdvId = $uriSegments[1];
                $rdv = getRDVById($pdo, $rdvId);
                if ($rdv) {
                    echo json_encode($rdv);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'RDV not found']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'RDV ID is required']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = getInputData();
            createRDV($pdo, $data);
            http_response_code(201);
            echo json_encode(['message' => 'RDV created successfully']);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = getInputData();
            updateRDV($pdo, $data);
            echo json_encode(['message' => 'RDV updated successfully']);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
?>
