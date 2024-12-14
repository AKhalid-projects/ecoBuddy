<?php
try {
    $pdo = new PDO("sqlite:database/ecobuddy.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert sample data
    $sql = "
    INSERT INTO users (username, password, user_type)
    VALUES
        ('admin', 'adminpassword', 'manager'),
        ('lee', 'lee123password', 'user');

    INSERT INTO eco_facilities (title, category, description, location, latitude, longitude, status, image_path)
    VALUES
        ('Recycling Bin', 'Waste Management', 'Recycling bin for plastics and paper', '123 Green St', 40.7128, -74.0060, 'Active', '/images/recycle.jpg'),
        ('E-Bike Station', 'Transportation', 'Station for electric bikes', '456 Eco Blvd', 40.7306, -73.9352, 'Under Maintenance', '/images/ebike.jpg');
    ";

    $pdo->exec($sql);

    echo "Sample data inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
