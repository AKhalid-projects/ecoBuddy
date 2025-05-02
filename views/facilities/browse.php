<?php
// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;

$location = $_GET['location'] ?? null;
$status = $_GET['status'] ?? null;
$search = $_GET['search'] ?? null;
$category = $_GET['category'] ?? null;

$total = $facilityController->getTotalFacilities($search, $category, $location, $status);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['view'] === 'update_status') {
    include('update_status.php');
    exit;
}
$facilities = $facilityController->getPaginatedFacilities($page, $limit, $search, $category, $location, $status);

$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Facilities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        .user-marker {
            background: none;
            border: none;
        }
        .user-marker div {
            box-shadow: 0 0 4px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Eco Facilities</h1>

    <?php
    if (isset($_SESSION['username'])) {
        echo '<div class="alert alert-success">Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</div>';
        echo '<a href="index.php?view=logout" class="btn btn-danger mb-3">Logout</a>';
    } else {
        echo '<div class="alert alert-warning">You are not logged in. <a href="index.php?view=login" class="alert-link">Login here</a>.</div>';
        exit;
    }
    ?>

    <!-- Search Form -->
    <form method="GET" action="index.php" class="mb-4" id="searchForm">
        <input type="hidden" name="view" value="browse">
        <div class="row g-3 justify-content-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by title or description"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" id="searchInput">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" id="categorySelect">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="location" class="form-control" placeholder="Search by location"
                       value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>" id="locationInput">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusSelect">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Under Maintenance" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Under Maintenance') ? 'selected' : ''; ?>>Under Maintenance</option>
                    <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Map -->
    <div id="map" style="height: 500px; width: 100%; margin-bottom: 30px;"></div>

    <!-- Facilities Display -->
    <div id="facilitiesContainer">
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
                                    <?php echo !empty($facility['statusComment']) ? htmlspecialchars($facility['statusComment']) : 'No status available'; ?>
                                </p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($facility['houseNumber'] . ' ' . $facility['streetName'] . ', ' . $facility['county'] . ', ' . $facility['town'] . ' - ' . $facility['postcode']); ?></p>
                                <p><strong>Coordinates:</strong>
                                    <?php echo (!empty($facility['lat']) && !empty($facility['lng']))
                                        ? htmlspecialchars($facility['lat'] . ', ' . $facility['lng'])
                                        : 'Coordinates not available'; ?>
                                </p>
                                <p><strong>Contributor:</strong> <?php echo htmlspecialchars($facility['contributor_name']); ?></p>
                                <!-- Update Status Button -->
                                <button class="btn btn-sm btn-outline-primary" onclick="openStatusPrompt(<?php echo $facility['id']; ?>)">Update Status</button>
                                <!-- Location Button -->
                                <button class="btn btn-sm btn-outline-success" onclick="zoomToFacility(<?php echo $facility['id']; ?>, <?php echo $facility['lat']; ?>, <?php echo $facility['lng']; ?>)">Location</button>
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
                            <a class="page-link" href="?view=browse&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?view=browse&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
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
            <div class="alert alert-warning text-center">No facilities found. Try adjusting your search criteria.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- JavaScript for Mapping and Status Update -->
<script>
    var map = L.map('map').setView([26.2285, 50.5860], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Try to center map on user
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;
            map.setView([userLat, userLng], 13);
            
            // Add marker for user's location
            var userMarker = L.marker([userLat, userLng], {
                icon: L.divIcon({
                    className: 'user-marker',
                    html: '<div style="background-color: #4CAF50; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>'
                })
            }).addTo(map);
            
            // Add popup for user's location
            userMarker.bindPopup('Your current location');
        }, error => {
            console.error('Error getting user location:', error);
        });
    }

    // Load facilities from server and add markers
    fetch('index.php?view=facilities_json')
        .then(response => response.json())
        .then(facilities => {
            facilities.forEach(facility => {
                if (facility.lat && facility.lng) {
                    // Create marker with facility location
                    var marker = L.marker([parseFloat(facility.lat), parseFloat(facility.lng)]).addTo(map);
                    
                    // Create popup content with facility details
                    var popupContent = `
                        <div class="facility-popup">
                            <h5>${facility.title}</h5>
                            <p><strong>Category:</strong> ${facility.category_name}</p>
                            <p><strong>Status:</strong> ${facility.statusComment || 'No status available'}</p>
                            <p><strong>Location:</strong> ${facility.houseNumber} ${facility.streetName}, ${facility.town}</p>
                        </div>
                    `;
                    
                    // Bind popup to marker
                    marker.bindPopup(popupContent);
                }
            });
        })
        .catch(error => {
            console.error('Error loading facilities:', error);
            alert('Error loading facility locations. Please try again later.');
        });

    // Open a prompt to update status
    function openStatusPrompt(facilityId) {
        var status = prompt('Enter new status for this facility:');
        if (status) {
            updateFacilityStatus(facilityId, status);
        }
    }

    // AJAX function to update facility status
    function updateFacilityStatus(facilityId, statusComment) {
        fetch('index.php?view=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `facilityId=${facilityId}&statusComment=${encodeURIComponent(statusComment)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert('Status updated!');
                window.location.reload();
            } else {
                alert('Error updating status: ' + (data.message || 'Unknown error.'));
            }
        })
        .catch(error => console.error('Error updating status:', error));
    }

    // Function to zoom to facility location
    function zoomToFacility(facilityId, lat, lng) {
        if (lat && lng) {
            map.setView([parseFloat(lat), parseFloat(lng)], 16);
            // Find and open the popup for this facility
            map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                    const markerLat = layer.getLatLng().lat;
                    const markerLng = layer.getLatLng().lng;
                    if (markerLat === parseFloat(lat) && markerLng === parseFloat(lng)) {
                        layer.openPopup();
                    }
                }
            });
        } else {
            alert('Location coordinates not available for this facility.');
        }
    }

    // Real-time search functionality
    let searchTimeout;
    const searchInputs = ['searchInput', 'categorySelect', 'locationInput', 'statusSelect'];
    
    searchInputs.forEach(inputId => {
        document.getElementById(inputId).addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    });

    function performSearch() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categorySelect').value;
        const location = document.getElementById('locationInput').value;
        const status = document.getElementById('statusSelect').value;

        fetch(`index.php?view=browse&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&location=${encodeURIComponent(location)}&status=${encodeURIComponent(status)}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newFacilities = doc.getElementById('facilitiesContainer');
                document.getElementById('facilitiesContainer').innerHTML = newFacilities.innerHTML;
            })
            .catch(error => {
                console.error('Error performing search:', error);
            });
    }
</script>
</body>
</html>
