<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/env.php';

class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $created_at;
    public $updated_at;

    public function __construct($db = null)
    {
        $this->conn = $db ?: (new Database())->connect();
    }

    public function create()
    {
        try {
            if (empty($this->username) || empty($this->password)) {
                throw new Exception('Username and password are required');
            }
            $query = 'INSERT INTO ' . $this->table . ' (username, password) VALUES (:username, :password)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }

            $error = implode(' ', $stmt->errorInfo());
            throw new Exception($error);
        } catch (Exception $e) {
            return false;
        }
    }

    public function usernameExists()
    {
        try {
            $query = 'SELECT id, username, password FROM ' . $this->table . ' 
                     WHERE username = :username LIMIT 1';

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username);

            if (!$stmt->execute()) {
                $error = implode(' ', $stmt->errorInfo());
                return false;
            }

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                $this->password = $row['password'];
                return true;
            }

            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyPassword($enteredPassword) {
        // Clean inputs
        $enteredPassword = trim($enteredPassword);
        $storedHash = trim($this->password);    
        // Basic validation
        if (empty($storedHash) || strlen($storedHash) < 60) {
            return false;
        }
        
        // Verification
        $isValid = password_verify($enteredPassword, $storedHash);
        
        return $isValid;
    }
    public function requiresPasswordChange()
    {
        // Check session flag
        if ($_SESSION['force_password_change'] ?? false) {
            return true;
        }

        // Check against default password
        $defaultPassword = Env::get('ADMIN_PASSWORD');
        if (!empty($defaultPassword) && !empty($this->password)) {
            return password_verify($defaultPassword, $this->password);
        }

        return false;
    }
    public function updatePassword($newPassword) {
        try {
            // Validate password length
            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters');
            }
    
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            // Update query
            $query = 'UPDATE ' . $this->table . ' 
                     SET password = :password 
                     WHERE id = :id';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $this->id);
            
            if ($stmt->execute()) {
                // Update the object property
                $this->password = $hashedPassword;
                return true;
            }
            
            throw new Exception('Failed to update password');
            
        } catch (Exception $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }
    public function verifyCurrentPassword($enteredPassword) {
        // Ensure we have the current hash loaded
        if (empty($this->password)) {
            $this->loadUserData();
        }
    
        // Clean inputs
        $enteredPassword = trim($enteredPassword);
        $storedHash = trim($this->password);    
        
        // Basic validation
        if (empty($storedHash) || strlen($storedHash) < 60) {
            return false;
        }
        
        // Verification
        return password_verify($enteredPassword, $storedHash);
    }
    
    private function loadUserData() {
        if (empty($this->id)) return false;
        
        $query = 'SELECT password FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->password = $row['password'];
            return true;
        }
        
        return false;
    }
}
