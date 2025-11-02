<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}


require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../services/BookingService.php';


$input = json_decode(file_get_contents('php://input'), true);
$stallsInput = isset($input['stalls']) && is_array($input['stalls']) ? $input['stalls'] : [];
$totalPrice = isset($input['totalPrice']) ? (int) $input['totalPrice'] : 0;
$category = isset($input['category']) ? $input['category'] : null;

$service = new BookingService($pdo);
$result = $service->createBooking($stallsInput, $totalPrice, $category);
if (isset($result['ok']) && $result['ok']) {
    echo json_encode(['ok' => true, 'reference' => $result['reference']]);
} else {
    http_response_code(400);
    echo json_encode(['error' => $result['error'] ?? 'Unknown error']);
}
