<?php
/**
 * Manages file-related operations such as generating and downloading XML files.
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

/**
 * SMNTCS File Manager class
 *
 * @since 1.0.0
 */
class SMNTCS_File_Manager {

	/**
	 * Constructor to set up action hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'download_xml_file' ] );
	}

	/**
	 * Generate XML file
	 *
	 * @param array $coupon_codes Coupon codes.
	 * @return string
	 * @since 1.0.0
	 */
	public static function generate_xml( $coupon_codes ) {
		global $wp_filesystem;

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		$xml_content  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_content .= '<coupons>';
		foreach ( $coupon_codes as $code ) {
			$xml_content .= "<coupon><code>{$code}</code></coupon>";
		}
		$xml_content .= '</coupons>';

		$file_name = 'coupon-codes-' . time() . '.xml';
		$file_path = wp_upload_dir()['path'] . '/' . $file_name;

		update_option( 'smntcs_coupon_codes_xml_file_path', $file_path );
		$wp_filesystem->put_contents( $file_path, $xml_content );

		return wp_upload_dir()['url'] . '/' . $file_name;
	}

	/**
	 * Download XML file
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function download_xml_file() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['coupon_code_generator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coupon_code_generator_nonce'] ) ), 'coupon_code_generator_action' ) ) {
			return;
		}

		if ( isset( $_GET['coupon_codes'] ) && 'xml' === $_GET['coupon_codes'] ) {
			$file_path = get_option( 'smntcs_coupon_codes_xml_file_path' );

			if ( file_exists( $file_path ) ) {
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/xml' );
				header( 'Content-Disposition: attachment; filename=' . basename( $file_path ) );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $file_path ) );
				flush();
				readfile( $file_path );
				exit;
			}
		}
	}
}

new SMNTCS_File_Manager();
