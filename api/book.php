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
$zoneCode = isset($input['zone']) ? $input['zone'] : null;

// Extract buyer information
$buyer = isset($input['buyer']) ? $input['buyer'] : [];
$buyerInfo = [
    'firstName' => isset($buyer['firstName']) ? trim($buyer['firstName']) : '',
    'lastName' => isset($buyer['lastName']) ? trim($buyer['lastName']) : '',
    'email' => isset($buyer['email']) ? trim($buyer['email']) : '',
    'phone' => isset($buyer['phone']) ? trim($buyer['phone']) : '',
    'companyName' => isset($buyer['companyName']) ? trim($buyer['companyName']) : null,
    'address' => isset($buyer['address']) ? trim($buyer['address']) : null
];

// Validate required buyer fields
if (
    empty($buyerInfo['firstName']) || empty($buyerInfo['lastName']) ||
    empty($buyerInfo['email']) || empty($buyerInfo['phone'])
) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required buyer information']);
    exit;
}

$service = new BookingService($pdo);
$result = $service->createBooking($stallsInput, $totalPrice, $category, $zoneCode, $buyerInfo);
if (isset($result['ok']) && $result['ok']) {
    echo json_encode(['ok' => true, 'reference' => $result['reference']]);
} else {
    http_response_code(400);
    echo json_encode(['error' => $result['error'] ?? 'Unknown error']);
}
