<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    echo "Starting import process...\n";
    
    // Connect to SQLite database
    $dbPath = __DIR__ . '/database/ecobuddy_updated.sqlite';
    echo "Connecting to database at: {$dbPath}\n";
    
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection established.\n";

    // Open the CSV file
    echo "Opening CSV file...\n";
    $file = fopen(__DIR__ . '/formatted_users.csv', 'r');
    if ($file === false) {
        throw new Exception('Failed to open CSV file.');
    }
    echo "CSV file opened successfully.\n";

    // Skip the header row
    fgetcsv($file, 0, ',', '"', '\\');
    echo "Header row skipped.\n";

    // Start transaction
    $pdo->beginTransaction();
    echo "Transaction started.\n";

    // Prepare the SQL statement for insertion
    $stmt = $pdo->prepare("
        INSERT INTO ecoUser (id, username, password, userType)
        VALUES (:id, :username, :password, :userType)
    ");
    echo "SQL statement prepared.\n";

    $rowCount = 0;
    $errors = [];

    // Read and process each row in the CSV file
    echo "Starting to process rows...\n";
    while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
        try {
            echo "Processing row " . ($rowCount + 1) . "...\n";
            
            // Validate data
            if (count($row) !== 4) {
                throw new Exception("Invalid row format");
            }

            $hashedPassword = password_hash($row[2], PASSWORD_BCRYPT);
            
            $stmt->execute([
                ':id' => (int)$row[0],
                ':username' => $row[1],
                ':password' => $hashedPassword,
                ':userType' => (int)$row[3]
            ]);
            
            $rowCount++;
            echo "Row {$rowCount} processed successfully.\n";
        } catch (Exception $e) {
            $errors[] = "Error on row {$rowCount}: " . $e->getMessage();
            echo "Error processing row {$rowCount}: " . $e->getMessage() . "\n";
        }
    }

    // Commit transaction
    $pdo->commit();
    echo "Transaction committed.\n";
    fclose($file);
    echo "CSV file closed.\n";

    // Report results
    echo "Successfully imported {$rowCount} users.\n";
    if (!empty($errors)) {
        echo "Errors encountered:\n" . implode("\n", $errors);
    }

} catch (Exception $e) {
    // Rollback transaction if started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "Transaction rolled back due to error.\n";
    }
    
    // Log error
    error_log("Import error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
?>