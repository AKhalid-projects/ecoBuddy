<?php
class UserController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function login($username, $password) {
        $user = $this->userModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect based on user role
            if ($user['user_type'] === 'manager') {
                header("Location: /ecoBuddy/views/manager/dashboard.php");
            } else {
                header("Location: /ecoBuddy/index.php?view=browse");
            }
            exit;
        } else {
            echo "Invalid username or password.";
        }
    }

}
?>
