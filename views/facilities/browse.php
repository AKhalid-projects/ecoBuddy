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
}
?>

<?php if (!empty($facilities)) : ?>
    <?php foreach ($facilities as $facility) : ?>
        <div>
            <h2><?php echo htmlspecialchars($facility['title']); ?></h2>
            <p><?php echo htmlspecialchars($facility['description']); ?></p>
            <p>Status: <?php echo htmlspecialchars($facility['status']); ?></p>
            <p>Location: <?php echo htmlspecialchars($facility['location']); ?></p>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No facilities found.</p>
<?php endif; ?>
</body>
</html>