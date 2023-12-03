<?php
/**
 * Utility functions for SMNTCS Coupon Code Generator.
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

class SMNTCS_Utility {

	/**
	 * Verifies a nonce for a given action.
	 *
	 * @param string $nonce  The nonce to verify.
	 * @param string $action The action associated with the nonce.
	 * @return bool True if nonce is valid, false otherwise.
	 */
	public static function verify_nonce( $post ) {
		if ( isset( $post['coupon_code_generator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $post['coupon_code_generator_nonce'] ) ), 'coupon_code_generator_action' ) ) {
			return;
		}
	}

}
