<?php

require_once 'src/RoleController.php';

use PHPUnit\Framework\TestCase;

class RoleControllerTest extends TestCase {
    private $controller;
    private $db;

    protected function setUp(): void {
        $this->db = new PDO("sqlite::memory:");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création de la table et insertion de données pour le test
        $this->db->exec("CREATE TABLE roles (id INTEGER PRIMARY KEY AUTOINCREMENT, label TEXT)");
        $this->db->exec("INSERT INTO roles (label) VALUES ('Admin'), ('User')");

        $this->controller = new RoleController($this->db, 'GET', null);
    }

    public function testGetAllRoles() {
        $expected = json_encode([
            ['id' => 1, 'label' => 'Admin'],
            ['id' => 2, 'label' => 'User']
        ]);
        $result = $this->controller->getAllRoles();
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertSame($expected, $result['body']);
    }

    public function testGetRole() {
        $this->controller = new RoleController($this->db, 'GET', 1);
        $expected = json_encode(['id' => 1, 'label' => 'Admin']);
        $result = $this->controller->getRole(1);
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertSame($expected, $result['body']);
    }

    public function testRoleNotFound() {
        $this->controller = new RoleController($this->db, 'GET', 999);
        $result = $this->controller->getRole(999);
        
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status_code_header']);
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Not Found']), $result['body']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
