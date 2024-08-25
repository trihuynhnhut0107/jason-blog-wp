<?php
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;
// Register a custom REST route for login
add_action('rest_api_init', function() {
    register_rest_route('custom/v1', '/protected_check', array(
        'methods' => 'GET',
        'callback' => 'is_protected_check',
        'permission_callback' => '__return_true',
    ));
});

// Callback function for login
function is_protected_check($request) {
    
    $is_protected = Woocommerce_Pay_Per_Post_Helper::is_protected(1);
    return $is_protected;
}


add_action('rest_api_init', function() {
    register_rest_route('custom/v1', '/has_access_checked', array(
        'methods' => 'GET',
        'callback' => 'is_allowed_admin',
        'permission_callback' => '__return_true',
    ));
});

// Callback function for login
function is_allowed_admin($request) {
    wp_set_current_user(wp_validate_auth_cookie("jason|1724207765|OB7dkEvDHCmj9iItT1GHv6mZpNpzuPS3PZfohqBEGpL|d8600f05257ae20b0b822b1c0c8ebf99cfcfd8a2b33e57376875ba69b3364e20", 'logged_in'));
    $current_user = wp_get_current_user();

    $is_allowed = Woocommerce_Pay_Per_Post_Protection_Checks::check_if_admin_user_have_access();
    return $is_allowed;
}

// add_action('rest_api_init', function() {
//     register_rest_route('custom/v1', '/has_access_checked', array(
//         'methods' => 'GET',
//         'callback' => 'is_allowed_admin',
//         'permission_callback' => '__return_true',
//     ));
// });

add_action('rest_api_init', function() {
    register_rest_route('custom/v1', '/get-post-list', array(
        'methods' => 'GET',
        'callback' => 'get_paginated_posts',
        'permission_callback' => '__return_true',
    ));
});


function get_paginated_posts(WP_REST_Request $request) {
	$cookie = isset($_COOKIE['user']) ? $_COOKIE['user'] : '';
    wp_set_current_user(wp_validate_auth_cookie($cookie, 'logged_in'));
    $current_user = wp_get_current_user();
	
	$search = $request->get_param('search') ? sanitize_text_field($request->get_param('search')) : '';

    // Retrieve pagination parameters from the request
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10;

    // Ensure page and per_page are valid
    $page = max(1, $page);
    $per_page = max(1, min(100, $per_page)); // Limit per_page to a maximum of 100

    // Query posts with pagination
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'paged' => $page,
		's' => $search, // Add the search term to the query
    );

    $query = new WP_Query($args);
    
    // Prepare response
    $posts = array();
        
    foreach ($query->posts as $post) {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);
        $posts[] = array(
            'post' => $post,
            'id' => $post->ID,
            'has_access' => $restrict->can_user_view_content(),
        );
    }

    // Prepare response object
    $response = array(
        'current_page' => $page,
        'per_page' => $per_page,
        'total_posts' => $query->found_posts,
        'total_pages' => $query->max_num_pages,
        'posts' => $posts,
    );

    return new WP_REST_Response($response, 200);
}