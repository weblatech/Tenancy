<?php

/**
 * Laravel PHP built-in server router.
 * 
 * When using `php -S 0.0.0.0:$PORT server.php`, this script routes
 * all non-file requests through public/index.php (Laravel front controller).
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Route everything else through Laravel's front controller
require __DIR__ . '/public/index.php';
