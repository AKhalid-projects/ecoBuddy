<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all users (with their roles)
    public function getAllUsers() {
        $stmt = $this->pdo->query("
            SELECT ecoUser.*, ecoUsertypes.name AS role 
            FROM ecoUser
            JOIN ecoUsertypes ON ecoUser.userType = ecoUsertypes.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a user by username (with their role)
    public function getUserByUsername($username) {
        return $this->getUserWithRole($username);
    }

    // Fetch a user by username (with role) - single point of logic
    public function getUserWithRole($username) {
        $stmt = $this->pdo->prepare("
            SELECT ecoUser.*, ecoUsertypes.name AS role
            FROM ecoUser
            JOIN ecoUsertypes ON ecoUser.userType = ecoUsertypes.id
            WHERE ecoUser.username = :username
        ");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Validate user credentials and return user if valid
    public function validateUser($username, $password) {
        $user = $this->getUserWithRole($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Valid credentials
        }
        return false; // Invalid credentials
    }

    // Create a new user
    public function createUser($username, $password, $email, $userType) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoUser (username, password, email, userType)
            VALUES (:username, :password, :email, :userType)
        ");
        $stmt->execute([
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':email' => $email,
            ':userType' => $userType
        ]);
        return $this->pdo->lastInsertId();
    }

    // Delete a user by ID
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ecoUser WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    // Update user details (e.g., change password, email, or role)
    public function updateUser($id, $password = null, $email = null, $userType = null) {
        $fields = [];
        $params = [':id' => $id];

        if ($password) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }
        if ($email) {
            $fields[] = "email = :email";
            $params[':email'] = $email;
        }
        if ($userType) {
            $fields[] = "userType = :userType";
            $params[':userType'] = $userType;
        }

        // Only update if there are fields to modify
        if (!empty($fields)) {
            $sql = "UPDATE ecoUser SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }
    }
}
?>
