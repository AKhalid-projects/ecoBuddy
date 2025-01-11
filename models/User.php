<?php
// User class for managing user-related database operations.
class User {
    private $pdo; // Database connection object.
    // Constructor to inject PDO object for database interactions.
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // Fetch all users (with their roles).
    public function getAllUsers() {
        $stmt = $this->pdo->query("
            SELECT ecoUser.*, ecoUsertypes.name AS role 
            FROM ecoUser
            JOIN ecoUsertypes ON ecoUser.userType = ecoUsertypes.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return an associative array of all users with roles.
    }
    // Fetch a user by username (with their role) by reusing getUserWithRole.
    public function getUserByUsername($username) {
        return $this->getUserWithRole($username); // Calls getUserWithRole for consistency.
    }
    // Fetch a user by username (with role) - central logic for user details.
    public function getUserWithRole($username) {
        $stmt = $this->pdo->prepare("
            SELECT ecoUser.*, ecoUsertypes.name AS role
            FROM ecoUser
            JOIN ecoUsertypes ON ecoUser.userType = ecoUsertypes.id
            WHERE ecoUser.username = :username
        ");
        $stmt->execute([':username' => $username]); // Bind username to query.
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return single user record as an associative array.
    }
    // Validate user credentials and return user if valid.
    public function validateUser($username, $password) {
        $user = $this->getUserWithRole($username); // Get user data with role.
        if ($user && password_verify($password, $user['password'])) { // Verify password hash.
            return $user; // Return user if credentials are valid.
        }
        return false; // Return false if credentials are invalid.
    }
    // Create a new user in the database.
    public function createUser($username, $password, $email, $userType) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoUser (username, password, email, userType)
            VALUES (:username, :password, :email, :userType)
        ");
        $stmt->execute([
            ':username' => $username, // Bind username.
            ':password' => password_hash($password, PASSWORD_BCRYPT),  // Hash password for security.
            ':email' => $email,  // Bind email.
            ':userType' => $userType // Bind user type (e.g., 'manager' or 'user').
        ]);
        return $this->pdo->lastInsertId(); // Return the ID of the newly created user.
    }
    // Delete a user by ID
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ecoUser WHERE id = :id"); // Delete user by ID.
        $stmt->execute([':id' => $id]); // Bind user ID to query.
    }

    // Update user details (e.g., change password, email, or role)
    public function updateUser($id, $password = null, $email = null, $userType = null) {
        $fields = []; // Array to hold the fields that need to be updated.
        $params = [':id' => $id]; // Parameters array for binding values.

        if ($password) { // If password is provided, add it to the update list.
            $fields[] = "password = :password"; // Update password field.
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT); // Hash new password.
        }
        if ($email) { // If email is provided, add it to the update list.
            $fields[] = "email = :email"; // Update email field.
            $params[':email'] = $email; // Bind new email.
        }
        if ($userType) { // If user type is provided, add it to the update list.
            $fields[] = "userType = :userType"; // Update user type.
            $params[':userType'] = $userType; // Bind new user type (e.g., 'manager' or 'user').
        }
        // Only execute update if there are fields to modify.
        if (!empty($fields)) {
            $sql = "UPDATE ecoUser SET " . implode(', ', $fields) . " WHERE id = :id"; // Construct the SQL update query.
            $stmt = $this->pdo->prepare($sql); // Prepare the query.
            $stmt->execute($params); // Execute the query with bound parameters.
        }
    }
}
?>
