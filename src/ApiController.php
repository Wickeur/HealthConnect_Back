<?php
class ApiController {
    public function processRequest($method, $uri) {
        switch ($method) {
            case 'GET':
                $this->handleGetRequest($uri);
                break;
            case 'POST':
                $this->handlePostRequest($uri);
                break;
            case 'PUT':
                $this->handlePutRequest($uri);
                break;
            case 'DELETE':
                $this->handleDeleteRequest($uri);
                break;
            default:
                $this->notFoundResponse();
                break;
        }
    }

    private function handleGetRequest($uri) {
        if ($uri[2] === 'items') {
            $this->getAllItems();
        } else {
            $this->notFoundResponse();
        }
    }

    private function handlePostRequest($uri) {
        // Implémentation des requêtes POST
    }

    private function handlePutRequest($uri) {
        // Implémentation des requêtes PUT
    }

    private function handleDeleteRequest($uri) {
        // Implémentation des requêtes DELETE
    }

    private function getAllItems() {
        // Exemple de réponse JSON pour une requête GET
        $response = [
            ["id" => 1, "name" => "Item 1"],
            ["id" => 2, "name" => "Item 2"]
        ];
        echo json_encode($response);
    }

    private function notFoundResponse() {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Not Found"]);
    }
}
