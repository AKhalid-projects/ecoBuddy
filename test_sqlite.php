<?php
$dbPath = __DIR__ . '/database/ecobuddy.db';
$pdo = new PDO("sqlite:$dbPath");
$stmt = $pdo->query("SELECT * FROM eco_facilities");
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($facilities, true) . "</pre>";
?>
