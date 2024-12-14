<!DOCTYPE html>
<html>
<head>
    <title>Browse Facilities</title>
</head>
<body>
<h1>Eco Facilities</h1>

<?php
// Check if the user is logged in
if (isset($_SESSION['username'])) {
    echo "<p>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</p>";
    echo '<a href="/ecoBuddy/index.php?view=logout">Logout</a>';
} else {
    echo '<p>You are not logged in. <a href="/ecoBuddy/index.php?view=login">Login here</a>.</p>';
    exit; // Stop rendering the page for non-logged-in users
}
?>

<?php if (!empty($facilities)) : ?>
    <?php foreach ($facilities as $facility) : ?>
        <div>
            <h2><?php echo htmlspecialchars($facility['title']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($facility['category_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($facility['description']); ?></p>
            <p><strong>Status:</strong>
                <?php if (!empty($facility['statusComment'])) : ?>
                    <?php echo htmlspecialchars($facility['statusComment']); ?>
                <?php else : ?>
                    No status available
                <?php endif; ?>
            </p>
            <p><strong>Location:</strong>
                <?php echo htmlspecialchars($facility['houseNumber'] . ' ' . $facility['streetName'] . ', ' . $facility['county'] . ', ' . $facility['town'] . ' - ' . $facility['postcode']); ?>
            </p>
            <p><strong>Coordinates:</strong>
                <?php
                echo !empty($facility['lat']) && !empty($facility['lng'])
                    ? htmlspecialchars($facility['lat'] . ', ' . $facility['lng'])
                    : 'Coordinates not available';
                ?>
            </p>
            <p><strong>Contributor:</strong> <?php echo htmlspecialchars($facility['contributor_name']); ?></p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else : ?>
    <p>No facilities found.</p>
    <?php if ($_SESSION['role'] === 'Manager') : ?>
        <a href="/ecoBuddy/index.php?view=add_facility">Add New Facility</a>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
