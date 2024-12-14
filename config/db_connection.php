<?php
try {
    // Use an absolute path to avoid path issues
    $dbPath = __DIR__ . '/../database/ecobuddy.db';

    // Establish the database connection
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected to the database successfully!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
