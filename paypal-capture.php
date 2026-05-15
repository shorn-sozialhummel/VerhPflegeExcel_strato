<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false]);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$txn_id = trim($input['txn_id'] ?? '');
$betrag = floatval($input['betrag'] ?? 0);

if (empty($txn_id) || $betrag < 25.00) {
    echo json_encode(['ok' => false, 'reason' => 'invalid']);
    exit;
}

try {
    $pdo  = db_connect();
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO paypal_payments
         (txn_id, betrag, status, created_at)
         VALUES (?, ?, 'completed', NOW())"
    );
    $stmt->execute([$txn_id, $betrag]);
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'reason' => 'db_error']);
}
