<?php
// Start a new or resume an existing session to store user information
session_start();

// Include the necessary files for database connection, models, and controllers
require_once './config/db_connection.php'; // Database connection file
require_once './models/User.php';          // User model
require_once './models/Facility.php';      // Facility model
require_once './controllers/UserController.php';    // User controller
require_once './controllers/FacilityController.php'; // Facility controller

// Initialize Models and Controllers
$userModel = new User($pdo);  // Instantiate the User model with the database connection
$facilityModel = new Facility($pdo); // Instantiate the Facility model
$userController = new UserController($userModel);  // Create a UserController instance
$facilityController = new FacilityController($facilityModel); // Create a FacilityController instance

// Handle GET requests based on the 'view' parameter in the URL
if (isset($_GET['view'])) {
    $view = htmlspecialchars($_GET['view']); // Sanitize the view input to prevent XSS attacks

    // Route: Login Page
    if ($view === 'login') {
        include './views/users/login.php'; // Include the login view
    }
    // Route: Logout (ends session)
    elseif ($view === 'logout') {
        session_destroy(); // Destroy the current session
        header("Location: /ecoBuddy/index.php?view=login"); // Redirect to the login page
        exit; // Stop further script execution
    }
    // Route: Browse facilities (general user)
    elseif ($view === 'browse') {
        $search = isset($_GET['search']) ? $_GET['search'] : ''; // Get search input
        $category = isset($_GET['category']) ? $_GET['category'] : null; // Get category filter
        $categories = $facilityModel->getAllCategories(); // Get all categories for the filter dropdown
        $facilities = $facilityController->browseFacilities($search, $category); // Fetch facilities based on search and category
        include './views/facilities/browse.php'; // Include the browse facilities view
    }
    // Route: Manager-only views
    elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'manager') {
        // Route: Add new facility form
        if ($view === 'add_facility') {
            $categories = $facilityModel->getAllCategories(); // Get categories for the form
            include './views/manager/add_facility.php'; // Include the add facility view
        }
        // Route: Edit facility form
        elseif ($view === 'edit_facility' && isset($_GET['id'])) {
            $facilityId = (int)$_GET['id']; // Convert the facility ID to an integer
            $facility = $facilityModel->getFacilityById($facilityId); // Fetch the facility details by ID
            if (!$facility) {
                die("Facility with ID $facilityId not found."); // Display an error if the facility is not found
            }

            $categories = $facilityModel->getAllCategories(); // Get categories for the dropdown
            if (!$categories) {
                die("No categories found in the database."); // Display an error if no categories are available
            }
            include './views/manager/edit_facility.php'; // Include the edit facility view
        }
        // Route: Delete a facility
        elseif ($view === 'delete_facility' && isset($_GET['id'])) {
            $facilityId = (int)$_GET['id']; // Get the facility ID from the URL
            $facilityController->deleteFacility($facilityId); // Delete the facility by ID
            header("Location: /ecoBuddy/index.php?view=dashboard"); // Redirect to the manager dashboard
            exit; // Stop further script execution
        }
        // Route: Manager dashboard
        elseif ($view === 'dashboard') {
            $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; // Get and sanitize search input
            $category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null; // Get category filter
            $categories = $facilityModel->getAllCategories(); // Get categories for filtering
            $facilities = $facilityController->browseFacilities($search, $category); // Get filtered facilities
            include './views/manager/dashboard.php'; // Include the dashboard view
        }
    }
    // Route: 404 - Page not found
    else {
        http_response_code(404); // Set HTTP response code to 404 (not found)
        $file = './views/404.php'; // Path to the 404 error page
        if (file_exists($file)) {
            include $file; // Include the 404 error page if it exists
        } else {
            // Show a default 404 message if the file is missing
            echo "<h1>404 - Page Not Found</h1><p>The 404 page is missing.</p>";
        }
    }
}
// Handle POST requests (form submissions)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle login form submission
    if (isset($_POST['login'])) {
        $username = htmlspecialchars($_POST['username']); // Get and sanitize username input
        $password = htmlspecialchars($_POST['password']); // Get and sanitize password input
        $userController->login($username, $password); // Call the login function
    }
    // Handle create facility form submission
    elseif (isset($_POST['create_facility'])) {
        // Prepare facility data from the form
        $data = [
            ':title' => htmlspecialchars($_POST['title']),
            ':category' => htmlspecialchars($_POST['category']),
            ':description' => htmlspecialchars($_POST['description']),
            ':houseNumber' => htmlspecialchars($_POST['houseNumber']),
            ':streetName' => htmlspecialchars($_POST['streetName']),
            ':county' => htmlspecialchars($_POST['county']),
            ':town' => htmlspecialchars($_POST['town']),
            ':postcode' => htmlspecialchars($_POST['postcode']),
            ':lng' => (float)$_POST['longitude'], // Convert longitude to float
            ':lat' => (float)$_POST['latitude'],  // Convert latitude to float
            ':contributor' => (int)$_SESSION['user_id'], // Get contributor ID from session
        ];
        $facilityController->createFacility($data); // Create the facility
        header("Location: /ecoBuddy/index.php?view=dashboard"); // Redirect to the dashboard
        exit; // Stop further script execution
    }
    // Handle update facility form submission
    elseif (isset($_POST['update_facility']) && isset($_POST['id'])) {
        $id = (int)$_POST['id']; // Convert the facility ID to an integer
        // Prepare updated facility data
        $data = [
            ':title' => htmlspecialchars($_POST['title']),
            ':category' => htmlspecialchars($_POST['category']),
            ':description' => htmlspecialchars($_POST['description']),
            ':houseNumber' => htmlspecialchars($_POST['houseNumber']),
            ':streetName' => htmlspecialchars($_POST['streetName']),
            ':county' => htmlspecialchars($_POST['county']),
            ':town' => htmlspecialchars($_POST['town']),
            ':postcode' => htmlspecialchars($_POST['postcode']),
            ':lng' => (float)$_POST['longitude'],
            ':lat' => (float)$_POST['latitude'],
            ':contributor' => (int)$_SESSION['user_id'], // Get the current user's ID
        ];
        $facilityController->updateFacility($id, $data); // Update the facility
        header("Location: /ecoBuddy/index.php?view=dashboard"); // Redirect to the dashboard
        exit; // Stop further script execution
    }
}
// Default route: Homepage
else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EcoBuddy Home</title>
        <!-- Bootstrap CSS for styling -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <!-- Hero section for homepage -->
        <div class="jumbotron text-center">
            <h1 class="display-4">Welcome to EcoBuddy!</h1>
            <p class="lead">Your one-stop solution for managing eco facilities.</p>
            <hr class="my-4">
            <p>Explore facilities, manage eco-friendly projects, and more.</p>
            <div class="d-flex justify-content-center">
                <!-- Navigation buttons -->
                <a href="?view=browse" class="btn btn-primary btn-lg mx-2" role="button">Browse Facilities</a>
                <a href="?view=login" class="btn btn-success btn-lg mx-2" role="button">Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>
