<?php
// This class is responsible for handling user-related actions like login, logout, and registration.
class UserController {
    private $userModel;  // Holds the User model for database operations.

    // Constructor to initialize the User model.
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    /**
     * Handles user login.
     *
     * @param string $username The username input.
     * @param string $password The password input.
     */
    public function login($username, $password) {
        // Fetch the user data along with their role (e.g., 'manager' or 'user') from the database.
        $user = $this->userModel->getUserWithRole($username);

        // Verify that the user exists and the password is correct.
        if ($user && password_verify($password, $user['password'])) {
            // Store user information in session variables for the current session.
            $_SESSION['user_id'] = $user['id'];             // Store the user's ID.
            $_SESSION['username'] = $user['username'];       // Store the user's username.
            $_SESSION['user_type'] = strtolower($user['role']); // Store the user's role in lowercase.

            // Redirect the user based on their role:
            // If the user is a manager, they are sent to the manager dashboard.
            // Otherwise, they are sent to the general browsing page.
            if ($_SESSION['user_type'] === 'manager') {
                header("Location: /ecoBuddy/index.php?view=dashboard"); // Manager's dashboard.
            } else {
                header("Location: /ecoBuddy/index.php?view=browse");    // General user browsing page.
            }
            exit; // Ensure the script stops executing after the redirect.
        } else {
            // If the username or password is incorrect, display an error message.
            echo "Invalid username or password.";
        }
    }

    /**
     * Handles user logout.
     */
    public function logout() {
        // Destroys the current session, logging the user out.
        session_destroy();
        // Redirect the user to the login page.
        header("Location: /ecoBuddy/index.php?view=login");
        exit; // Stop further execution after the redirect.
    }

    /**
     * Handles user registration.
     *
     * @param string $username The username to register.
     * @param string $password The password for the user.
     * @param string $email The email of the user.
     * @param string $userType The role of the user (e.g., 'manager' or 'user').
     */
    public function register($username, $password, $email, $userType) {
        // Try to create a new user and catch any errors during the process.
        try {
            // Call the model's createUser function to insert the new user into the database.
            $userId = $this->userModel->createUser($username, $password, $email, $userType);
            // Display a success message if registration is successful.
            echo "User registered successfully with ID: " . $userId;
        } catch (Exception $e) {
            // Display an error message if something goes wrong during registration.
            echo "Error registering user: " . $e->getMessage();
        }
    }
}
?>
