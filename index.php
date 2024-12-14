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
        $facilities = $facilityController->browseFacilities();
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
            include './views/manager/dashboard.php';
        }
    } else {
        http_response_code(404);
        include './views/404.php';
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
    echo "Welcome to EcoBuddy! <a href='?view=browse'>Browse Facilities</a> | <a href='?view=login'>Login</a>";
}
?>
