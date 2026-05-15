<?php
require_once 'config.php';

function db_connect() {
    global $config;
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO(
        'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8',
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec("CREATE TABLE IF NOT EXISTS paypal_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        txn_id VARCHAR(64) UNIQUE NOT NULL,
        betrag DECIMAL(8,2) NOT NULL,
        status VARCHAR(20) NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at DATETIME NOT NULL
    )");
    return $pdo;
}
