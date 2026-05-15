<?php
require_once 'config.php';
require_once 'db.php';

$raw_post = file_get_contents('php://input');
if (empty($raw_post)) exit;

$check = 'cmd=_notify-validate&' . $raw_post;

$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $check);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
curl_close($ch);

if ($response !== 'VERIFIED') exit;

parse_str($raw_post, $data);

$payment_status = $data['payment_status'] ?? '';
$txn_id         = $data['txn_id'] ?? '';
$receiver_email = $data['receiver_email'] ?? '';
$mc_gross       = floatval($data['mc_gross'] ?? 0);
$mc_currency    = $data['mc_currency'] ?? '';

if (
    $payment_status === 'Completed' &&
    strtolower($receiver_email) === strtolower($config['paypal_email']) &&
    $mc_gross >= 25.00 &&
    $mc_currency === 'EUR' &&
    !empty($txn_id)
) {
    $pdo = db_connect();
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO paypal_payments (txn_id, betrag, status, created_at)
         VALUES (?, ?, 'completed', NOW())"
    );
    $stmt->execute([$txn_id, $mc_gross]);
}
exit;
