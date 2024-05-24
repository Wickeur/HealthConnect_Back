<?php
require_once 'config.php';

// Helper function to get input data
function getInputData() {
    $json = file_get_contents("php://input");
    return json_decode($json, true);
}

// Role Functions
function getRoleById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM Role WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllRoles($pdo) {
    $stmt = $pdo->query("SELECT * FROM Role");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createRole($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO Role (label) VALUES (?)");
    $stmt->execute([$data['label']]);
}

// User Functions
function getUserById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM User");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createUser($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO User (pseudo, mail, password, idRole) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['pseudo'], $data['mail'], $data['password'], $data['idRole']]);
}

function updateUser($pdo, $data) {
    $stmt = $pdo->prepare("UPDATE User SET pseudo = ?, mail = ?, password = ?, idRole = ? WHERE id = ?");
    $stmt->execute([$data['pseudo'], $data['mail'], $data['password'], $data['idRole'], $data['id']]);
}

// MedicalFile Functions
function getMedicalFileById($pdo, $idUser) {
    $stmt = $pdo->prepare("SELECT * FROM MedicalFile WHERE idUser = ?");
    $stmt->execute([$idUser]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createMedicalFile($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO MedicalFile (idUser, comment, content) VALUES (?, ?, ?)");
    $stmt->execute([$data['idUser'], $data['comment'], $data['content']]);
}

function updateMedicalFile($pdo, $data) {
    $stmt = $pdo->prepare("UPDATE MedicalFile SET comment = ?, content = ? WHERE idUser = ?");
    $stmt->execute([$data['comment'], $data['content'], $data['idUser']]);
}

// RDV Functions
function getRDVById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM RDV WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createRDV($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO RDV (idUserClient, idUserMedecin, date, timeStart, timeEnd) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$data['idUserClient'], $data['idUserMedecin'], $data['date'], $data['timeStart'], $data['timeEnd']]);
}

function updateRDV($pdo, $data) {
    $stmt = $pdo->prepare("UPDATE RDV SET idUserClient = ?, idUserMedecin = ?, date = ?, timeStart = ?, timeEnd = ? WHERE id = ?");
    $stmt->execute([$data['idUserClient'], $data['idUserMedecin'], $data['date'], $data['timeStart'], $data['timeEnd'], $data['id']]);
}

?>
