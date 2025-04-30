<?php
spl_autoload_register(function ($class_name) {
    // List of directories to search
    $directories = [
        __DIR__.'/models/',
        __DIR__.'/controllers/',
        __DIR__.'/utils/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory.$class_name.'.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    error_log("Autoload failed for class: ".$class_name);
});