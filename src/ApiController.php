<?php
class ApiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function handleRequest($method, $uri) {
        switch ($uri[0]) {
            case 'role':
                $this->handleRole($method, $uri);
                break;
            case 'roles':
                if ($method == 'GET') {
                    echo json_encode($this->getAllRoles());
                }
                break;
            case 'user':
                $this->handleUser($method, $uri);
                break;
            case 'users':
                if ($method == 'GET') {
                    echo json_encode($this->getAllUsers());
                }
                break;
            case 'medicalFile':
                $this->handleMedicalFile($method, $uri);
                break;
            case 'RDV':
                $this->handleRDV($method, $uri);
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Endpoint not found']);
                break;
        }
    }

    // Helper function to get input data
    private function getInputData() {
        return json_decode(file_get_contents("php://input"), true);
    }

    // Handle Role
    private function handleRole($method, $uri) {
        if ($method == 'GET' && isset($uri[1])) {
            echo json_encode($this->getRoleById($uri[1]));
        } elseif ($method == 'POST') {
            $data = $this->getInputData();
            $this->createRole($data);
        }
    }

    private function getRoleById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Role WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM Role");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function createRole($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Role (label) VALUES (?)");
        $stmt->execute([$data['label']]);
    }

    // Handle User
    private function handleUser($method, $uri) {
        if ($method == 'GET' && isset($uri[1])) {
            echo json_encode($this->getUserById($uri[1]));
        } elseif ($method == 'POST') {
            $data = $this->getInputData();
            $this->createUser($data);
        } elseif ($method == 'PUT') {
            $data = $this->getInputData();
            $this->updateUser($data);
        }
    }

    private function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO User (pseudo, mail, password, idRole) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['pseudo'], $data['mail'], $data['password'], $data['idRole']]);
    }

    private function updateUser($data) {
        $stmt = $this->pdo->prepare("UPDATE User SET pseudo = ?, mail = ?, password = ?, idRole = ? WHERE id = ?");
        $stmt->execute([$data['pseudo'], $data['mail'], $data['password'], $data['idRole'], $data['id']]);
    }

    // Handle MedicalFile
    private function handleMedicalFile($method, $uri) {
        if ($method == 'GET' && isset($uri[1])) {
            echo json_encode($this->getMedicalFileById($uri[1]));
        } elseif ($method == 'POST') {
            $data = $this->getInputData();
            $this->createMedicalFile($data);
        } elseif ($method == 'PUT') {
            $data = $this->getInputData();
            $this->updateMedicalFile($data);
        }
    }

    private function getMedicalFileById($idUser) {
        $stmt = $this->pdo->prepare("SELECT * FROM MedicalFile WHERE idUser = ?");
        $stmt->execute([$idUser]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function createMedicalFile($data) {
        $stmt = $this->pdo->prepare("INSERT INTO MedicalFile (idUser, comment, content) VALUES (?, ?, ?)");
        $stmt->execute([$data['idUser'], $data['comment'], $data['content']]);
    }

    private function updateMedicalFile($data) {
        $stmt = $this->pdo->prepare("UPDATE MedicalFile SET comment = ?, content = ? WHERE idUser = ?");
        $stmt->execute([$data['comment'], $data['content'], $data['idUser']]);
    }

    // Handle RDV
    private function handleRDV($method, $uri) {
        if ($method == 'GET' && isset($uri[1])) {
            echo json_encode($this->getRDVById($uri[1]));
        } elseif ($method == 'POST') {
            $data = $this->getInputData();
            $this->createRDV($data);
        } elseif ($method == 'PUT') {
            $data = $this->getInputData();
            $this->updateRDV($data);
        }
    }

    private function getRDVById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM RDV WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function createRDV($data) {
        $stmt = $this->pdo->prepare("INSERT INTO RDV (idUserClient, idUserMedecin, date, timeStart, timeEnd) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['idUserClient'], $data['idUserMedecin'], $data['date'], $data['timeStart'], $data['timeEnd']]);
    }

    private function updateRDV($data) {
        $stmt = $this->pdo->prepare("UPDATE RDV SET idUserClient = ?, idUserMedecin = ?, date = ?, timeStart = ?, timeEnd = ? WHERE id = ?");
        $stmt->execute([$data['idUserClient'], $data['idUserMedecin'], $data['date'], $data['timeStart'], $data['timeEnd'], $data['id']]);
    }
}
?>
