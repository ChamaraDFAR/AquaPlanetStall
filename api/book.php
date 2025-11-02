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
// Category: will be overridden for U-section, P-T section, and V-section stalls based on stall categories
$category = isset($input['category']) && in_array($input['category'], [
    'Ornamental','Other','General Restaurant','Special Restaurant',
    'Banking partner','Platinum sponsor','Gold sponsor','Silver sponsor',
    'Bronze sponsor','Co sponsor stalls','General Exhibition stall',
    'Ornamental Fish Stall(A)','Ornamental Fish Stall(B)','Ornamental Fish Stall(C)','Ornamental Fish Stall(D)'
], true) ? $input['category'] : null;

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
// Category is only required for non-U-section, non-P-T-section, and non-V-section bookings
// For U-section, P-T-section, and V-section bookings, it will be determined from stall categories
if ($category === null) {
    // Check if all stalls are U-section, P-T section, or V-section
    $allCategorized = true;
    foreach ($stallIds as $id) {
        $firstChar = substr($id, 0, 1);
        if (!str_starts_with($id, 'U') && !in_array($firstChar, ['P','Q','R','S','T','V'], true)) {
            $allCategorized = false;
            break;
        }
    }
    if (!$allCategorized) {
        http_response_code(400);
        echo json_encode(['error' => 'Category is required for non-categorized stall bookings']);
        exit;
    }
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
        // If U-stall, require category_id (1 = General Restaurant or 2 = Special Restaurant)
        if (str_starts_with($id, 'U')) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [1,2], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id . '. Must be General Restaurant (1) or Special Restaurant (2)');
            }
        }
        // If P-T stall, require category_id (3-9)
        if (in_array(substr($id, 0, 1), ['P','Q','R','S','T'], true)) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [3,4,5,6,7,8,9], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id);
            }
        }
        // If V-stall, require category_id (10-13)
        if (str_starts_with($id, 'V')) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [10,11,12,13], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id);
            }
        }
        // If P-T stall, require category_id (3-9)
        if (in_array(substr($id, 0, 1), ['P','Q','R','S','T'], true)) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [3,4,5,6,7,8,9], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id);
            }
        }
        // If V-stall, require category_id (10-13)
        if (str_starts_with($id, 'V')) {
            if (!isset($stallCatMap[$id]) || !in_array($stallCatMap[$id], [10,11,12,13], true)) {
                throw new RuntimeException('Category missing/invalid for stall ' . $id);
            }
        }
    }

    $reference = generate_reference();

    // Determine the booking category based on stalls
    $bookingCategory = $category; // Default to provided category
    $hasUSectionStalls = false;
    $hasPTSectionStalls = false;
    $hasVSectionStalls = false;
    $categoryIds = [];
    
    // Check if booking contains U-section, P-T section, or V-section stalls and collect their categories
    foreach ($stallIds as $id) {
        $firstChar = substr($id, 0, 1);
        if ($firstChar === 'U') {
            $hasUSectionStalls = true;
            if (isset($stallCatMap[$id])) {
                $categoryIds[] = $stallCatMap[$id];
            }
        } elseif (in_array($firstChar, ['P','Q','R','S','T'], true)) {
            $hasPTSectionStalls = true;
            if (isset($stallCatMap[$id])) {
                $categoryIds[] = $stallCatMap[$id];
            }
        } elseif ($firstChar === 'V') {
            $hasVSectionStalls = true;
            if (isset($stallCatMap[$id])) {
                $categoryIds[] = $stallCatMap[$id];
            }
        }
    }
    
    // If booking has U-section, P-T section, or V-section stalls, set category based on stall category
    if (($hasUSectionStalls || $hasPTSectionStalls || $hasVSectionStalls) && !empty($categoryIds)) {
        // For U-section: prefer Special Restaurant (id=2) if mixed
        // For P-T section: use the highest value category (lowest ID = highest price)
        // For V-section: use the highest value category (lowest ID = highest price)
        $selectedCategoryId = $categoryIds[0];
        if ($hasUSectionStalls && in_array(2, $categoryIds)) {
            $selectedCategoryId = 2; // Special Restaurant
        } elseif ($hasPTSectionStalls || $hasVSectionStalls) {
            // For P-T and V, use the lowest ID (highest price category)
            $selectedCategoryId = min($categoryIds);
        }
        
        // Fetch category name from database
        $catNameStmt = $pdo->prepare('SELECT name FROM categories WHERE id = ?');
        $catNameStmt->execute([$selectedCategoryId]);
        $catRow = $catNameStmt->fetch();
        if ($catRow && isset($catRow['name'])) {
            $bookingCategory = $catRow['name'];
        }
    }

    $insBooking = $pdo->prepare('INSERT INTO bookings (reference, category, total_price) VALUES (:ref, :cat, :total)');
    $insBooking->execute([':ref' => $reference, ':cat' => $bookingCategory, ':total' => $totalPrice]);
    $bookingId = (int)$pdo->lastInsertId();

    $insItem = $pdo->prepare('INSERT INTO booking_items (booking_id, stall_id, organization, price) VALUES (:bid, :sid, :org, :price)');
    $updStall = $pdo->prepare('UPDATE stalls SET status = "booked", organization = :org, booking_ref = :ref, category_id = :cat WHERE id = :id');

    // Load categories once (all categories: 1-13)
    $catPrice = [];
    $catStmt = $pdo->query('SELECT id, price FROM categories WHERE id IN (1,2,3,4,5,6,7,8,9,10,11,12,13)');
    foreach ($catStmt->fetchAll() as $c) { $catPrice[(int)$c['id']] = (int)$c['price']; }

    foreach ($rows as $row) {
        $id = $row['id'];
        $org = $stallOrgMap[$id];
        $price = (int)$row['price'];
        $firstChar = substr($id, 0, 1);
        if ($firstChar === 'U' || in_array($firstChar, ['P','Q','R','S','T','V'], true)) {
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
