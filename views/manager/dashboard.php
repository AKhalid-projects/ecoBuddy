<?php
// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Number of facilities per page
$total = $facilityController->getTotalFacilities($search, $category);
$facilities = $facilityController->getPaginatedFacilities($page, $limit, $search, $category);

$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">Manager Dashboard</h1>
    <div class="alert alert-success text-center">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
    </div>

    <!-- Logout Button -->
    <div class="text-center mb-4">
        <a href="/ecoBuddy/index.php?view=logout" class="btn btn-danger">Logout</a>
    </div>

    <!-- Add Facility Button -->
    <div class="text-center mb-4">
        <a href="/ecoBuddy/index.php?view=add_facility" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Facility
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="/ecoBuddy/index.php" class="mb-4">
        <input type="hidden" name="view" value="dashboard">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by title or description"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
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
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Facilities Display -->
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
                            <div class="d-flex justify-content-start gap-4">
                                <a href="/ecoBuddy/index.php?view=edit_facility&id=<?php echo $facility['id']; ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                                <a href="/ecoBuddy/index.php?view=delete_facility&id=<?php echo $facility['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this facility?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?view=dashboard&page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?view=dashboard&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?view=dashboard&page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else : ?>
        <div class="alert alert-warning text-center">No facilities found. Try adjusting your search criteria.</div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
