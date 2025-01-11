<!DOCTYPE html>
<html>
<head>
    <title>Add Facility</title>
    <!-- Bootstrap CSS for styling the form and layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Add New Eco Facility</h1>

    <!-- Form for adding a new facility -->
    <form method="POST" action="/ecoBuddy/index.php" class="needs-validation" novalidate>
        <input type="hidden" name="create_facility" value="1"> <!-- Hidden field to indicate form submission for creating a facility -->

        <!-- Title input field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <input type="text" class="form-control" id="title" name="title" required> <!-- Required field for the facility title -->
        </div>

        <!-- Category dropdown menu -->
        <div class="mb-3">
            <label for="category" class="form-label">Category:</label>
            <select class="form-select" id="category" name="category" required> <!-- Required field for category selection -->
                <option value="">--Select a Category--</option> <!-- Placeholder option -->
                <?php foreach ($categories as $category): ?> <!-- Loop through categories to display each option -->
                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Description text area -->
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea> <!-- Optional description field -->
        </div>

        <!-- Address details (House Number and Street Name) -->
        <div class="row g-3">
            <div class="col-md-6">
                <label for="houseNumber" class="form-label">House Number:</label>
                <input type="text" class="form-control" id="houseNumber" name="houseNumber"> <!-- Optional field for house number -->
            </div>
            <div class="col-md-6">
                <label for="streetName" class="form-label">Street Name:</label>
                <input type="text" class="form-control" id="streetName" name="streetName"> <!-- Optional field for street name -->
            </div>
        </div>

        <!-- Address details (County and Town) -->
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label for="county" class="form-label">County:</label>
                <input type="text" class="form-control" id="county" name="county"> <!-- Optional field for county -->
            </div>
            <div class="col-md-6">
                <label for="town" class="form-label">Town:</label>
                <input type="text" class="form-control" id="town" name="town"> <!-- Optional field for town -->
            </div>
        </div>

        <!-- Postcode field -->
        <div class="mb-3 mt-3">
            <label for="postcode" class="form-label">Postcode:</label>
            <input type="text" class="form-control" id="postcode" name="postcode" required> <!-- Required field for postcode -->
        </div>

        <!-- Latitude and Longitude fields -->
        <div class="row g-3">
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude:</label>
                <input type="number" step="any" class="form-control" id="latitude" name="latitude" required> <!-- Required field for latitude -->
            </div>
            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude:</label>
                <input type="number" step="any" class="form-control" id="longitude" name="longitude" required> <!-- Required field for longitude -->
            </div>
        </div>

        <!-- Contributor ID field -->
        <div class="mb-3 mt-3">
            <label for="contributor" class="form-label">Contributor (User ID):</label>
            <input type="number" class="form-control" id="contributor" name="contributor" required> <!-- Required field for the contributor's user ID -->
        </div>

        <!-- Submit button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Add Facility</button> <!-- Submit button to send form data -->
        </div>
    </form>
</div>

<!-- Bootstrap JS for interactive form validations and modal support -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
