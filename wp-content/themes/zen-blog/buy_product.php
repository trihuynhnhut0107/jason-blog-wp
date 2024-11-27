<?php
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/buypost', array(
        'methods' => 'POST',
        'callback' => 'handle_purchase_post',
        'permission_callback' => 'permissionCheck',
    ));
    register_rest_route('custom/v1', '/get-token', array(
        'methods' => 'GET',
        'callback' => 'get_user_tokens',
        'permission_callback' => 'permissionCheck',
    ));
});

function generate_tokens($user_id, $amount) {
    $tokens = get_user_meta($user_id, 'tokens', true);

    if (!$tokens) {
        $tokens = 0;
    }

    $tokens += $amount;
    update_user_meta($user_id, 'tokens', $tokens);

    return $tokens;
}



function handle_purchase_post(WP_REST_Request $request) {

    $user_id = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in');
    $post_id = $request->get_param('post_id');

    if (!$post_id) {
        return new WP_REST_Response('Invalid parameters', 400);
    }
    $user_id = get_current_user_id();
    $meta_value = get_post_meta($post_id, '_ppp_document_settings_meta', true);

    $meta_object = json_decode($meta_value, true);
    $product_ids = array_map(function($item) {
        return $item['value'];
    }, $meta_object['product_ids']);

    $product = wc_get_product($product_ids[0]);
    if (!$product || !$product->exists()) {
        return new WP_REST_Response('Product does not exist', 404);
    }
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return new WP_REST_Response('User are not logged in', 404);
    }
    $tokens = get_user_meta($user_id, 'token', true);
    
    if(!$tokens) {
        return new WP_REST_Response('No tokens available', 400);
    }
    if ($tokens < 1) {
        return new WP_REST_Response('Insufficient tokens', 400);
    }

    $new_token = $tokens - 1;

    
    update_user_meta($user_id, 'token', $new_token);

    $order = wc_create_order();
    $order->add_product($product, 1);
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


function get_user_tokens() {
   
    $user_id = get_current_user_id();
    $token_meta = get_user_meta($user_id, 'token', true);

    return array('token' => $token_meta);
}
