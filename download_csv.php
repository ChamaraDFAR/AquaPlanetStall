<?php
require_once __DIR__ . '/includes/db.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
if ($ref === '') {
    http_response_code(400);
    echo 'Missing reference';
    exit;
}

$stmt = $pdo->prepare('SELECT id, reference, total_price FROM bookings WHERE reference = ?');
$stmt->execute([$ref]);
$booking = $stmt->fetch();
if (!$booking) {
    http_response_code(404);
    echo 'Booking not found';
    exit;
}

// Determine zone
$z = $pdo->prepare('SELECT z.code, z.name FROM stalls s JOIN zones z ON z.id = s.zone_id WHERE s.booking_ref = ? LIMIT 1');
$z->execute([$ref]);
$zone = $z->fetch() ?: ['code' => '', 'name' => ''];

$it = $pdo->prepare('SELECT stall_id, organization, price FROM booking_items WHERE booking_id = ? ORDER BY stall_id');
$it->execute([(int)$booking['id']]);
$items = $it->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="receipt_' . preg_replace('/[^A-Za-z0-9_-]/', '', $ref) . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Reference', $booking['reference']]);
fputcsv($out, ['Zone', $zone['name'] . ($zone['code'] ? ' (' . $zone['code'] . ')' : '')]);
fputcsv($out, []);
fputcsv($out, ['Stall', 'Organization', 'Price (LKR)']);
foreach ($items as $row) {
    fputcsv($out, [$row['stall_id'], $row['organization'], (int)$row['price']]);
}
fputcsv($out, []);
fputcsv($out, ['Total', (int)$booking['total_price']]);
fclose($out);
exit;


