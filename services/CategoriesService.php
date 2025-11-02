<?php
// CategoriesService: handles category data access and logic
require_once __DIR__ . '/../includes/db.php';

class CategoriesService
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllCategories()
    {
        $stmt = $this->pdo->query('SELECT id, name, price FROM categories ORDER BY id');
        return $stmt->fetchAll();
    }

    public function getCategoryById($id)
    {
        $stmt = $this->pdo->prepare('SELECT id, name, price FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
