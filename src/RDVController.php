<?php

class RDVController {
    private $db;
    private $requestMethod;
    private $rdvId;

    public function __construct($db, $requestMethod, $rdvId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->rdvId = $rdvId;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->rdvId) {
                    $response = $this->getRDV($this->rdvId);
                } else {
                    $response = $this->getAllRDVs();
                }
                break;
            case 'POST':
                $response = $this->createRDVFromRequest();
                break;
            case 'PUT':
                $response = $this->updateRDVFromRequest($this->rdvId);
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

    private function getAllRDVs()
    {
        $query = "
            SELECT id, idUserClient, idUserMedecin, date, timeStart, timeEnd
            FROM rdvs;
        ";

        try {
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $response = [
                'status_code_header' => 'HTTP/1.1 200 OK',
                'body' => json_encode($result)
            ];
        } catch (\PDOException $e) {
            $response = $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function getRDV($id)
    {
        $query = "
            SELECT id, idUserClient, idUserMedecin, date, timeStart, timeEnd
            FROM rdvs
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([$id]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return $this->notFoundResponse();
            }

            $response = [
                'status_code_header' => 'HTTP/1.1 200 OK',
                'body' => json_encode($result)
            ];
        } catch (\PDOException $e) {
            $response = $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function createRDVFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateRDV($input)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            INSERT INTO rdvs (idUserClient, idUserMedecin, date, timeStart, timeEnd)
            VALUES (?, ?, ?, ?, ?);
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['idUserClient'],
                $input['idUserMedecin'],
                $input['date'],
                $input['timeStart'],
                $input['timeEnd']
            ]);
            $response = [
                'status_code_header' => 'HTTP/1.1 201 Created',
                'body' => null
            ];
        } catch (\PDOException $e) {
            $response = $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function updateRDVFromRequest($id)
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateRDV($input)) {
            return $this->unprocessableEntityResponse();
        }

        $query = "
            UPDATE rdvs
            SET idUserClient = ?, idUserMedecin = ?, date = ?, timeStart = ?, timeEnd = ?
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                $input['idUserClient'],
                $input['idUserMedecin'],
                $input['date'],
                $input['timeStart'],
                $input['timeEnd'],
                $id
            ]);
            $response = [
                'status_code_header' => 'HTTP/1.1 200 OK',
                'body' => null
            ];
        } catch (\PDOException $e) {
            $response = $this->errorResponse($e->getMessage());
        }

        return $response;
    }

    private function validateRDV($input)
    {
        if (!isset($input['idUserClient']) || !isset($input['idUserMedecin']) ||
            !isset($input['date']) || !isset($input['timeStart']) || !isset($input['timeEnd'])) {
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
