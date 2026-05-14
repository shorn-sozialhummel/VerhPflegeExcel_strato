<?php
require_once 'config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['ok' => false]);
  exit;
}
$eingabe = $_POST['passwort'] ?? '';
echo json_encode(['ok' => ($eingabe === $config['passwort'])]);
