<?php
try {
    // Connect to the database
    $dbPath = __DIR__ . '/database/ecobuddy.db';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define user data with hashed passwords
    $users = [
        ['username' => 'admin', 'password' => password_hash('adminpassword123', PASSWORD_BCRYPT), 'user_type' => 'manager'],
        ['username' => 'lee', 'password' => password_hash('lee123password', PASSWORD_BCRYPT), 'user_type' => 'user']
    ];

    // Insert users into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, user_type) VALUES (:username, :password, :user_type)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }

    echo "Users inserted successfully!";
} catch (PDOException $e) {
    echo "Error inserting users: " . $e->getMessage();
}
?>
