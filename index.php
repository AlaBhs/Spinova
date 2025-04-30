<?php
// Start session and load configuration
require __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/install.php';
require_once __DIR__ . '/utils/helpers.php';

// Initialize session with secure settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => Env::get('APP_ENV') === 'production',
    'cookie_samesite' => 'Strict'
]);

// Error reporting based on environment
if (Env::get('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database connection
try {
    $db = (new Database())->connect();
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

// Flash messages implementation
if (!isset($_SESSION['flash'])) {
    $_SESSION['flash'] = [];
}

// Make flash messages available to views and clear after
$flash_messages = $_SESSION['flash'] ?? [];
$_SESSION['flash'] = [];

// Set common variables for views
$view_vars = [
    'currentUser' => $_SESSION['user'] ?? null,
    'error' => $flash_messages['error'] ?? null,
    'success' => $flash_messages['success'] ?? null,
    'title' => 'Spinova URL Rotator'
];

// Route definitions
$routes = [
    // Admin routes
    '/login' => ['controller' => 'admin', 'action' => 'login'],
    '/logout' => ['controller' => 'admin', 'action' => 'logout'],
    '/settings' => ['controller' => 'admin', 'action' => 'settings'],

    // Archive routes
    '/archive' => ['controller' => 'archive', 'action' => 'index'],
    '/archive/([a-zA-Z0-9]+)' => ['controller' => 'archive', 'action' => 'archive', 'params' => ['link']],
    '/archive/delete/([a-zA-Z0-9]+)' => ['controller' => 'archive', 'action' => 'destroy', 'params' => ['link']],
    '/archive/restore/([a-zA-Z0-9]+)' => ['controller' => 'archive', 'action' => 'restore', 'params' => ['link']],

    // Main routes
    '/' => ['controller' => 'main', 'action' => 'home'],
    '/dashboard' => ['controller' => 'main', 'action' => 'index'],
    '/create' => ['controller' => 'main', 'action' => 'create'],
    '/edit/([a-zA-Z0-9]+)' => ['controller' => 'main', 'action' => 'edit', 'params' => ['id']],
    '/delete/([a-zA-Z0-9]+)' => ['controller' => 'main', 'action' => 'destroy', 'params' => ['link']],
    '/([a-zA-Z0-9]+)' => ['controller' => 'main', 'action' => 'redirect', 'params' => ['short']]
];

// Route matching
$matched = false;
foreach ($routes as $pattern => $route) {
    if (preg_match('#^' . $pattern . '$#', $request_uri, $matches)) {
        $matched = true;

        // Extract parameters
        $params = [];
        if (isset($route['params'])) {
            foreach ($route['params'] as $i => $param) {
                $params[$param] = $matches[$i + 1];
            }
        }

        // Build controller path
        $controller_file = __DIR__ . '/controllers/' . ucfirst($route['controller']) . 'Controller.php';

        if (file_exists($controller_file)) {
            require_once $controller_file;
            $controller_class = ucfirst($route['controller']) . 'Controller';
            $controller = new $controller_class($db);

            // Call action method
            $action = $route['action'];
            if (method_exists($controller, $action)) {
                // Merge view variables
                $view_vars = array_merge($view_vars, $params);

                // Call controller action
                $controller->$action($view_vars);
                break;
            }
        }
    }
}

// 404 Not Found
if (!$matched) {
    $view_vars['title'] = "404 Page Not Found - Spinova URL Rotator";
    render_view('template/error', $view_vars);
}

// Helper function to render views
function render_view($view, $vars = [])
{
    extract($vars);
    require __DIR__ . '/views/' . $view . '.php';
    exit;
}
