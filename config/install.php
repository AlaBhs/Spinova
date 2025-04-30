<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/db.php';

function createAdminAccount($db)
{
    // Get credentials from .env
    $username = Env::get('ADMIN_USERNAME');
    $password = Env::get('ADMIN_PASSWORD');
    if (empty($username) || empty($password)) {
        throw new Exception("❌ Error: ADMIN_USERNAME or ADMIN_PASSWORD not set in .env");
    }

    // Check if admin already exists
    $user = new User($db);
    $user->username = $username;

    if ($user->usernameExists()) {
        return "✅ Admin account already exists (no action taken).";
    }

    // Create admin (with hashed password)
    $user->password = password_hash($password, PASSWORD_BCRYPT);
    
    if ($user->create()) {
        return "✅ Admin account created successfully!";
    } else {
        throw new Exception("❌ Failed to create admin account.");
    }
}
