<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';

function seed_stalls_if_empty(PDO $pdo): void {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM stalls')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $standardPrice = 150;
    $premiumPrice = 250;

    $pdo->beginTransaction();
    try {
        // Ensure base categories exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE, price INT NOT NULL)");
        // Upsert categories for restaurants
        $pdo->prepare('INSERT IGNORE INTO categories (name, price) VALUES ("General Restaurant", 200000), ("Special Restaurant", 400000)')->execute();
        // Fetch category ids
        $catStmt = $pdo->query('SELECT id, name FROM categories WHERE name IN ("General Restaurant","Special Restaurant")');
        $nameToId = [];
        foreach ($catStmt->fetchAll() as $row) { $nameToId[$row['name']] = (int)$row['id']; }

        $stmt = $pdo->prepare('INSERT INTO stalls (id, status, price, category_id) VALUES (:id, :status, :price, :category_id)');

        // Sections P-T, 1..14
        foreach (['P','Q','R','S','T'] as $section) {
            for ($i = 1; $i <= 14; $i++) {
                $id = $section . $i;
                $stmt->execute([':id' => $id, ':status' => 'available', ':price' => $standardPrice, ':category_id' => null]);
            }
        }

        // U: General Restaurants U1..U15 (200000) and Special Restaurants U16..U20 (400000)
        for ($i = 1; $i <= 15; $i++) {
            $id = 'U' . $i;
            $stmt->execute([':id' => $id, ':status' => 'available', ':price' => 200000, ':category_id' => ($nameToId['General Restaurant'] ?? null)]);
        }
        for ($i = 16; $i <= 20; $i++) {
            $id = 'U' . $i;
            $stmt->execute([':id' => $id, ':status' => 'available', ':price' => 400000, ':category_id' => ($nameToId['Special Restaurant'] ?? null)]);
        }

        // V: Aquaculture stalls V1..V89, premium for > 75
        for ($i = 1; $i <= 89; $i++) {
            $id = 'V' . $i;
            $price = $i > 75 ? $premiumPrice : $standardPrice;
            $stmt->execute([':id' => $id, ':status' => 'available', ':price' => $price, ':category_id' => null]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to seed stalls', 'details' => $e->getMessage()]);
        exit;
    }
}

try {
    seed_stalls_if_empty($pdo);

    $stmt = $pdo->query('SELECT id, status, price, category_id, organization, booking_ref FROM stalls ORDER BY id');
    $stalls = $stmt->fetchAll();

    echo json_encode(['stalls' => $stalls]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load stalls', 'details' => $e->getMessage()]);
}
