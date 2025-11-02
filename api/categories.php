<?php
header('Content-Type: application/json');


require_once __DIR__ . '/../services/CategoriesService.php';

try {
    $service = new CategoriesService($pdo);
    $categories = $service->getAllCategories();
    echo json_encode(['categories' => $categories]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load categories', 'details' => $e->getMessage()]);
}
?>