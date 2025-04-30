<?php
class Env {
    private static $vars = [];

    public static function load($path = __DIR__ . '/../.env') {
        if (!file_exists($path)) {
            throw new Exception('.env file not found');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Split name and value
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) {
                $value = $matches[1];
            }

            self::$vars[$name] = $value;
        }
    }

    public static function get($key, $default = null) {
        return self::$vars[$key] ?? $default;
    }
}

// Load environment variables automatically when this file is included
Env::load();
?>