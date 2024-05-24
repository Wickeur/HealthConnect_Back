<?php
class RoleController {
    private $db;
    private $requestMethod;
    private $roleId;

    public function __construct($db, $requestMethod, $roleId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->roleId = $roleId;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->roleId) {
                    $response = $this->getRole($this->roleId);
                } else {
                    $response = $this->getAllRoles();
                };
                break;
            case 'POST':
                $response = $this->createRole();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllRoles()
    {
        // Implémentation pour récupérer tous les rôles
    }

    private function getRole($id)
    {
        // Implémentation pour récupérer un rôle spécifique
    }

    private function createRole()
    {
        // Implémentation pour créer un rôle
    }

    private function notFoundResponse()
    {
        return ['status_code_header' => 'HTTP/1.1 404 Not Found', 'body' => null];
    }
}

