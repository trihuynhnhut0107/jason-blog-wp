<?php

namespace PRAMADILLO\INTEGRATIONS;

use Woocommerce_Pay_Per_Post_Helper;

class WooCommerceMemberships {

	public function get_membership_levels() {
		/** @noinspection PhpUndefinedFunctionInspection */
		return wc_memberships_get_membership_plans();

	}

	/** @noinspection PhpUnused */
	public function generate_membership_levels_dropdown( $selected = [] ): string {
		$selected  = empty( $selected ) ? [] : $selected;
		$drop_down = '';
		/** @noinspection PhpUndefinedFunctionInspection */
		$membership_levels = wc_memberships_get_membership_plans();
		$drop_down         .= '<optgroup label="Membership Levels">';

		foreach ( $membership_levels as $level ) {

			$drop_down .= '<option value="' . $level->id . '"';

			if ( in_array( (string) $level->id, $selected, true ) ) {
				$drop_down .= ' selected="selected"';
			}

			$drop_down .= '>' . $level->name . ' - [#' . $level->id . ']</option>';
		}
		$drop_down .= '</optgroup>';

		return $drop_down;

	}

	/**
	 * @param null $post_id
	 *
	 * @return bool
	 */
	public function is_member( $post_id = null ): bool {
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}
		$product_ids  = (array) get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );
		$current_user = wp_get_current_user();

		foreach ( $product_ids as $id ) {
			Woocommerce_Pay_Per_Post_Helper::logger( 'Looking to see if user is an active member of member level - ' . trim( $id ) );

			/** @noinspection PhpUndefinedFunctionInspection */
			$membership = wc_memberships_is_user_active_member( $current_user->ID, $id, false );

			if ( $membership ) {
				Woocommerce_Pay_Per_Post_Helper::logger( 'IS A MEMBER OF - ' . trim( $id ) );
				add_filter( 'wc_pay_per_post_enable_javascript_expiration_refresh', function () {
					return false;
				} );

				return true;
			}
			Woocommerce_Pay_Per_Post_Helper::logger( 'IS NOT A MEMBER OF - ' . trim( $id ) );

		}

		return false;
	}


	public function post_contains_membership_products( $post_id ): bool {
		$product_ids = (array) get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );

		foreach ( $product_ids as $product_id ) {
			if ( ( $product_id ) ) {
				if ( array_key_exists( (int) $product_id, $this->get_membership_levels() ) ) {
					return true;
				}
			}
		}

		return false;

	}

}