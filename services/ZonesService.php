<?php
// ZonesService: handles zone data access
require_once __DIR__ . '/../includes/db.php';

class ZonesService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllZones()
    {
        $stmt = $this->pdo->query('SELECT id, code, name FROM zones ORDER BY id');
        return $stmt->fetchAll();
    }
}


