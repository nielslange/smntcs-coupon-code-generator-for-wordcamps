<?php
/**
 * Handles the enqueueing of admin scripts and styles.
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

/**
 * SMNTCS Admin Scripts Class
 *
 * @since 1.0.0
 */
class SMNTCS_Admin_Scripts {

	/**
	 * Constructor to set up action hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Enqueues scripts and styles for the admin page.
	 *
	 * @param string $hook The current admin page.
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'tools_page_coupon-code-generator' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'coupon-generator-datepicker-script', SMNTCS_COUPON_CODE_PLUGIN_URL . 'js/datepicker.js', [ 'jquery', 'jquery-ui-datepicker' ], SMNTCS_COUPON_CODE_PLUGIN_VERSION, [ 'in_footer' => true ] );
		wp_enqueue_script( 'coupon-generator-copy-script', SMNTCS_COUPON_CODE_PLUGIN_URL . 'js/copyToClipboard.js', [], SMNTCS_COUPON_CODE_PLUGIN_VERSION, [ 'in_footer' => true ] );

		wp_enqueue_style( 'coupon-generator-datepicker-styles', SMNTCS_COUPON_CODE_PLUGIN_URL . '/js/jquery-ui.css', [], SMNTCS_COUPON_CODE_PLUGIN_VERSION );
	}
}

new SMNTCS_Admin_Scripts();
