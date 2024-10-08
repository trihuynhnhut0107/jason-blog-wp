<?php

/** @noinspection PhpUndefinedVariableInspection */
?>
<div class="wrap  about-wrap full-width-layout">
    <h1><?php 
_e( 'Pay For Post with WooCommerce Settings' );
?></h1>
    <p class="about-text"><?php 
_e( 'If you have any questions or want to suggest a feature request please reach out to me at <a href="https://pramadillo.com" target="_blank">pramadillo.com</a>.  If you really dig this plugin consider leaving me a review!', 'wc_pay_per_post' );
?></p>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php 
echo plugin_dir_url( __DIR__ ) . 'img/icon.png';
?>"/></div>


    <div class="wc-ppp-settings-wrap">

        <div id="poststuff">

            <div id="post-body" class="metabox-holder columns-2">
                <!--suppress HtmlUnknownTarget -->
                <form action="options.php" method="post">
					<?php 
settings_fields( WC_PPP_SLUG . '_settings' );
?>
                    <div id="post-body-content">

                        <div class="postbox">
                            <h2 class="hndle"><?php 
esc_attr_e( 'Restricted Content Message', 'wc_pay_per_post' );
?></h2>

                            <div class="inside">
                                <p><?php 
esc_attr_e( 'Message that gets displayed when visitor goes to page that they have not purchased.  This message can be overwritten per product if necessary.', 'wc_pay_per_post' );
?></p>
                                <label for="<?php 
echo WC_PPP_SLUG;
?>_restricted_content_default">
									<?php 
esc_attr_e( 'Restricted Content Message', 'wc_pay_per_post' );
?>
                                    <br>
	                                <?php 
wp_editor( $restricted_content_default, WC_PPP_SLUG . '_restricted_content_default', $settings = array(
    'textarea_rows' => 6,
) );
?>
                                    <p class="description">
										<?php 
_e( 'The token <code>{{product_id}}</code> will be automatically replaced with the product ID associated with the page the visitor is on. You can also use the token <code>{{parent_id}}</code> for when using Product Variations so you show the parent product.   You can use any WooCommerce shortcodes in the restricted content text.  Please view <a href="https://docs.woocommerce.com/document/woocommerce-shortcodes/" target="_blank">https://docs.woocommerce.com/document/woocommerce-shortcodes/</a>.', 'wc_pay_per_post' );
?>

                                        <?php 
?>

                                    </p>
                                </label>
                            </div>
                        </div>

                        <div class="postbox">
                            <h2 class="hndle"><?php 
esc_attr_e( 'Display on additional Post Types', 'wc_pay_per_post' );
?></h2>

                            <div class="inside">
                                <p><?php 
_e( 'By default, the Pay For Post with WooCommerce Meta Box appears only on standard WordPress Pages and Posts.  If you would like the Meta Box to appear for other custom post types, please enter them below. <br><br><strong>Please note that any custom post type needs to support custom fields in order for the Block Editor to save the post data! <a href="https://developer.wordpress.org/reference/functions/add_post_type_support/" target="_blank">See More</a> </strong>', 'wc_pay_per_post' );
?></p>
                                <label for="<?php 
echo WC_PPP_SLUG;
?>_custom_post_types"><?php 
esc_attr_e( 'Additional Post Types', 'wc_pay_per_post' );
?></label>
                                <br>
                                <select id="<?php 
echo WC_PPP_SLUG;
?>_custom_post_types" name="<?php 
echo WC_PPP_SLUG;
?>_custom_post_types[]" style="width: 75%" multiple="multiple">
                                    <optgroup label="Additional Post Types">
										<?php 
foreach ( $custom_post_types as $post_type ) {
    ?>
											<?php 
    if ( !in_array( $post_type, $available_post_types ) ) {
        ?>
                                                <option value="<?php 
        echo $post_type;
        ?>" selected="selected"><?php 
        echo $post_type;
        ?></option>
											<?php 
    }
    ?>
										<?php 
}
?>
                                    </optgroup>

                                    <optgroup label="System Post Types">
										<?php 
foreach ( $available_post_types as $post_type ) {
    ?>
                                            <option value="<?php 
    echo $post_type;
    ?>" <?php 
    if ( in_array( $post_type, $custom_post_types ) ) {
        ?> selected="selected" <?php 
    }
    ?>><?php 
    echo $post_type;
    ?></option>
										<?php 
}
?>
                                    </optgroup>
                                </select>

                                <p class="description">
									<?php 
_e( '<a href="" class="post-types-button">Click here</a> to view the available post types for your system.', 'wc_pay_per_post' );
?>
                                </p>

                                <div class="code current-post-types" style="display:none;">
                                    <ul>
										<?php 
