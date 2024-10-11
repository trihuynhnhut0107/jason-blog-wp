<?php

require get_template_directory() . '/authentication.php';
require get_template_directory() . '/protected_post-endpoint.php';
require get_template_directory() . '/buy-product.php';

function add_cors_http_header() {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $origin = $_SERVER['HTTP_ORIGIN'];
        $allowed_origins = ['http://localhost:3000']; 

        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
        }
    }
}
add_action('send_headers', 'add_cors_http_header', 15);