<?php
// Include the custom login API file
require get_template_directory() . '/authentication.php';
require get_template_directory() . '/protected_post.php';
require get_template_directory() . '/buy_product.php';
$allowed_origins = ['http://localhost:3000'];

function add_cors_http_headers() {
    global $allowed_origins;

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $origin = $_SERVER['HTTP_ORIGIN'];

        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Max-Age: 86400');
        exit(0);
    }
}

function my_theme_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'my_theme_setup');

add_action('send_headers', 'add_cors_http_headers', 15);