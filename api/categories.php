<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query('SELECT id, name, price FROM categories ORDER BY id');
    $categories = $stmt->fetchAll();
    echo json_encode(['categories' => $categories]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load categories', 'details' => $e->getMessage()]);
}
?>


