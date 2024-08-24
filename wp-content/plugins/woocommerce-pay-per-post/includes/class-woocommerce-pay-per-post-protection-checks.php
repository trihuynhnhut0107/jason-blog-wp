<?php

/** @noinspection PhpUnused */
use PRAMADILLO\INTEGRATIONS\PaidMembershipsPro;
use PRAMADILLO\INTEGRATIONS\WooCommerceMemberships;
use PRAMADILLO\INTEGRATIONS\WooCommerceSubscriptions;
class Woocommerce_Pay_Per_Post_Protection_Checks extends Woocommerce_Pay_Per_Post {
    public static function check_if_admin_call() : bool {
        if ( is_admin() ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_call  - IS an Admin Call' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_call  - IS NOT an Admin Call' );
        return false;
    }

    public static function check_if_admin_user_have_access() : bool {
        $admins_allowed_access = (bool) get_option( WC_PPP_SLUG . '_allow_admins_access_to_protected_posts', false );
        // Check and see if admins are allowed to view protected content.
        if ( $admins_allowed_access && is_super_admin() ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_user_have_access  - Administrators HAVE access to all protected posts via settings' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_user_have_access  - Administrators DO NOT HAVE access to all protected posts via settings' );
        return false;
    }

    public static function check_if_user_role_has_access() : bool {
        $allowed_user_roles = [];
        foreach ( wp_get_current_user()->roles as $role ) {
            if ( in_array( $role, $allowed_user_roles ) ) {
                return true;
            }
        }
        return false;
    }

    public static function check_if_purchased( $id ) : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - Checking to see if user has purchased product #' . $id );
        $current_user = wp_get_current_user();
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() ) {
            $subscriptions = new WooCommerceSubscriptions();
            if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, trim( $id ) ) && !$subscriptions->is_subscription_product( $id ) ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - WooSubscriptions Enabled and User has purchased product id #' . trim( $id ) . ' that is NOT a subscription product' );
                return true;
            }
        } else {
            if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, trim( $id ) ) ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - User has purchased product id #' . trim( $id ) );
                return true;
            }
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - User has NOT purchased product id #' . trim( $id ) );
        return false;
    }

    public static function check_if_logged_in() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_logged_in  - ' . (( is_user_logged_in() ? 'true' : 'false' )) );
        return is_user_logged_in();
    }

    public static function check_if_has_access() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_has_access  - Has been called' );
        //
        //            switch ($this->protection_type) {
        //                case 'standard': // Since we already check to see if they purchased the product standard protection returns true all the time.
        //                case 'delay': // Delay protection is same protection as standard, just difference in when to display pay wall, we already checked to see if they purchased product we return true.
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Standard or Delayed');
        //
        //                    return $this->check_if_purchased();
        //                case 'page-view':
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Page View Protection');
        //
        //                    return $this->has_access_page_view_protection__premium_only();
        //                case 'expire':
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Expiration Protection');
        //
        //                    return $this->has_access_expiry_protection__premium_only();
        //            }
        return true;
    }

    public static function check_if_is_paid_memberships_pro_member( $id ) : bool {
        //Is user a Paid Memberships Pro Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_paid_membership_pro() ) {
            $pmp = new PaidMembershipsPro();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_paid_memberships_pro_member  - Is the user a Paid Memberships Pro Member? - ' . (( $pmp->is_member( $id ) ? 'true' : 'false' )) );
            return $pmp->is_member( $id );
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_paid_memberships_pro_member  - User is NOT a Paid Memberships Pro member, as Paid Memberships Pro is not installed.' );
        return false;
    }

    public static function check_if_is_member( $id ) : bool {
        //Is user a WooCommerce Memberships Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_memberships() ) {
            $memberships = new WooCommerceMemberships();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_member  - Is the user a WooMemberships Member? - ' . (( $memberships->is_member( $id ) ? 'true' : 'false' )) );
            return $memberships->is_member( $id );
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_member  - User is NOT a member, as WooMemberships is not installed.' );
        return false;
    }

    public static function check_if_is_subscriber( $id ) : bool {
        //Is user a WooCommerce Subscriptions Subscriber?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() ) {
            $subscriptions = new WooCommerceSubscriptions();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_subscriber  - Does the user have a valid subscription? - ' . (( $subscriptions->is_subscriber( $id ) ? 'true' : 'false' )) );
            return $subscriptions->is_subscriber( $id );
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_is_subscriber  - User is NOT a subscriber, as WooSubscriptions is not installed.' );
        return false;
    }

    public static function check_if_post_contains_subscription_products( $id ) : bool {
        $subscriptions = new WooCommerceSubscriptions();
        $subscriptions->post_contains_subscription_products( $id );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_post_contains_subscription_products  - Does Post/Page Contain Subscription Products? - ' . (( $subscriptions->post_contains_subscription_products( $id ) ? 'true' : 'false' )) );
        return $subscriptions->post_contains_subscription_products( $id );
    }

    public static function check_if_post_contains_membership_products( $id ) : bool {
        $memberships = new WooCommerceMemberships();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_post_contains_membership_products  - Does Post Contain Membership Products? - ' . (( $memberships->post_contains_membership_products( $id ) ? 'true' : 'false' )) );
        return $memberships->post_contains_membership_products( $id );
    }

    public static function check_if_post_contains_paid_memberships_pro_membership_products( $id ) : bool {
        $pmp = new PaidMembershipsPro();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_post_contains_paid_memberships_pro_membership_products  - Does Post Contain Paid Membership Pro Membership Products? - ' . (( $pmp->post_contains_membership_products( $id ) ? 'true' : 'false' )) );
        return $pmp->post_contains_membership_products( $id );
    }

}
