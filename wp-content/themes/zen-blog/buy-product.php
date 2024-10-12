<?php
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/purchase', array(
        'methods' => 'POST',
        'callback' => 'handle_product_purchase',
        'permission_callback' => '',
    ));
});


// Generate tokens and update user meta
function generate_tokens($user_id, $amount) {
    $tokens = get_user_meta($user_id, 'tokens', true);

    // Initialize tokens if not already set
    if (!$tokens) {
        $tokens = 0;
    }

    // Add the purchased tokens
    $tokens += $amount;
    update_user_meta($user_id, 'tokens', $tokens);

    return $tokens;
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/buypost', array(
        'methods' => 'POST',
        'callback' => 'handle_purchase_post',
    ));
});


function handle_purchase_post(WP_REST_Request $request) {

    if (!(isset($_COOKIE[LOGGED_IN_COOKIE]) && wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in'))) {
        return WP_REST_Response('No user cookie', 400);
    } 
    $user_id = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in');
    $post_id = $request->get_param('post_id');

    // Verify the product ID and user ID are valid
    if (!$user_id || !$post_id) {
        return new WP_REST_Response('Invalid parameters', 400);
    }

    // Get the value of the custom meta field
    $meta_value = get_post_meta($post_id, '_ppp_document_settings_meta', true);
    // If it's stored as JSON, decode it
    $meta_object = json_decode($meta_value, true);
    $product_ids = array_map(function($item) {
        return $item['value'];
    }, $meta_object['product_ids']);

    $product = wc_get_product($product_ids[0]);
    if (!$product || !$product->exists()) {
        return new WP_REST_Response('Product does not exist', 404);
    }

    // Get the user object
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return new WP_REST_Response('User does not exist', 404);
    }

    // Check if the user has enough tokens
    $tokens = get_user_meta($user_id, 'token', true);
    
    if(!$tokens) {
        return new WP_REST_Response('No tokens available', 400);
    }
    if ($tokens < 1) {
        return new WP_REST_Response('Insufficient tokens', 400);
    }

    
    // Decrease the token balance
    $new_token = $tokens - 1;

    
    update_user_meta($user_id, 'token', $new_token);

    // Optional: Create an order in WooCommerce for the product purchase
    $order = wc_create_order();
    $order->add_product($product, 1); // Add product to order
    $order->set_customer_id($user_id);
    $order->set_status('completed'); 
    $order->save();
    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, true);
    $user_email = $user->user_email;
    $bought = wc_customer_bought_product( $user_email , $user_id, trim( $product_ids[0] ) );
    return array (
        'purchased'=> $restrict->check_if_purchased($user_id),
        'email' => $user_email,
        'bought' => $bought
    );
}


add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/get-token', array(
        'methods' => 'GET',
        'callback' => 'get_user_tokens',
        'permission_callback' => '',
    ));
});


function get_user_tokens() {
	$cookie = isset($_COOKIE['user']) ? $_COOKIE['user'] : '';
    wp_set_current_user(wp_validate_auth_cookie($cookie, 'logged_in'));
	$user_id = get_current_user_id();
    $token_meta = get_user_meta($user_id, 'token', true);

    return array('token' => $token_meta);
}
