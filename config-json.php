<?php
require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode([
  'hoechstbetrag' => $config['hoechstbetrag'],
  'mindestlohn'   => $config['mindestlohn'],
  'paypal_link'   => $config['paypal_link'],
]);
