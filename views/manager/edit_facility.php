<!DOCTYPE html>
<html>
<head>
    <title>Edit Facility</title>
</head>
<body>
<h1>Edit Facility</h1>

<form method="POST" action="/ecoBuddy/index.php">
    <input type="hidden" name="update_facility" value="1">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($facility['id']); ?>">

    <label>Title:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($facility['title']); ?>" required><br>

    <label>Category:</label>
    <select name="category">
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['id']); ?>"
                <?php echo ($category['id'] == $facility['category']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Description:</label>
    <textarea name="description"><?php echo htmlspecialchars($facility['description']); ?></textarea><br>

    <label>House Number:</label>
    <input type="text" name="houseNumber" value="<?php echo htmlspecialchars($facility['houseNumber']); ?>" required><br>

    <label>Street Name:</label>
    <input type="text" name="streetName" value="<?php echo htmlspecialchars($facility['streetName']); ?>" required><br>

    <label>County:</label>
    <input type="text" name="county" value="<?php echo htmlspecialchars($facility['county']); ?>" required><br>

    <label>Town:</label>
    <input type="text" name="town" value="<?php echo htmlspecialchars($facility['town']); ?>" required><br>

    <label>Postcode:</label>
    <input type="text" name="postcode" value="<?php echo htmlspecialchars($facility['postcode']); ?>" required><br>

    <label>Latitude:</label>
    <input type="number" step="any" name="latitude" value="<?php echo htmlspecialchars($facility['lat']); ?>" required><br>

    <label>Longitude:</label>
    <input type="number" step="any" name="longitude" value="<?php echo htmlspecialchars($facility['lng']); ?>" required><br>

    <button type="submit">Update Facility</button>
</form>
</body>
</html>
