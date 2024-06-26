<?php

require_once 'src/UserController.php';

use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {
    private $controller;
    private $db;

    protected function setUp(): void {
        $this->db = new PDO("sqlite::memory:");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table et insérer des données de test
        $this->db->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pseudo VARCHAR(255),
            mail VARCHAR(255),
            password VARCHAR(255),
            idRole INTEGER
        )");
        $this->db->exec("INSERT INTO users (pseudo, mail, password, idRole) VALUES ('testuser', 'user@example.com', 'hashedpassword', 1)");

        $this->controller = new UserController($this->db, 'GET', 1);
    }

    public function testGetUser() {
        $expected = json_encode([
            'id' => 1,
            'pseudo' => 'testuser',
            'mail' => 'user@example.com',
            'password' => 'hashedpassword',
            'idRole' => 1
        ]);
        $result = $this->controller->getUser(1);
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertSame($expected, $result['body']);
    }

    public function testUserNotFound() {
        $this->controller = new UserController($this->db, 'GET', 999);
        $result = $this->controller->getUser(999);
        
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status_code_header']);
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Not Found']), $result['body']);
    }

    public function testCreateUser() {
        $this->controller = new UserController($this->db, 'POST', null);
        
        $input = [
            'pseudo' => 'newuser',
            'mail' => 'newuser@example.com',
            'password' => 'newpassword',
            'idRole' => 2
        ];
        
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, json_encode($input));
        rewind($stream);
        file_put_contents('php://input', stream_get_contents($stream));
        
        $result = $this->controller->createUserFromRequest();
        
        $this->assertSame('HTTP/1.1 201 Created', $result['status_code_header']);
        $this->assertNull($result['body']);
    }

    public function testUpdateUser() {
        $this->controller = new UserController($this->db, 'PUT', 1);
        
        $input = [
            'pseudo' => 'updateduser',
            'mail' => 'updateduser@example.com',
            'password' => 'updatedpassword',
            'idRole' => 1
        ];
        
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, json_encode($input));
        rewind($stream);
        file_put_contents('php://input', stream_get_contents($stream));
        
        $result = $this->controller->updateUserFromRequest(1);
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertNull($result['body']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
