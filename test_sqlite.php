<?php
$dbPath = __DIR__ . '/database/ecobuddy.db';
$pdo = new PDO("sqlite:$dbPath");
$stmt = $pdo->query("SELECT * FROM users LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($users, true) . "</pre>";
?>
