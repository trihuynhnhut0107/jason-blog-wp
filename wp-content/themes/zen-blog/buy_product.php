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

    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, true);
    
    // Get the product ID associated with the post
    $product_id = get_post_meta($post_id, 'wc_pay_per_post_product_ids', true);

// Ensure $product_id[0] exists and is valid
    if (!empty($product_id[0])) {
        // Convert $product_id[0] to an integer
        $product_id_number = (int) $product_id[0];

        // Fetch the product object using the numeric ID
        $product = wc_get_product($product_id_number);

        // Check if the product object is valid
        if ($product) {
            // Get the product price
            $product_price = $product->get_price();

            
        }
        else {
            return new WP_REST_Response('Invalid parameters', 404);
        }
    }
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return new WP_REST_Response('User are not logged in', 404);
    }
    $tokens = get_user_meta($user_id, 'token', true);
    update_user_meta($user_id, 'token', 10000);
    if(!$tokens) {
        return new WP_REST_Response('No tokens available', 400);
    }
    if ($tokens < $product_price) {
        return new WP_REST_Response('Insufficient tokens', 400);
    }

    // $new_token = $tokens - $product_price;

    
    

    $order = wc_create_order();
    $order->add_product($product, 1);
    $order->set_customer_id($user_id);
    $order->set_status('completed'); 
    $order->save();
    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, true);
    $user_email = $user->user_email;
    $bought = wc_customer_bought_product( $user_email , $user_id, trim( $product_id_number ) );
    return array (
        'purchased'=> $restrict->check_if_purchased($user_id),
        'email' => $user_email,
        'bought' => $bought,
        'id' => $product_id_number
    );
}


function get_user_tokens() {
   
    $user_id = get_current_user_id();
    $token_meta = get_user_meta($user_id, 'token', true);

    return array('token' => $token_meta);
}
