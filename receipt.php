<?php
require_once __DIR__ . '/includes/db.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$booking = null;
$items = [];
$zone = ['code' => null, 'name' => null];
if ($ref !== '') {
    // Load booking, items, and zone
    $stmt = $pdo->prepare('SELECT id, reference, category, total_price FROM bookings WHERE reference = ?');
    $stmt->execute([$ref]);
    $booking = $stmt->fetch();

    if ($booking) {
        // Items
        $it = $pdo->prepare('SELECT stall_id, organization, price FROM booking_items WHERE booking_id = ? ORDER BY stall_id');
        $it->execute([(int) $booking['id']]);
        $items = $it->fetchAll();

        // Resolve zone from first stall's zone_id
        $z = $pdo->prepare('SELECT z.code, z.name FROM stalls s JOIN zones z ON z.id = s.zone_id WHERE s.booking_ref = ? LIMIT 1');
        $z->execute([$ref]);
        $zr = $z->fetch();
        if ($zr) {
            $zone['code'] = $zr['code'];
            $zone['name'] = $zr['name'];
        }

        // Get buyer information
        $buyerStmt = $pdo->prepare('SELECT * FROM booking_buyers WHERE booking_id = ?');
        $buyerStmt->execute([(int) $booking['id']]);
        $buyer = $buyerStmt->fetch();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .print-hide {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 20px;
            }

            .receipt-logo {
                max-width: 300px;
                height: auto;
            }
        }

        .receipt-logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="assests/logo.png" alt="AQUA PLANET SRI LANKA INTERNATIONAL EXPO 2025"
                                class="receipt-logo">
                        </div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h4 fw-bold mb-1">Booking Receipt</h1>
                                <div class="text-muted">Reference: <code><?= htmlspecialchars($ref ?: '-') ?></code>
                                </div>
                            </div>
                            <div class="d-flex gap-2 print-hide">
                                <button class="btn btn-primary" onclick="window.print()">Download / Print</button>
                            </div>
                        </div>

                        <?php if (!$booking): ?>
                            <div class="alert alert-danger">Booking not found.</div>
                        <?php else: ?>
                            <?php if (isset($buyer)): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">Buyer Information</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div><strong>First Name:</strong></div>
                                                <div><?= htmlspecialchars($buyer['first_name']) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div><strong>Last Name:</strong></div>
                                                <div><?= htmlspecialchars($buyer['last_name']) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div><strong>Email:</strong></div>
                                                <div><?= htmlspecialchars($buyer['email']) ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div><strong>Phone:</strong></div>
                                                <div><?= htmlspecialchars($buyer['phone']) ?></div>
                                            </div>
                                            <?php if (!empty($buyer['company_name'])): ?>
                                                <div class="col-12">
                                                    <div><strong>Company Name:</strong></div>
                                                    <div><?= htmlspecialchars($buyer['company_name']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($buyer['company_address'])): ?>
                                                <div class="col-12">
                                                    <div><strong>Company Address:</strong></div>
                                                    <div><?= htmlspecialchars($buyer['company_address']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-3">Booking Details</h6>
                                    <div><strong>Zone:</strong> <?= htmlspecialchars($zone['name'] ?: '-') ?>
                                        <?= $zone['code'] ? '(' . htmlspecialchars($zone['code']) . ')' : '' ?>
                                    </div>
                                    <div><strong>Category:</strong> <?= htmlspecialchars($booking['category']) ?></div>
                                    <div><strong>Total Price:</strong> LKR
                                        <?= number_format((int) $booking['total_price']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Stall</th>
                                            <th>Organization</th>
                                            <th class="text-end">Price (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $row): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars($row['stall_id']) ?></td>
                                                <td><?= htmlspecialchars($row['organization']) ?></td>
                                                <td class="text-end"><?= number_format((int) $row['price']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-3 print-hide">
                            <a href="index.php" class="btn btn-outline-secondary">Back to Map</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>