foreach ( get_post_types( array(
    'public' => true,
) ) as $post_type ) {
    echo '<li>' . $post_type . '</li>';
}
?>
                                    </ul>
                                </div>

                            </div>
                        </div>
	                    <?php 
?>



                        <div class="postbox">
                            <h2 class="hndle"><?php 
esc_attr_e( 'General Options', 'wc_pay_per_post' );
?></h2>

                            <div class="inside">
	                            <?php 
?>
                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_only_show_virtual_products" name="<?php 
echo WC_PPP_SLUG;
?>_only_show_virtual_products" <?php 
checked( $only_show_virtual_products );
?>> <?php 
esc_attr_e( 'Only show Virtual or Downloadable Products in Pay For Post with WooCommerce Meta Box?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
		                            <?php 
_e( 'Some folks have hundreds of products, this will limit the products displayed in the drop down list on the PPP meta box to just Virtual/Downloadable products.', 'wc_pay_per_post' );
?>
                                </p>
                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_turn_off_comments_when_protected" name="<?php 
echo WC_PPP_SLUG;
?>_turn_off_comments_when_protected" <?php 
checked( $turn_off_comments_when_protected );
?>> <?php 
esc_attr_e( 'Turn off comments completely on protected pages?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
									<?php 
_e( 'Turn off comments completely when user does not have access to page?', 'wc_pay_per_post' );
?>
                                </p>
                                <?php 
?>
                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_allow_admins_access_to_protected_posts" name="<?php 
echo WC_PPP_SLUG;
?>_allow_admins_access_to_protected_posts" <?php 
checked( $allow_admins_access_to_protected_posts );
?>> <?php 
esc_attr_e( 'Allow all administrator users to view all protected posts?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
									<?php 
_e( 'This is useful for debugging, you can enable or disable viewing of protected content when logged in as an administrator', 'wc_pay_per_post' );
?>
                                </p>

                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_block_editor_enabled" name="<?php 
echo WC_PPP_SLUG;
?>_block_editor_enabled" <?php 
checked( get_option( WC_PPP_SLUG . '_block_editor_enabled' ) );
?>> <?php 
esc_attr_e( 'Enable Block Editor Integration?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
		                            <?php 
_e( 'If you disable the standard meta box will display at the bottom of your edit screen rather than in the sidebar of the block editor.', 'wc_pay_per_post' );
?>
                                </p>

                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_enable_debugging" name="<?php 
echo WC_PPP_SLUG;
?>_enable_debugging" <?php 
checked( $enable_debugging );
?>> <?php 
esc_attr_e( 'Enable Debugging?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
									<?php 
_e( 'This will create a Pay For Post with WooCommerce log to let you see what is going on behind the scenes.  Can help to debug why a paywall is showing up or not showing up.  <strong>In order to debug you need to add ?wc-ppp-debug to your URL to log that particular page.</strong>', 'wc_pay_per_post' );
?>
									<?php 
if ( $enable_debugging || file_exists( Woocommerce_Pay_Per_Post_Helper::logger_uri() ) ) {
    ?>
                                        <br><br>
                                        <strong><?php 
    esc_attr_e( 'Log File Location', 'wc_pay_per_post' );
    ?></strong>: <a href="<?php 
    echo Woocommerce_Pay_Per_Post_Helper::logger_url();
    ?>" target="_blank"><?php 
    echo Woocommerce_Pay_Per_Post_Helper::logger_uri();
    ?></a> | <a href="<?php 
    echo wp_nonce_url( admin_url( 'admin.php?page=' . WC_PPP_SLUG . '-settings' ), 'delete_log', 'delete_log_nonce' );
    ?>" title="Delete Log"><span class="dashicons dashicons-dismiss error-message"></span></a>
									<?php 
}
?>
                                </p>
                                <br><hr><br>
                                <label>
                                    <input type="checkbox" value="1" class="<?php 
echo WC_PPP_SLUG;
?>_delete_settings" name="<?php 
echo WC_PPP_SLUG;
?>_delete_settings" <?php 
checked( $delete_settings );
?>> <?php 
esc_attr_e( 'Delete all settings and records this plugin made on deactivation?', 'wc_pay_per_post' );
?>
                                </label>
                                <p class="description">
									<?php 
_e( 'This will remove all customer page view stats, along with all settings, debug files, and database tables upon deactivation.  <br><strong>THIS CAN NOT BE UNDONE!</strong>', 'wc_pay_per_post' );
?>
                                </p>



                            </div>
                        </div>

						<?php 
submit_button( 'Save Settings' );
?>

                </form>

            </div>

			<?php 
require_once plugin_dir_path( __FILE__ ) . 'settings-sidebar.php';
?>
        </div>

        <br class="clear">
    </div>
</div>