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
                $response = $this->createRoleFromRequest();
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
        $query = "SELECT id, label FROM roles;";
        try {
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } catch (\PDOException $e) {
            $response = $this->errorResponse();
        }
        return $response;
    }

    private function getRole($id)
    {
        $query = "SELECT id, label FROM roles WHERE id = ?;";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array($id));
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return $this->notFoundResponse();
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } catch (\PDOException $e) {
            $response = $this->errorResponse();
        }
        return $response;
    }

    private function createRoleFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!isset($input['label'])) {
            return $this->unprocessableEntityResponse();
        }

        $query = "INSERT INTO roles (label) VALUES (?);";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array($input['label']));
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
        } catch (\PDOException $e) {
            $response = $this->errorResponse();
        }
        return $response;
    }

    private function errorResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 500 Internal Server Error',
            'body' => json_encode(['error' => 'Internal Server Error'])
        ];
    }

    private function unprocessableEntityResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 422 Unprocessable Entity',
            'body' => json_encode(['error' => 'Invalid input or missing data'])
        ];
    }

    private function notFoundResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 404 Not Found',
            'body' => json_encode(['error' => 'Not Found'])
        ];
    }
}
