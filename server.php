<?php
require __DIR__.'/autoload.php';
$public_dir = __DIR__.'/public';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Try to serve static files first
$file_path = $public_dir.$request_uri;
if (file_exists($file_path) && is_file($file_path)) {
    // Set MIME types
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'ico' => 'image/x-icon'
    ];
    
    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
    if (isset($mime_types[$ext])) {
        header('Content-Type: '.$mime_types[$ext]);
    }
    
    readfile($file_path);
    exit;
}

// Fall back to your main application router
require __DIR__.'/index.php';