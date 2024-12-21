<?php
global $facilityModel;
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: /ecoBuddy/index.php?view=login");
    exit;
}

// Fetch all facilities for the dashboard
$facilities = $facilityModel->getAllFacilities();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Manager Dashboard</h1>

    <!-- Welcome Message -->
    <div class="alert alert-success text-center">
        <p>Welcome, Manager: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
    </div>

    <!-- Logout and Add Facility Buttons -->
    <div class="text-center mb-4">
        <a href="/ecoBuddy/index.php?view=add_facility" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Facility
        </a>
        <a href="/ecoBuddy/index.php?view=logout" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Facilities Display -->
    <div class="row">
        <?php if (!empty($facilities)): ?>
            <?php foreach ($facilities as $facility): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($facility['title']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($facility['category_name']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($facility['description']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($facility['statusComment'] ?? 'No status available'); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($facility['houseNumber'] . ' ' . $facility['streetName'] . ', ' . $facility['county'] . ', ' . $facility['town'] . ' - ' . $facility['postcode']); ?></p>

                            <!-- Action Icons -->
                            <div class="d-flex justify-content-between mt-3">
                                <!-- Edit Button -->
                                <a href="/ecoBuddy/index.php?view=edit_facility&id=<?php echo $facility['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <!-- Delete Button -->
                                <a href="/ecoBuddy/index.php?view=delete_facility&id=<?php echo $facility['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this facility?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <p>No facilities found. Use the "Add New Facility" button to create one.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
