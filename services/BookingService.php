<?php
// Service class for booking logic

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/BookingItem.php';

class BookingService
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createBooking($stallsInput, $totalPrice, $category, $zoneCode = null)
    {

        // Normalize input and build BookingItems
        $stallIds = [];
        $bookingItems = [];
        foreach ($stallsInput as $item) {
            if (is_string($item)) {
                $stallIds[] = $item;
            } elseif (is_array($item) && isset($item['id'])) {
                $id = (string) $item['id'];
                $stallIds[] = $id;
                $org = isset($item['organization']) ? $item['organization'] : null;
                $catId = isset($item['category_id']) ? (int) $item['category_id'] : null;
                $bookingItems[$id] = new BookingItem($id, $org, $catId);
            }
        }
        if (empty($stallIds)) {
            return ['ok' => false, 'error' => 'No stalls provided'];
        }
        // Category is only required for non-U-section, non-P-T-section, and non-V-section bookings
        if ($category === null) {
            $allCategorized = true;
            foreach ($stallIds as $id) {
                $firstChar = substr($id, 0, 1);
                if (!str_starts_with($id, 'U') && !in_array($firstChar, ['P', 'Q', 'R', 'S', 'T', 'V'], true)) {
                    $allCategorized = false;
                    break;
                }
            }
            if (!$allCategorized) {
                return ['ok' => false, 'error' => 'Category is required for non-categorized stall bookings'];
            }
        }

        try {
            $placeholders = implode(',', array_fill(0, count($stallIds), '?'));
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("SELECT id, status, price FROM stalls WHERE id IN ($placeholders) FOR UPDATE");
            $stmt->execute($stallIds);
            $rows = $stmt->fetchAll();
            if (count($rows) !== count($stallIds)) {
                throw new \RuntimeException('Some stalls not found');
            }
            foreach ($rows as $row) {
                $id = $row['id'];
                if ($row['status'] !== 'available') {
                    throw new \RuntimeException('One or more stalls are not available');
                }
                if (!isset($bookingItems[$id]) || empty($bookingItems[$id]->organization)) {
                    throw new \RuntimeException('Organization missing for stall ' . $id);
                }
                if (str_starts_with($id, 'U')) {
                    if (!isset($bookingItems[$id]->category_id) || !in_array($bookingItems[$id]->category_id, [1, 2], true)) {
                        throw new \RuntimeException('Category missing/invalid for stall ' . $id . '. Must be General Restaurant (1) or Special Restaurant (2)');
                    }
                }
                if (in_array(substr($id, 0, 1), ['P', 'Q', 'R', 'S', 'T'], true)) {
                    if (!isset($bookingItems[$id]->category_id) || !in_array($bookingItems[$id]->category_id, [3, 4, 5, 6, 7, 8, 9], true)) {
                        throw new \RuntimeException('Category missing/invalid for stall ' . $id);
                    }
                }
                if (str_starts_with($id, 'V')) {
                    if (!isset($bookingItems[$id]->category_id) || !in_array($bookingItems[$id]->category_id, [10, 11, 12, 13], true)) {
                        throw new \RuntimeException('Category missing/invalid for stall ' . $id);
                    }
                }
            }

            $reference = 'BK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
            $bookingCategory = $category;
            $hasUSectionStalls = false;
            $hasPTSectionStalls = false;
            $hasVSectionStalls = false;
            $categoryIds = [];
            foreach ($stallIds as $id) {
                $firstChar = substr($id, 0, 1);
                if ($firstChar === 'U') {
                    $hasUSectionStalls = true;
                    if (isset($bookingItems[$id]->category_id)) {
                        $categoryIds[] = $bookingItems[$id]->category_id;
                    }
                } elseif (in_array($firstChar, ['P', 'Q', 'R', 'S', 'T'], true)) {
                    $hasPTSectionStalls = true;
                    if (isset($bookingItems[$id]->category_id)) {
                        $categoryIds[] = $bookingItems[$id]->category_id;
                    }
                } elseif ($firstChar === 'V') {
                    $hasVSectionStalls = true;
                    if (isset($bookingItems[$id]->category_id)) {
                        $categoryIds[] = $bookingItems[$id]->category_id;
                    }
                }
            }
            if (($hasUSectionStalls || $hasPTSectionStalls || $hasVSectionStalls) && !empty($categoryIds)) {
                $selectedCategoryId = $categoryIds[0];
                if ($hasUSectionStalls && in_array(2, $categoryIds)) {
                    $selectedCategoryId = 2;
                } elseif ($hasPTSectionStalls || $hasVSectionStalls) {
                    $selectedCategoryId = min($categoryIds);
                }
                $catNameStmt = $this->pdo->prepare('SELECT name FROM categories WHERE id = ?');
                $catNameStmt->execute([$selectedCategoryId]);
                $catRow = $catNameStmt->fetch();
                if ($catRow && isset($catRow['name'])) {
                    $bookingCategory = $catRow['name'];
                }
            }
            $booking = new Booking($reference, $bookingCategory, $totalPrice);
            $insBooking = $this->pdo->prepare('INSERT INTO bookings (reference, category, total_price) VALUES (:ref, :cat, :total)');
            $insBooking->execute([':ref' => $reference, ':cat' => $bookingCategory, ':total' => $totalPrice]);
            $bookingId = (int) $this->pdo->lastInsertId();
            $insItem = $this->pdo->prepare('INSERT INTO booking_items (booking_id, stall_id, organization, price) VALUES (:bid, :sid, :org, :price)');
            $updStall = $this->pdo->prepare('UPDATE stalls SET status = "booked", organization = :org, booking_ref = :ref, category_id = :cat, zone_id = :zone WHERE id = :id');

            // Resolve zone id from provided zone code (if any)
            $zoneId = null;
            if (!empty($zoneCode)) {
                $zstmt = $this->pdo->prepare('SELECT id FROM zones WHERE code = ?');
                $zstmt->execute([$zoneCode]);
                $zrow = $zstmt->fetch();
                if ($zrow && isset($zrow['id'])) {
                    $zoneId = (int) $zrow['id'];
                }
            }
            $catPrice = [];
            $catStmt = $this->pdo->query('SELECT id, price FROM categories WHERE id IN (1,2,3,4,5,6,7,8,9,10,11,12,13)');
            foreach ($catStmt->fetchAll() as $c) {
                $catPrice[(int) $c['id']] = (int) $c['price'];
            }
            foreach ($rows as $row) {
                $id = $row['id'];
                $item = $bookingItems[$id];
                $price = (int) $row['price'];
                $firstChar = substr($id, 0, 1);
                if ($firstChar === 'U' || in_array($firstChar, ['P', 'Q', 'R', 'S', 'T', 'V'], true)) {
                    $cid = $item->category_id ?? null;
                    if ($cid && isset($catPrice[$cid])) {
                        $price = $catPrice[$cid];
                    }
                }
                $item->price = $price;
                $booking->addItem($item);
                $insItem->execute([
                    ':bid' => $bookingId,
                    ':sid' => $id,
                    ':org' => $item->organization,
                    ':price' => $price,
                ]);
                $updStall->execute([
                    ':org' => $item->organization,
                    ':ref' => $reference,
                    ':id' => $id,
                    ':cat' => ($item->category_id ?? null),
                    ':zone' => $zoneId
                ]);
            }
            if (!$booking->validate()) {
                throw new \RuntimeException('Booking data invalid');
            }
            $this->pdo->commit();
            return ['ok' => true, 'reference' => $reference];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
