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
            'has_access' => $restrict->can_user_view_content(),
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

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/post/(?P<slug>[^/]+)', array(
        'methods' => 'GET',
        'callback' => 'get_single_post',
        'permission_callback' => 'permissionCheck',
    ));
});

function get_single_post(WP_REST_Request $request) {
    $slug = $request->get_param('slug') ? sanitize_title($request->get_param('slug')) : '';

    if (empty($slug)) {
        return new WP_REST_Response(array('message' => 'Slug not provided'), 400);
    }

    $post = get_page_by_path($slug, OBJECT, 'post');
    if (!$post) {
        return new WP_REST_Response(array('message' => 'Post not found'), 404);
    }

    // Increment view count
    $views = get_post_meta($post->ID, 'view_count', true) ?: 0;
    update_post_meta($post->ID, 'view_count', ++$views);

    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);
    $product_id = get_post_meta($post->ID, 'wc_pay_per_post_product_ids', true);

    return new WP_REST_Response(array(
        'post' => $post,
        'id' => $post->ID,
        'has_access' => $restrict->can_user_view_content(),
        'product_id' => $product_id,
        'view_count' => $views,  // Return the current view count
    ), 200);
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/top-views', array(
        'methods' => 'GET',
        'callback' => 'get_top_viewed_posts',
        'permission_callback' => '__return_true',  // Or define a custom permission callback if needed
    ));
});

function get_top_viewed_posts() {
    $args = array(
        'post_type' => 'post',
        'meta_key' => 'view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'posts_per_page' => 5,  // Limit to top 5
    );

    $query = new WP_Query($args);
    $posts = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'slug' => get_post_field('post_name', get_the_ID()),  // Add slug
                'view_count' => get_post_meta(get_the_ID(), 'view_count', true) ?: 0,
            );
        }
        wp_reset_postdata();
    }

    return new WP_REST_Response($posts, 200);
}