<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'reason' => 'invalid_method']);
    exit;
}

$txn_id = trim($_POST['txn_id'] ?? '');

if (strlen($txn_id) < 8) {
    echo json_encode(['ok' => false, 'reason' => 'too_short']);
    exit;
}

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare(
        "SELECT id FROM paypal_payments
         WHERE txn_id = ? AND status = 'completed' AND used = 0"
    );
    $stmt->execute([$txn_id]);
    $row = $stmt->fetch();

    if ($row) {
        $pdo->prepare("UPDATE paypal_payments SET used = 1 WHERE txn_id = ?")
            ->execute([$txn_id]);
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'reason' => 'not_found']);
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'reason' => 'db_error']);
}
