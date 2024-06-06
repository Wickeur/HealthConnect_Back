<?php

require_once 'src/RDVController.php';

use PHPUnit\Framework\TestCase;

class RDVControllerTest extends TestCase {
    private $controller;
    private $db;

    protected function setUp(): void {
        $this->db = new PDO("sqlite::memory:");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table et insérer des données de test
        $this->db->exec("CREATE TABLE rdvs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            idUserClient INTEGER,
            idUserMedecin INTEGER,
            date DATE,
            timeStart TIME,
            timeEnd TIME
        )");
        $this->db->exec("INSERT INTO rdvs (idUserClient, idUserMedecin, date, timeStart, timeEnd) VALUES (1, 2, '2024-01-15', '09:00:00', '09:30:00')");

        $this->controller = new RDVController($this->db, 'GET', 1);
    }

    public function testGetRDV() {
        $expected = json_encode([
            'id' => 1,
            'idUserClient' => 1,
            'idUserMedecin' => 2,
            'date' => '2024-01-15',
            'timeStart' => '09:00:00',
            'timeEnd' => '09:30:00'
        ]);
        $result = $this->controller->getRDV(1);
        
        $this->assertSame('HTTP/1.1 200 OK', $result['status_code_header']);
        $this->assertSame($expected, $result['body']);
    }

    public function testRDVNotFound() {
        $this->controller = new RDVController($this->db, 'GET', 999);
        $result = $this->controller->getRDV(999);
        
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status_code_header']);
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Not Found']), $result['body']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
