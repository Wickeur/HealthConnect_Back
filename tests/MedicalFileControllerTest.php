<?php

use PHPUnit\Framework\TestCase;

class MedicalFileControllerTest extends TestCase {
    private $controller;
    private $db;

    protected function setUp(): void {
        $this->db = new PDO("sqlite::memory:");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table et insérer des données de test
        $this->db->exec("CREATE TABLE medical_files (id INTEGER PRIMARY KEY AUTOINCREMENT, idUser INTEGER, comment TEXT, content TEXT)");
        $this->db->exec("INSERT INTO medical_files (idUser, comment, content) VALUES (1, 'Initial comment', 'Initial content')");

        $this->controller = new MedicalFileController($this->db, 'GET', 1);
    }

    public function testGetMedicalFile() {
        $expected = json_encode(['id' => 1, 'idUser' => 1, 'comment' => 'Initial comment', 'content' => 'Initial content']);
        $result = $this->controller->getMedicalFile(1);
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertSame($expected, $result['body']);
    }

    public function testCreateMedicalFileFromRequest() {
        $newData = json_encode(['idUser' => 1, 'comment' => 'New comment', 'content' => 'New content']);
        $this->controller = new MedicalFileController($this->db, 'POST', null);
        $this->controller->createMedicalFileFromRequest();

        $statement = $this->db->query("SELECT * FROM medical_files WHERE id = 2");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('1', $result['idUser']);
        $this->assertSame('New comment', $result['comment']);
        $this->assertSame('New content', $result['content']);
    }

    public function testUpdateMedicalFileFromRequest() {
        $updatedData = json_encode(['comment' => 'Updated comment', 'content' => 'Updated content']);
        $this->controller = new MedicalFileController($this->db, 'PUT', 1);
        $this->controller->updateMedicalFileFromRequest(1);

        $statement = $this->db->query("SELECT * FROM medical_files WHERE id = 1");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('Updated comment', $result['comment']);
        $this->assertSame('Updated content', $result['content']);
    }

    public function testMedicalFileNotFound() {
        $this->controller = new MedicalFileController($this->db, 'GET', 999);
        $result = $this->controller->getMedicalFile(999);
        
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status_code_header']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
