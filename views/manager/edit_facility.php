<!DOCTYPE html>
<html>
<head>
    <title>Edit Facility</title>
    <!-- Bootstrap CSS for styling the form and ensuring responsiveness -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Edit Facility</h1>

    <!-- Form for updating an existing facility -->
    <form method="POST" action="/ecoBuddy/index.php" class="needs-validation" novalidate>
        <!-- Hidden fields to store form identifiers -->
        <input type="hidden" name="update_facility" value="1"> <!-- Indicates that this is an update form -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($facility['id']); ?>"> <!-- Facility ID -->

        <!-- Title Field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <!-- Input field for the title with pre-filled existing value -->
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($facility['title']); ?>" required>
        </div>

        <!-- Category Dropdown Field -->
        <div class="mb-3">
            <label for="category" class="form-label">Category:</label>
            <select class="form-select" id="category" name="category" required>
                <!-- Loop to display category options -->
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>"
                        <?php echo ($category['id'] == $facility['category']) ? 'selected' : ''; ?>> <!-- Pre-select the existing category -->
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Description Field -->
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <!-- Textarea for the description with existing value -->
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($facility['description']); ?></textarea>
        </div>

        <!-- Address Details -->
        <div class="row g-3">
            <div class="col-md-6">
                <label for="houseNumber" class="form-label">House Number:</label>
                <!-- Input field for house number with pre-filled value -->
                <input type="text" class="form-control" id="houseNumber" name="houseNumber" value="<?php echo htmlspecialchars($facility['houseNumber']); ?>">
            </div>
            <div class="col-md-6">
                <label for="streetName" class="form-label">Street Name:</label>
                <!-- Input field for street name with pre-filled value -->
                <input type="text" class="form-control" id="streetName" name="streetName" value="<?php echo htmlspecialchars($facility['streetName']); ?>">
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label for="county" class="form-label">County:</label>
                <!-- Input field for county with pre-filled value -->
                <input type="text" class="form-control" id="county" name="county" value="<?php echo htmlspecialchars($facility['county']); ?>">
            </div>
            <div class="col-md-6">
                <label for="town" class="form-label">Town:</label>
                <!-- Input field for town with pre-filled value -->
                <input type="text" class="form-control" id="town" name="town" value="<?php echo htmlspecialchars($facility['town']); ?>">
            </div>
        </div>

        <!-- Postcode Field -->
        <div class="mb-3 mt-3">
            <label for="postcode" class="form-label">Postcode:</label>
            <!-- Input field for postcode with pre-filled value -->
            <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo htmlspecialchars($facility['postcode']); ?>" required>
        </div>

        <!-- Latitude and Longitude Fields -->
        <div class="row g-3">
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude:</label>
                <!-- Input field for latitude with pre-filled value -->
                <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="<?php echo htmlspecialchars($facility['lat']); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude:</label>
                <!-- Input field for longitude with pre-filled value -->
                <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="<?php echo htmlspecialchars($facility['lng']); ?>" required>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
            <!-- Button to submit the form and update the facility -->
            <button type="submit" class="btn btn-primary">Update Facility</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS for validation and interactivity -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
