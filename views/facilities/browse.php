<!DOCTYPE html>
<html>
<head>
    <title>Browse Facilities</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Eco Facilities</h1>

    <?php
    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        echo '<div class="alert alert-success">Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</div>';
        echo '<a href="/ecoBuddy/index.php?view=logout" class="btn btn-danger mb-3">Logout</a>';
    } else {
        echo '<div class="alert alert-warning">You are not logged in. <a href="/ecoBuddy/index.php?view=login" class="alert-link">Login here</a>.</div>';
        exit; // Stop rendering the page for non-logged-in users
    }
    ?>

    <?php if (!empty($facilities)) : ?>
        <div class="row">
            <?php foreach ($facilities as $facility) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($facility['title']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($facility['category_name']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($facility['description']); ?></p>
                            <p><strong>Status:</strong>
                                <?php echo !empty($facility['statusComment'])
                                    ? htmlspecialchars($facility['statusComment'])
                                    : 'No status available'; ?>
                            </p>
                            <p><strong>Location:</strong>
                                <?php echo htmlspecialchars($facility['houseNumber'] . ' ' . $facility['streetName'] . ', ' . $facility['county'] . ', ' . $facility['town'] . ' - ' . $facility['postcode']); ?>
                            </p>
                            <p><strong>Coordinates:</strong>
                                <?php echo !empty($facility['lat']) && !empty($facility['lng'])
                                    ? htmlspecialchars($facility['lat'] . ', ' . $facility['lng'])
                                    : 'Coordinates not available'; ?>
                            </p>
                            <p><strong>Contributor:</strong> <?php echo htmlspecialchars($facility['contributor_name']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="text-center">No facilities found.</p>
        <?php if ($_SESSION['role'] === 'Manager') : ?>
            <div class="text-center">
                <a href="/ecoBuddy/index.php?view=add_facility" class="btn btn-primary">Add New Facility</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
