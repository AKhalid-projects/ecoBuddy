<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: /ecoBuddy/index.php?view=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
</head>
<body>
<h1>Welcome, Manager!</h1>
<p>You are logged in as: <?php echo $_SESSION['username']; ?></p>
<a href="/ecoBuddy/index.php?view=logout">Logout</a>
</body>
</html>
