<?php

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

    public function testCreateUserFromRequest() {
        $newData = json_encode([
            'pseudo' => 'newuser',
            'mail' => 'newuser@example.com',
            'password' => 'newpassword',
            'idRole' => 2
        ]);
        $this->controller = new UserController($this->db, 'POST', null);
        $_POST = json_decode($newData, true);
        $this->controller->createUserFromRequest();

        $statement = $this->db->query("SELECT * FROM users WHERE id = 2");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('newuser', $result['pseudo']);
        $this->assertSame('newuser@example.com', $result['mail']);
        $this->assertSame('newpassword', $result['password']);
        $this->assertSame(2, $result['idRole']);
    }

    public function testUpdateUserFromRequest() {
        $updatedData = json_encode([
            'pseudo' => 'updateduser',
            'mail' => 'updated@example.com',
            'password' => 'updatedpassword',
            'idRole' => 1
        ]);
        $this->controller = new UserController($this->db, 'PUT', 1);
        $_POST = json_decode($updatedData, true);
        $this->controller->updateUserFromRequest(1);

        $statement = $this->db->query("SELECT * FROM users WHERE id = 1");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('updateduser', $result['pseudo']);
        $this->assertSame('updated@example.com', $result['mail']);
        $this->assertSame('updatedpassword', $result['password']);
        $this->assertSame(1, $result['idRole']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
