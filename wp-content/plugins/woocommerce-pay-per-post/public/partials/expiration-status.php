<?php /** @noinspection PhpUndefinedVariableInspection */
/**
 * Do not edit this file directly.  You can copy this file to your theme directory
 * in woocommerce-pay-per-post/expiration-status.php
 *
 * $user_info = Array
 * (
 * [last_purchase_date] => Carbon\Carbon Object
 * (
 * [date] => 2018-08-14 14:24:21.000000
 * [timezone_type] => 3
 * [timezone] => UTC
 * )
 *
 * [expiration_date] => Carbon\Carbon Object
 * (
 * [date] => 2023-11-14 14:24:21.000000
 * [timezone_type] => 3
 * [timezone] => UTC
 * )
 *
 * [time_remaining] => Array
 * (
 * [human] => 5 years from now
 * [difference] => 62
 * [frequency] => month
 * )
 *
 * )
 */
?>
<div class="wc-ppp-expiration-status-container">
    <h4><?php esc_attr_e( 'Order Information', 'wc_pay_per_post' ); ?></h4>

    <ul>
        <li><strong><?php esc_attr_e( 'Order Date', 'wc_pay_per_post' ); ?></strong>: <?php echo $user_info['last_purchase_date']->format( Woocommerce_Pay_Per_Post_Helper::date_time_format() ); ?></li>
        <li><strong><?php esc_attr_e( 'Expiration Date', 'wc_pay_per_post' ); ?></strong>: <?php echo $user_info['expiration_date']->format( Woocommerce_Pay_Per_Post_Helper::date_time_format() ); ?></li>
        <li><strong><?php esc_attr_e( 'Time Remaining', 'wc_pay_per_post' ); ?></strong>: <?php echo $user_info['time_remaining']['human']; ?></li>
    </ul>
</div>