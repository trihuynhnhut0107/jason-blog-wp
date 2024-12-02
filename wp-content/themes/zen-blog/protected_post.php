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

    // Retrieve category IDs from the request, if provided
    $categories = $request->get_param('categories');
    if ($categories) {
        $categories = array_map('intval', explode(',', $categories)); // Convert comma-separated list to an array of integers
    }

    // Query posts with pagination and optional category filtering
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'paged' => $page,
        's' => $search, // Add the search term to the query
    );

    // Add category filtering if categories are provided
    if (!empty($categories)) {
        $args['category__in'] = $categories;
    }

    $query = new WP_Query($args);
    
    // Prepare response
    $posts = array();
        
    foreach ($query->posts as $post) {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);

        // Retrieve the _ppp_document_settings_meta and decode it
        $meta_value = get_post_meta($post->ID, '_ppp_document_settings_meta', true);
        $meta_data = $meta_value ? json_decode($meta_value, true) : null;

        // Extract the product ID from the meta field
        $product_id = null;
        if (!empty($meta_data['product_ids']) && is_array($meta_data['product_ids'])) {
            $product_id = $meta_data['product_ids'][0]['value'] ?? null;
        }

        // Get the product price if a product ID exists
        $product_price = null;
        if ($product_id) {
            $product = wc_get_product($product_id);
            $product_price = $product ? $product->get_price() : null; // Retrieve the price of the product
        }

        // Get the featured image URL
        $featured_image_url = get_the_post_thumbnail_url($post->ID, 'full'); // You can replace 'full' with other image sizes if needed

        // Get the view count or default to 0
        $view_count = get_post_meta($post->ID, 'view_count', true) ?: 0;

        // Get the reading time from Reading Time WP plugin
        $reading_time = get_post_meta($post->ID, 'rt_reading_time', true) ?: 'N/A';

        $posts[] = array(
            'post' => $post,
            'id' => $post->ID,
            'has_access' => $restrict->can_user_view_content(),
            'product_id' => $product_id, // Include the product_id in the response
            'product_price' => $product_price, // Include the product price in the response
            'featured_image' => $featured_image_url,  // Add the featured image URL to the response
            'view_count' => $view_count, // Add the view count to the response
            'reading_time' => $reading_time, // Add the reading time to the response
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
        'callback' => 'get_single_post'
    ));
});

function get_single_post(WP_REST_Request $request) {
    // Get the slug parameter from the request
    $slug = $request->get_param('slug') ? sanitize_title($request->get_param('slug')) : '';

    // Check if slug is provided
    if (empty($slug)) {
        return new WP_REST_Response(array('message' => 'Slug not provided'), 400);
    }

    // Get the post object by slug
    $post = get_page_by_path($slug, OBJECT, 'post');
    
    // If no post is found, return a 404 error
    if (!$post) {
        return new WP_REST_Response(array('message' => 'Post not found'), 404);
    }

    // Increment the view count for the post
    $views = get_post_meta($post->ID, 'view_count', true) ?: 0;
    update_post_meta($post->ID, 'view_count', ++$views);

    // Instantiate the Woocommerce Pay Per Post restriction object
    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);
    
    // Get the product ID associated with the post
    $product_id = get_post_meta($post->ID, 'wc_pay_per_post_product_ids', true);

    // Get the URL of the featured image
    $featured_image_url = get_the_post_thumbnail_url($post->ID, 'full');  // 'full' is the image size; you can choose other sizes

    // Return the response with post details, including featured image URL
    return new WP_REST_Response(array(
        'post' => $post,
        'id' => $post->ID,
        'has_access' => $restrict->can_user_view_content(),
        'product_id' => $product_id,
        'view_count' => $views,  // Return the current view count
        'featured_image' => $featured_image_url,  // Add the featured image URL to the response
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