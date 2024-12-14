<?php
session_start();
require_once './config/db_connection.php';
require_once './models/User.php';
require_once './models/Facility.php';
require_once './controllers/UserController.php';
require_once './controllers/FacilityController.php';

// Handle Routes
if (isset($_GET['view'])) {
    $view = htmlspecialchars($_GET['view']);

    if ($view === 'login') {
        $userModel = new User($pdo);
        $userController = new UserController($userModel);
        include './views/users/login.php';
    } elseif ($view === 'browse') {
        $facilityModel = new Facility($pdo);
        $facilityController = new FacilityController($facilityModel);
        $facilities = $facilityController->browseFacilities();
        include './views/facilities/browse.php';
    } else {
        http_response_code(404);
        include './views/404.php';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $userModel = new User($pdo);
        $userController = new UserController($userModel);
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $userController->login($username, $password);
    }
} else {
    echo "Welcome to EcoBuddy! <a href='?view=browse'>Browse Facilities</a> | <a href='?view=login'>Login</a>";
}
?>
