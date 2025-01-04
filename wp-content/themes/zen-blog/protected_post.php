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
    wp_set_current_user($user_id);
    $search = $request->get_param('search') ? sanitize_text_field($request->get_param('search')) : '';

    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10;

    $page = max(1, $page);
    $per_page = max(1, min(100, $per_page));

    $categories = $request->get_param('categories');
    if ($categories) {
        $categories = array_map('intval', explode(',', $categories));
    }

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        's'              => $search,
    );

    if (!empty($categories)) {
        $args['category__in'] = $categories;
    }

    $query = new WP_Query($args);
    $posts = array();

    foreach ($query->posts as $post) {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);
        $meta_value = get_post_meta($post->ID, '_ppp_document_settings_meta', true);
        $meta_data = $meta_value ? json_decode($meta_value, true) : null;

        $product_id = null;
        if (!empty($meta_data['product_ids']) && is_array($meta_data['product_ids'])) {
            $product_id = $meta_data['product_ids'][0]['value'] ?? null;
        }

        $product_price = null;
        if ($product_id) {
            $product = wc_get_product($product_id);
            $product_price = $product ? $product->get_price() : null;
        }

        $featured_image_url = get_the_post_thumbnail_url($post->ID, 'full');
        $view_count = get_post_meta($post->ID, 'view_count', true) ?: 0;
        $reading_time = get_post_meta($post->ID, 'rt_reading_time', true) ?: 'N/A';

        if (!$restrict->can_user_view_content()) {
            $post->post_content = wp_trim_words(wp_strip_all_tags($post->post_content), 200, '');
        }

        $posts[] = array(
            'post'           => $post,
            'id'             => $post->ID,
            'has_access'     => $restrict->can_user_view_content(),
            'product_id'     => $product_id,
            'product_price'  => $product_price,
            'featured_image' => $featured_image_url,
            'view_count'     => $view_count,
            'reading_time'   => $reading_time,
        );
    }

    $response = array(
        'current_page' => $page,
        'per_page'     => $per_page,
        'total_posts'  => $query->found_posts,
        'total_pages'  => $query->max_num_pages,
        'posts'        => $posts,
    );

    return new WP_REST_Response($response, 200);
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/post/(?P<slug>[^/]+)', array(
        'methods'  => 'GET',
        'callback' => 'get_single_post'
    ));
});

function get_single_post(WP_REST_Request $request) {
    $user_id = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in');
    wp_set_current_user($user_id);

    $slug = $request->get_param('slug') ? sanitize_title($request->get_param('slug')) : '';
    if (empty($slug)) {
        return new WP_REST_Response(array('message' => 'Slug not provided'), 400);
    }

    $post = get_page_by_path($slug, OBJECT, 'post');
    if (!$post) {
        return new WP_REST_Response(array('message' => 'Post not found'), 404);
    }

    $views = get_post_meta($post->ID, 'view_count', true) ?: 0;
    update_post_meta($post->ID, 'view_count', ++$views);

    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post->ID, true);
    $product_id = get_post_meta($post->ID, 'wc_pay_per_post_product_ids', true);
    $featured_image_url = get_the_post_thumbnail_url($post->ID, 'full');

    $product = null;
    $product_price = null;
    if (!empty($product_id) && is_array($product_id)) {
        $product = wc_get_product((int)$product_id[0]);
        $product_price = $product ? $product->get_price() : null;
    }

    if (!$restrict->can_user_view_content()) {
        $post->post_content = wp_trim_words(wp_strip_all_tags($post->post_content), 200, '');
    }    

    return new WP_REST_Response(array(
        'user'          => $user_id,
        'post'          => $post,
        'id'            => $post->ID,
        'has_access'    => $restrict->can_user_view_content(),
        'product_id'    => $product_id,
        'view_count'    => $views,
        'featured_image'=> $featured_image_url,
        'product_price' => $product_price,
    ), 200);
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/top-views', array(
        'methods'  => 'GET',
        'callback' => 'get_top_viewed_posts',
        'permission_callback' => '__return_true',
    ));
});

function get_top_viewed_posts() {
    $args = array(
        'post_type'      => 'post',
        'meta_key'       => 'view_count',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'posts_per_page' => 5,
    );

    $query = new WP_Query($args);
    $posts = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = array(
                'id'         => get_the_ID(),
                'title'      => get_the_title(),
                'slug'       => get_post_field('post_name', get_the_ID()),
                'view_count' => get_post_meta(get_the_ID(), 'view_count', true) ?: 0,
            );
        }
        wp_reset_postdata();
    }

    return new WP_REST_Response($posts, 200);
}
