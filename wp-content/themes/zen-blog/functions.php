<?php
// Include the custom login API file
require get_template_directory() . '/login-endpoint.php';
require get_template_directory() . '/protected_post-endpoint.php';
require get_template_directory() . '/buy-product.php';

function add_cors_http_header(){
header("Access-Control-Allow-Origin: * ");
}
add_action('init','add_cors_http_header');



// add_action('save_post', 'create_wc_product_on_post_publish', 10, 3);

// function create_wc_product_on_post_publish($post_id, $post, $update) {
//     // Only create product for posts, not custom post types or revisions
//     if ($post->post_type != 'post' || $update) {
//         return;
//     }

//     // Create the WooCommerce product
//     $product = new WC_Product_Simple();
//     $product->set_name($post->post_title); // Set the product title to the post title
//     $product->set_status('publish'); // Publish the product
//     $product->set_catalog_visibility('visible'); // Set product visibility

//     $product->save();
//     $post_title = $post->post_title;
//     $ppp_document_settings_meta = array(
//         "product_ids" => array(array("label" => "$post_title - [#$post_id]", "value" => $post_id)),
//         "delay_restriction_enable" => "",
//         "delay_restriction" => "",
//         "delay_restriction_frequency" => "day",
//         "page_view_restriction_enable" => "",
//         "page_view_restriction" => "",
//         "page_view_restriction_frequency" => "day",
//         "page_view_restriction_enable_time_frame" => "",
//         "page_view_restriction_time_frame" => "",
//         "expire_restriction_enable" => "",
//         "expire_restriction" => "",
//         "expire_restriction_frequency" => "day",
//         "show_warnings" => ""
//     );

//     // Add the custom meta to the product
//     add_post_meta($post_id, '_ppp_document_settings_meta', json_encode($ppp_document_settings_meta));
// }