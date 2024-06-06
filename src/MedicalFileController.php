<?php

class MedicalFileController {
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
                $response = $this->getMedicalFile($this->userId);
                break;
            case 'POST':
                $response = $this->createMedicalFileFromRequest();
                break;
            case 'PUT':
                $response = $this->updateMedicalFileFromRequest($this->userId);
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

    private function getMedicalFile($id)
    {
        $query = "
            SELECT * FROM medical_files
            WHERE idUser = ?;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([$id]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return $this->notFoundResponse();
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } catch (\PDOException $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function createMedicalFileFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateMedicalFile($input)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            INSERT INTO medical_files (idUser, comment, content)
            VALUES (?, ?, ?);
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['idUser'],
                $input['comment'],
                $input['content']
            ]);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
        } catch (\PDOException $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function updateMedicalFileFromRequest($id)
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateMedicalFile($input)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            UPDATE medical_files
            SET comment = ?, content = ?
            WHERE idUser = ?;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['comment'],
                $input['content'],
                $id
            ]);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
        } catch (\PDOException $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function validateMedicalFile($input)
    {
        if (!isset($input['idUser']) || !isset($input['comment']) || !isset($input['content'])) {
            return false;
        }
        return true;
    }

    private function errorResponse($message)
    {
        return [
            'status_code_header' => 'HTTP/1.1 500 Internal Server Error',
            'body' => json_encode(['error' => $message])
        ];
    }

    private function unprocessableEntityResponse()
    {
        return [
            'status_code_header' => 'HTTP/1.1 422 Unprocessable Entity',
            'body' => json_encode(['error' => 'Invalid input'])
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
?>
