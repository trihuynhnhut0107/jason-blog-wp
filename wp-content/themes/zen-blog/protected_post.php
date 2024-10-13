<?php
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;


add_action('rest_api_init', function() {
    register_rest_route('custom/v1', '/get-post-list', array(
        'methods' => 'GET',
        'callback' => 'get_paginated_posts',
        'permission_callback' => 'permissionCheck',
    ));
});


function get_paginated_posts(WP_REST_Request $request) {
    $user_id = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in');
	
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
		$product_id = get_post_meta($post->ID, 'wc_pay_per_post_product_ids', true);
        $posts[] = array(
            'post' => $post,
            'id' => $post->ID,
            'has_access' => $restrict->can_user_view_content($user_id),
			'product_id' => $product_id, // Include the product_id in the response
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