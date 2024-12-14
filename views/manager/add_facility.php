<!DOCTYPE html>
<html>
<head>
    <title>Add Facility</title>
</head>
<body>
<h1>Add New Eco Facility</h1>
<form method="POST" action="/ecoBuddy/index.php">
    <input type="hidden" name="create_facility" value="1">

    <!-- Title -->
    <label>Title:</label>
    <input type="text" name="title" required><br>

    <!-- Category Dropdown -->
    <label>Category:</label>
    <select name="category" required>
        <option value="">--Select a Category--</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <!-- Description -->
    <label>Description:</label>
    <textarea name="description"></textarea><br>

    <!-- Address Details -->
    <label>House Number:</label>
    <input type="text" name="houseNumber"><br>
    <label>Street Name:</label>
    <input type="text" name="streetName"><br>
    <label>County:</label>
    <input type="text" name="county"><br>
    <label>Town:</label>
    <input type="text" name="town"><br>
    <label>Postcode:</label>
    <input type="text" name="postcode" required><br>

    <!-- Latitude and Longitude -->
    <label>Latitude:</label>
    <input type="number" step="any" name="latitude" required><br>
    <label>Longitude:</label>
    <input type="number" step="any" name="longitude" required><br>

    <!-- Contributor -->
    <label>Contributor (User ID):</label>
    <input type="number" name="contributor" required><br>

    <!-- Submit Button -->
    <button type="submit">Add Facility</button>
</form>
</body>
</html>
