<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../services/ZonesService.php';

try {
    $service = new ZonesService($pdo);
    $zones = $service->getAllZones();
    echo json_encode(['ok' => true, 'zones' => $zones]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to load zones']);
}


