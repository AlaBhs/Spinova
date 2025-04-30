<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../models/User.php';

class Seeder {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function seedAdminUser() {
        try {
            $username = Env::get('ADMIN_USERNAME');
            $password = Env::get('ADMIN_PASSWORD');
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            if (empty($username) || empty($password)) {
                throw new Exception('Admin credentials not configured in .env');
            }

            $user = new User($this->db);
            $user->username = $username;
            
            // Check if admin already exists
            if ($user->usernameExists()) {
                return false;
            }

            // Create new admin
            $user->password = $password; // Will be hashed in the User model
            if ($user->create()) {
                return true;
            }

            throw new Exception("Failed to create admin user");

        } catch (Exception $e) {
            error_log("Seeder error: " . $e->getMessage());
            return false;
        }
    }
}