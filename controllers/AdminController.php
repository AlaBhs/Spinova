<?php
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../utils/seeder.php';
class AdminController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
   
    /**
     * Handle login requests
     */
    public function login($vars) {

        if (is_authenticated()) {
            redirect('/dashboard');
        }

        $this->seedDefaultAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $user = new User($this->db);
            $user->username = $username;
            
            if ($user->usernameExists()) {
                
                if ($user->verifyPassword($password)) {
                    
                    $_SESSION['user'] = [
                        'id' => $user->id,
                        'username' => $user->username
                    ];

                    if ($user->requiresPasswordChange()) {
                        flash('warning', 'You must change your default password');
                        redirect('/settings');
                    }
                    
                    flash('success', 'Logged in successfully');
                    redirect('/dashboard');
                } 
            } 
            
            flash('error', 'Invalid username or password');
            redirect('/login');
        }

        $vars['title'] = 'Login - Spinova URL Rotator';
        render_view('auth/login', $vars);
    }


    private function seedDefaultAdmin() {
        try {
            $user = new User($this->db);
            
            // Check if any user exists
            $query = 'SELECT COUNT(*) FROM users ';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $userCount = $stmt->fetchColumn();
    
            if ($userCount == 0) {
                $seeder = new Seeder();
                if ($seeder->seedAdminUser()) {
                    error_log("Default admin user created successfully");
                    flash('info', 'Default admin account created. Please login with credentials from .env');
                } else {
                    error_log("Failed to seed admin user");
                }
            }
        } catch (Exception $e) {
            error_log("Admin seeding check failed: " . $e->getMessage());
        }
    }
    /**
     * Handle logout
     */
    public function logout() {
        // Clear all session data
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        
        redirect('/login');
    }
    
    /**
     * Handle admin settings and password changes
     */
    public function settings($vars) {
        if (!is_authenticated()) {
            redirect('/login');
        }
    
        $user = new User($this->db);
        $user->id = $_SESSION['user']['id'];
        $errors = [];
        $success = false;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
    
            // Basic validation
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required';
            }
            if (empty($newPassword)) {
                $errors[] = 'New password is required';
            } elseif (strlen($newPassword) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }
    
            // Process if no errors
            if (empty($errors)) {
                // Verify current password
                if (!$user->verifyCurrentPassword($currentPassword)) {
                    $errors[] = 'Current password is incorrect';
                } 
                // Update password
                elseif ($user->updatePassword($newPassword)) {
                    $success = true;
                    // Clear force password change flag if set
                    if (isset($_SESSION['force_password_change'])) {
                        unset($_SESSION['force_password_change']);
                    }
                    flash('success', 'Password changed successfully!');
                } else {
                    $errors[] = 'Failed to update password';
                }
            }
        }
    
        // Prepare view data
        $vars['errors'] = $errors;
        $vars['success'] = $success;
        $vars['force_password_change'] = $user->requiresPasswordChange();
        
        render_view('auth/settings', $vars);
    }
    /**
     * Middleware to check if password needs changing
     */
    public function checkPasswordChange() {
        if (is_authenticated()) {
            $user = new User($this->db);
            $user->id = $_SESSION['user']['id'];
            $user->username = $_SESSION['user']['username'];
            
            if ($user->requiresPasswordChange() && 
                basename($_SERVER['SCRIPT_NAME']) !== 'settings.php') {
                flash('warning', 'You must change your default password');
                redirect('/settings');
            }
        }
    }
}
?>