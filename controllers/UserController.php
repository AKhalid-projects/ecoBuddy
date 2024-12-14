<?php
class UserController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function login($username, $password) {
    // Fetch user with their role
    $user = $this->userModel->getUserWithRole($username);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = strtolower($user['role']); // 'manager' or 'user'

        // Redirect based on user type
        if ($_SESSION['user_type'] === 'manager') {
            header("Location: /ecoBuddy/index.php?view=dashboard");
        } else {
            header("Location: /ecoBuddy/index.php?view=browse");
        }
        exit;
    } else {
        echo "Invalid username or password.";
    }
}


    public function logout() {
        // Destroy session and redirect to login page
        session_destroy();
        header("Location: /ecoBuddy/index.php?view=login");
        exit;
    }

    public function register($username, $password, $email, $userType) {
        // Create a new user
        try {
            $userId = $this->userModel->createUser($username, $password, $email, $userType);
            echo "User registered successfully with ID: " . $userId;
        } catch (Exception $e) {
            echo "Error registering user: " . $e->getMessage();
        }
    }
}
?>
