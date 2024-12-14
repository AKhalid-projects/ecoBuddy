<?php
try {
    // Connect to the database
    $dbPath = __DIR__ . '/database/ecobuddy.db';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define dummy data for eco facilities
    $facilities = [
        [
            'title' => 'Recycling Bin',
            'category' => 'Waste Management',
            'description' => 'Recycling bin for plastics and paper',
            'location' => '123 Green St',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'status' => 'Active',
            'image_path' => '/images/recycle.jpg'
        ],
        [
            'title' => 'E-Bike Station',
            'category' => 'Transportation',
            'description' => 'Station for electric bikes',
            'location' => '456 Eco Blvd',
            'latitude' => 40.7306,
            'longitude' => -73.9352,
            'status' => 'Under Maintenance',
            'image_path' => '/images/ebike.jpg'
        ]
    ];

    // Insert facilities into the database
    $stmt = $pdo->prepare("
        INSERT INTO eco_facilities (title, category, description, location, latitude, longitude, status, image_path) 
        VALUES (:title, :category, :description, :location, :latitude, :longitude, :status, :image_path)
    ");
    foreach ($facilities as $facility) {
        $stmt->execute($facility);
    }

    echo "Eco facilities inserted successfully!";
} catch (PDOException $e) {
    echo "Error inserting eco facilities: " . $e->getMessage();
}
?>
