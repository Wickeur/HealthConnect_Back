<?php

require_once 'src/MedicalFileController.php';

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

    public function testMedicalFileNotFound() {
        $this->controller = new MedicalFileController($this->db, 'GET', 999);
        $result = $this->controller->getMedicalFile(999);
        
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status_code_header']);
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Not Found']), $result['body']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
