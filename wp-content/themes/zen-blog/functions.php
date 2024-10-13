<?php
// Include the custom login API file
require get_template_directory() . '/authentication.php';
require get_template_directory() . '/protected_post.php';
require get_template_directory() . '/buy_product.php';
$allowed_origins = ['http://localhost:3000'];

function add_cors_http_headers() {
    global $allowed_origins;

    // Check if the request has an "Origin" header
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $origin = $_SERVER['HTTP_ORIGIN'];

        // Only allow specific origins
        if (in_array($origin, $allowed_origins)) {
            // Allow origin
            header("Access-Control-Allow-Origin: $origin");
            // Allow credentials (cookies, authorization headers, etc.)
            header("Access-Control-Allow-Credentials: true");
            // Allowed HTTP methods
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            // Allowed headers from client requests
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        }
    }

    // Handle OPTIONS requests (preflight requests)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // Allow the browser to cache the preflight response for 1 day (86400 seconds)
        header('Access-Control-Max-Age: 86400');
        // End the script to avoid sending further content for preflight
        exit(0);
    }
}

add_action('send_headers', 'add_cors_http_headers', 15);