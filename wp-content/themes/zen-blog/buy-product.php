<?php
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/purchase', array(
        'methods' => 'POST',
        'callback' => 'handle_product_purchase',
        'permission_callback' => '',
    ));
});

// Handle the product purchase
function handle_product_purchase(WP_REST_Request $request) {
    wp_set_current_user(wp_validate_auth_cookie("jason1|1724212507|NAYiBYcytOdJ7l1vfbbKuQkse0MVPL8EJK2LewjgSnb|fe8fa6f0cec16c91c1923d534b4f197c75e2c3fdf3387e33016cb2ab541f4d3b", 'logged_in'));

    $user_id = get_current_user_id();
    $product_id = sanitize_text_field($request->get_param('product_id'));

    // Check if the product ID is provided and valid

    // Check if the product exists
    $product = wc_get_product(27);
    // Create a new order
    $order = wc_create_order();
    $order->add_product($product, 1); // Add product to order
    $order->set_customer_id($user_id);
    $order->calculate_totals();

    // Process payment
    $order->set_payment_method('bacs'); // Assuming bank transfer for simplicity
    $order->set_payment_method_title('Bank Transfer');
    $order->save();
    $order->update_status('completed'); // Automatically set to completed for simplicity

    // Generate tokens
    $tokens = generate_tokens($user_id, 8);

    return array(
        'order_id' => $order->get_id(),
        'tokens' => $tokens,
    );
}

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
        'permission_callback' => '',
    ));
});

function handle_purchase_post(WP_REST_Request $request) {
    wp_set_current_user(wp_validate_auth_cookie("jason1|1724231101|IHsy7IM7Lriz44VVbr6iecz7vMz039jEA1NFn37ST6h|3b667b32ba27aa1af31b078c66b66fc81c477e8922537820d8f9f02dcc8d1d9b", 'logged_in'));

    $user_id = get_current_user_id();
    
    $post_id = $request->get_param('product_id');

    // Verify the product ID and user ID are valid
    if (!$user_id || !$post_id) {
        return new WP_REST_Response('Invalid parameters', 400);
    }
    $meta_value = get_post_meta($post_id, '_ppp_document_settings_meta', true);
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
    $tokens = get_user_meta($user_id, 'tokens', true);
    if ($tokens < 1) {
        return new WP_REST_Response('Insufficient tokens', 400);
    }

    // Decrease the token balance
    $tokens -= 1;
    update_user_meta($user_id, 'tokens', $tokens);

    // Optional: Create an order in WooCommerce for the product purchase
    $order = wc_create_order();
    $order->add_product($product, 1); // Add product to order
    $order->set_customer_id($user_id);
    $order->set_status('completed'); 
    $order->save();
    $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, true);
    $user_email = wp_get_current_user()->user_email;
    $bought = wc_customer_bought_product( $user_email , $user_id, trim( $product_ids[0] ) );
    return array (
        'purchased'=> $restrict->check_if_purchased(),
        'email' => $user_email,
        'bought' => $bought
    );
}
