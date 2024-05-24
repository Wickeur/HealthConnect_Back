<?php

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

    public function testCreateRDVFromRequest() {
        $newData = json_encode([
            'idUserClient' => 1,
            'idUserMedecin' => 2,
            'date' => '2024-01-16',
            'timeStart' => '10:00:00',
            'timeEnd' => '10:30:00'
        ]);
        $this->controller = new RDVController($this->db, 'POST', null);
        $_POST = json_decode($newData, true);
        $this->controller->createRDVFromRequest();

        $statement = $this->db->query("SELECT * FROM rdvs WHERE id = 2");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('1', $result['idUserClient']);
        $this->assertSame('2', $result['idUserMedecin']);
        $this->assertSame('2024-01-16', $result['date']);
        $this->assertSame('10:00:00', $result['timeStart']);
        $this->assertSame('10:30:00', $result['timeEnd']);
    }

    public function testUpdateRDVFromRequest() {
        $updatedData = json_encode([
            'date' => '2024-01-17',
            'timeStart' => '11:00:00',
            'timeEnd' => '11:30:00'
        ]);
        $this->controller = new RDVController($this->db, 'PUT', 1);
        $_POST = json_decode($updatedData, true);
        $this->controller->updateRDVFromRequest(1);

        $statement = $this->db->query("SELECT * FROM rdvs WHERE id = 1");
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('2024-01-17', $result['date']);
        $this->assertSame('11:00:00', $result['timeStart']);
        $this->assertSame('11:30:00', $result['timeEnd']);
    }

    protected function tearDown(): void {
        $this->db = null;
    }
}
