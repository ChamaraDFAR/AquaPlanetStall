<?php
// StallsService: handles stall data access and logic
require_once __DIR__ . '/../includes/db.php';

class StallsService
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllStalls()
    {
        $stmt = $this->pdo->query('SELECT id, status, price, category_id, organization, booking_ref FROM stalls ORDER BY id');
        return $stmt->fetchAll();
    }

    public function getStallById($id)
    {
        $stmt = $this->pdo->prepare('SELECT id, status, price, category_id, organization, booking_ref FROM stalls WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
