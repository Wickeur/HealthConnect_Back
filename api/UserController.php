<?php

class UserController {
    private $db;
    private $requestMethod;
    private $userId;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
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

    private function getAllUsers()
    {
        $query = "
            SELECT id, pseudo, mail, password, idRole 
            FROM users;
        ";

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

    public function getUser($id)
    {
        $query = "
            SELECT id, pseudo, mail, password, idRole 
            FROM users
            WHERE id = ?;
        ";

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

    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            INSERT INTO users
            (pseudo, mail, password, idRole)
            VALUES (?, ?, ?, ?);
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['pseudo'],
                $input['mail'],
                password_hash($input['password'], PASSWORD_DEFAULT),
                $input['idRole']
            ]);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
        } catch (\PDOException $e) {
            $response = $this->errorResponse();
        }

        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateUser($input, $id)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            UPDATE users
            SET pseudo = ?, mail = ?, password = ?, idRole = ?
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['pseudo'],
                $input['mail'],
                password_hash($input['password'], PASSWORD_DEFAULT),
                $input['idRole'],
                $id
            ]);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
        } catch (\PDOException $e) {
            $response = $this->errorResponse();
        }

        return $response;
    }

    private function validateUser($input, $id = null)
    {
        if (isset($id)) {
            if (!is_numeric($id)) {
                return false;
            }
        }
        if (!isset($input['pseudo']) || !isset($input['mail']) || !isset($input['password']) || !isset($input['idRole'])) {
            return false;
        }
        return true;
    }

    private function errorResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 500 Internal Server Error',
            'body' => null
        ];
    }

    private function unprocessableEntityResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 422 Unprocessable Entity',
            'body' => json_encode([
                'error' => 'Invalid input'
            ])
        ];
    }

    private function notFoundResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 404 Not Found',
            'body' => json_encode([
                'error' => 'Not Found'
            ])
        ];
    }
}
