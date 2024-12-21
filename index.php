<?php
session_start();
require_once './config/db_connection.php';
require_once './models/User.php';
require_once './models/Facility.php';
require_once './controllers/UserController.php';
require_once './controllers/FacilityController.php';

// Initialize Models and Controllers
$userModel = new User($pdo);
$facilityModel = new Facility($pdo);
$userController = new UserController($userModel);
$facilityController = new FacilityController($facilityModel);

// Handle Routes
if (isset($_GET['view'])) {
    $view = htmlspecialchars($_GET['view']);

    if ($view === 'login') {
        include './views/users/login.php';
    } elseif ($view === 'logout') {
        session_destroy();
        header("Location: /ecoBuddy/index.php?view=login");
        exit;
    } elseif ($view === 'browse') {
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $categories = $facilityModel->getAllCategories(); // Fetch categories
        $facilities = $facilityController->browseFacilities($search, $category);
        include './views/facilities/browse.php';
    } elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'manager') {
        if ($view === 'add_facility') {
            $categories = $facilityModel->getAllCategories();
            include './views/manager/add_facility.php';
        } elseif ($view === 'edit_facility' && isset($_GET['id'])) {
            $facilityId = (int)$_GET['id'];
            $facility = $facilityModel->getFacilityById($facilityId);
            if (!$facility) {
                die("Facility with ID $facilityId not found.");
            }

            // Fetch categories for dropdown
            $categories = $facilityModel->getAllCategories();
            if (!$categories) {
                die("No categories found in the database.");
            }
            include './views/manager/edit_facility.php';
        } elseif ($view === 'delete_facility' && isset($_GET['id'])) {
            $facilityId = (int)$_GET['id'];
            $facilityController->deleteFacility($facilityId);
            header("Location: /ecoBuddy/index.php?view=dashboard");
            exit;
        } elseif ($view === 'dashboard') {
            $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
            $category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null;
            $categories = $facilityModel->getAllCategories(); // Fetch categories
            $facilities = $facilityController->browseFacilities($search, $category); // Get filtered facilities
            include './views/manager/dashboard.php';
        }

    } else {
        http_response_code(404); // Set HTTP response code to 404
        $file = './views/404.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<h1>404 - Page Not Found</h1><p>The 404 page is missing.</p>";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $userController->login($username, $password);
    } elseif (isset($_POST['create_facility'])) {
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
            ':contributor' => (int)$_SESSION['user_id'],
        ];
        $facilityController->createFacility($data);
        header("Location: /ecoBuddy/index.php?view=dashboard");
        exit;
    } elseif (isset($_POST['update_facility']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
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
            ':contributor' => (int)$_SESSION['user_id'],
        ];
        $facilityController->updateFacility($id, $data);
        header("Location: /ecoBuddy/index.php?view=dashboard");
        exit;
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EcoBuddy Home</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Welcome to EcoBuddy!</h1>
            <p class="lead">Your one-stop solution for managing eco facilities.</p>
            <hr class="my-4">
            <p>Explore facilities, manage eco-friendly projects, and more.</p>
            <div class="d-flex justify-content-center">
                <a href="?view=browse" class="btn btn-primary btn-lg mx-2" role="button">Browse Facilities</a>
                <a href="?view=login" class="btn btn-success btn-lg mx-2" role="button">Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}

?>
