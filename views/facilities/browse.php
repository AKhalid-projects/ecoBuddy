<?php
// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Get the current page from URL or default to 1.
$limit = 6;  // Number of facilities to display per page.

$location = $_GET['location'] ?? null;  // Get location filter input if provided.
$status = $_GET['status'] ?? null;  // Get status filter input if provided.

// Get the total number of facilities matching the search filters for pagination calculation.
$total = $facilityController->getTotalFacilities($search, $category, $location, $status);

// Get the facilities for the current page, applying the filters and pagination.
$facilities = $facilityController->getPaginatedFacilities($page, $limit, $search, $category, $location, $status);

$totalPages = ceil($total / $limit);  // Calculate the total number of pages.
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Facilities</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Eco Facilities</h1>

    <?php
    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        echo '<div class="alert alert-success">Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</div>';  // Display welcome message.
        echo '<a href="/ecoBuddy/index.php?view=logout" class="btn btn-danger mb-3">Logout</a>';  // Logout button.
    } else {
        // Display login prompt if the user is not logged in and exit the page.
        echo '<div class="alert alert-warning">You are not logged in. <a href="/ecoBuddy/index.php?view=login" class="alert-link">Login here</a>.</div>';
        exit;
    }
    ?>

    <!-- Search Form -->
    <form method="GET" action="/ecoBuddy/index.php" class="mb-4">
        <input type="hidden" name="view" value="browse">  <!-- Keep the view as 'browse' when submitting the form. -->
        <div class="row g-3 justify-content-center">
            <div class="col-md-6">
                <!-- Search by title or description -->
                <input type="text" name="search" class="form-control" placeholder="Search by title or description"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <!-- Filter by category -->
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>"
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <!-- Filter by location -->
                <input type="text" name="location" class="form-control" placeholder="Search by location (e.g., town, county, postcode)"
                       value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <!-- Filter by status -->
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Under Maintenance" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Under Maintenance') ? 'selected' : ''; ?>>Under Maintenance</option>
                    <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <!-- Submit button for searching -->
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Facilities Display -->
    <?php if (!empty($facilities)) : ?>  <!-- Check if there are any facilities to display. -->
        <div class="row">
            <?php foreach ($facilities as $facility) : ?>  <!-- Loop through each facility and display its details. -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($facility['title']); ?></h5>  <!-- Facility title. -->
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($facility['category_name']); ?></h6>  <!-- Facility category. -->
                            <p class="card-text"><?php echo htmlspecialchars($facility['description']); ?></p>  <!-- Facility description. -->
                            <p><strong>Status:</strong>
                                <?php echo !empty($facility['statusComment'])
                                    ? htmlspecialchars($facility['statusComment'])  // Show the status comment if available.
                                    : 'No status available'; ?>
                            </p>
                            <p><strong>Location:</strong>
                                <?php echo htmlspecialchars($facility['houseNumber'] . ' ' . $facility['streetName'] . ', ' . $facility['county'] . ', ' . $facility['town'] . ' - ' . $facility['postcode']); ?>  <!-- Full address. -->
                            </p>
                            <p><strong>Coordinates:</strong>
                                <?php echo !empty($facility['lat']) && !empty($facility['lng'])
                                    ? htmlspecialchars($facility['lat'] . ', ' . $facility['lng'])  // Show coordinates if available.
                                    : 'Coordinates not available'; ?>
                            </p>
                            <p><strong>Contributor:</strong> <?php echo htmlspecialchars($facility['contributor_name']); ?></p>  <!-- Contributor information. -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Previous page button -->
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?view=browse&page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Page number links -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?view=browse&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next page button -->
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?view=browse&page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else : ?>
        <!-- Display a message if no facilities match the search criteria. -->
        <div class="alert alert-warning text-center">No facilities found. Try adjusting your search criteria.</div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS for interactive components -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
