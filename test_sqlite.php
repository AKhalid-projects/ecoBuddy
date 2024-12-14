<?php
try {
    // Connect to SQLite database
    $dbPath = __DIR__ . '/database/ecobuddy.db';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Open the CSV file
    $file = fopen(__DIR__ . '/Users_Dataset.csv', 'r');
    if ($file === false) {
        throw new Exception('Failed to open CSV file.');
    }

    // Skip the header row
    fgetcsv($file);

    // Prepare the SQL statement for insertion
    $stmt = $pdo->prepare("
        INSERT INTO users (id, username, password, user_type)
        VALUES (:id, :username, :password, :user_type)
    ");

    // Read and process each row in the CSV file
    while (($row = fgetcsv($file)) !== false) {
        $hashedPassword = password_hash($row[2], PASSWORD_BCRYPT); // Hash the password
        $stmt->execute([
            ':id' => $row[0],
            ':username' => $row[1],
            ':password' => $hashedPassword,
            ':user_type' => $row[3],
        ]);
    }

    fclose($file);
    echo "Users imported successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
