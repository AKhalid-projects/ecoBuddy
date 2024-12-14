<?php
// Connect to the database
require_once './config/db_connection.php';

try {
    // Fetch all users with unhashed passwords
    $stmt = $pdo->query("SELECT id, password FROM ecoUser");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop through each user and hash the password
    foreach ($users as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);

        // Update the password in the database
        $updateStmt = $pdo->prepare("UPDATE ecoUser SET password = :hashedPassword WHERE id = :id");
        $updateStmt->execute([
            ':hashedPassword' => $hashedPassword,
            ':id' => $user['id']
        ]);

        echo "Password for user ID {$user['id']} has been hashed.<br>";
    }

    echo "All passwords have been updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
