<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$stallsInput = isset($input['stalls']) && is_array($input['stalls']) ? $input['stalls'] : [];
$totalPrice = isset($input['totalPrice']) ? (int)$input['totalPrice'] : 0;
// Category: 'Ornamental' | 'Other'
$category = isset($input['category']) && in_array($input['category'], ['Ornamental','Other'], true)
    ? $input['category']
    : null;

// Normalize input to array of ['id' => string, 'organization' => 'DFAR'|'NAQDA', 'category_id'?: 1|2]
$stallIds = [];
$stallOrgMap = [];
$stallCatMap = [];
foreach ($stallsInput as $item) {
    if (is_string($item)) {
        // backward compatibility, organization unknown
        $stallIds[] = $item;
    } elseif (is_array($item) && isset($item['id'])) {
        $id = (string)$item['id'];
        $stallIds[] = $id;
        if (isset($item['organization']) && in_array($item['organization'], ['DFAR','NAQDA'], true)) {
            $stallOrgMap[$id] = $item['organization'];
        }
        if (isset($item['category_id'])) {
            $stallCatMap[$id] = (int)$item['category_id'];
        }
    }
}

if (empty($stallIds)) {
    http_response_code(400);
    echo json_encode(['error' => 'No stalls provided']);
    exit;
}
if ($category === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Category is required and must be Ornamental or Other']);
    exit;
}

function generate_reference(): string {
    return 'BK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
}

try {
    $placeholders = implode(',', array_fill(0, count($stallIds), '?'));

    $pdo->beginTransaction();

    // Lock the target stalls
    $stmt = $pdo->prepare("SELECT id, status, price FROM stalls WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($stallIds);
    $rows = $stmt->fetchAll();

    if (count($rows) !== count($stallIds)) {
        throw new RuntimeException('Some stalls not found');
    }

    // Validate all are available
    foreach ($rows as $row) {
        if ($row['status'] !== 'available') {
            throw new RuntimeException('One or more stalls are not available');
        }
        $id = $row['id'];
        if (!isset($stallOrgMap[$id])) {
            throw new RuntimeException('Organization missing for stall ' . $id);
        }
        // If U-stall, require category_id (1 or 2)
        if (str_starts_with($id, 'U')) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [1,2], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id);
            }
        }
    }

    $reference = generate_reference();

    $insBooking = $pdo->prepare('INSERT INTO bookings (reference, category, total_price) VALUES (:ref, :cat, :total)');
    $insBooking->execute([':ref' => $reference, ':cat' => $category, ':total' => $totalPrice]);
    $bookingId = (int)$pdo->lastInsertId();

    $insItem = $pdo->prepare('INSERT INTO booking_items (booking_id, stall_id, organization, price) VALUES (:bid, :sid, :org, :price)');
    $updStall = $pdo->prepare('UPDATE stalls SET status = "booked", organization = :org, booking_ref = :ref, category_id = :cat WHERE id = :id');

    // Load categories once
    $catPrice = [];
    $catStmt = $pdo->query('SELECT id, price FROM categories WHERE id IN (1,2)');
    foreach ($catStmt->fetchAll() as $c) { $catPrice[(int)$c['id']] = (int)$c['price']; }

    foreach ($rows as $row) {
        $id = $row['id'];
        $org = $stallOrgMap[$id];
        $price = (int)$row['price'];
        if (str_starts_with($id, 'U')) {
            $cid = $stallCatMap[$id] ?? null;
            if ($cid && isset($catPrice[$cid])) {
                $price = $catPrice[$cid];
            }
        }
        $insItem->execute([
            ':bid' => $bookingId,
            ':sid' => $id,
            ':org' => $org,
            ':price' => $price,
        ]);
        $updStall->execute([':org' => $org, ':ref' => $reference, ':id' => $id, ':cat' => ($stallCatMap[$id] ?? null)]);
    }

    $pdo->commit();

    echo json_encode(['ok' => true, 'reference' => $reference]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
