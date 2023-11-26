<?php
/**
 * SMNTCS Coupon Code Generator for WordCamps
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

/**
 * SMNTCS Coupon Code Generator for WordCamps class
 *
 * @since 1.0.0
 */
class SMNTCS_Coupon_Code_Generator {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_init', [ $this, 'download_xml_file' ] );

	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts($hook) {
		if ('tools_page_coupon-code-generator' !== $hook) {
			return;
		}

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('smntcs-simple-events-script', SMNTCS_COUPON_CODE_PLUGIN_URL . 'js/custom.js', [ 'jquery', 'jquery-ui-datepicker'], SMNTCS_COUPON_CODE_PLUGIN_VERSION);
		wp_enqueue_style( 'smntcs-simple-events-styles', SMNTCS_COUPON_CODE_PLUGIN_URL . '/js/jquery-ui.css', [], SMNTCS_COUPON_CODE_PLUGIN_VERSION );
	}

	/**
	 * Display the plugin page
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'tools.php',
			'Coupon Code Generator',
			'Coupon Codes',
			'manage_options',
			'coupon-code-generator',
			[ $this, 'display_plugin_page' ]
		);
	}

	/**
	 * Display the plugin page
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_page() {
		$coupon_codes = get_transient( 'smntcs_coupon_codes' );
		$xml_file_url = get_transient( 'smntcs_coupon_codes_xml_url' );

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			check_admin_referer( 'coupon_code_generator_action', 'coupon_code_generator_nonce' );

			$errors = $this->validate_form_data();

			if ( empty( $errors ) ) {
				$coupon_codes = $this->generate_coupon_codes( intval( $_POST['number_of_coupons'] ), sanitize_text_field( $_POST['prefix'] ) );
				set_transient( 'smntcs_coupon_codes', $coupon_codes, 12 * HOUR_IN_SECONDS );

				$xml_file_url = $this->generate_xml( $coupon_codes );
				set_transient( 'smntcs_coupon_codes_xml_url', $xml_file_url, 12 * HOUR_IN_SECONDS );
			}
		}

		require_once plugin_dir_path( SMNTCS_COUPON_CODE_PLUGIN_FILE ) . 'views/admin-page.php';
	}

	private function validate_form_data() {
		$errors          = [];
		$required_fields = [ 'discount', 'number_of_coupons', 'quantity_of_coupon_code' ];

		foreach ( $required_fields as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				$errors[] = "Field '$field' is required.";
			}
		}

		return $errors;
	}


	// Method to generate coupon codes
	private function generate_coupon_codes( $number, $prefix ) {
		$codes = [];
		for ( $i = 0; $i < $number; $i++ ) {
			$code    = $prefix ? "{$prefix}_" . wp_generate_password( 8, false ) : wp_generate_password( 8, false );
			$codes[] = $code;
		}
		return $codes;
	}

	// Method to generate XML from coupon codes
	private function generate_xml( $coupon_codes ) {
		// XML generation logic
		$xml_content  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_content .= '<coupons>';
		foreach ( $coupon_codes as $code ) {
			$xml_content .= "<coupon><code>{$code}</code></coupon>";
		}
		$xml_content .= '</coupons>';

		$file_name = 'coupon-codes-' . time() . '.xml';
		$file_path = wp_upload_dir()['path'] . '/' . $file_name;

		update_option( 'smntcs_coupon_codes_xml_file_path', $file_path );
		file_put_contents( $file_path, $xml_content );
		return wp_upload_dir()['url'] . '/' . $file_name;
	}

	public function download_xml_file() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['smntcs_download'] ) && $_GET['smntcs_download'] === 'xml' ) {
			$file_path = get_option( 'smntcs_coupon_codes_xml_file_path' );

			if ( file_exists( $file_path ) ) {
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/xml' );
				header( 'Content-Disposition: attachment; filename=' . basename( $file_path ) );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $file_path ) );
				flush(); // Flush system output buffer
				readfile( $file_path );
				exit;
			}
		}
	}


}

new SMNTCS_Coupon_Code_Generator();
