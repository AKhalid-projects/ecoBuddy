<?php
class UserController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function login($username, $password) {
        $user = $this->userModel->validateUser($username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: /ecoBuddy/views/facilities/browse.php");
        } else {
            echo "Invalid username or password.";
        }
    }
}
?>
