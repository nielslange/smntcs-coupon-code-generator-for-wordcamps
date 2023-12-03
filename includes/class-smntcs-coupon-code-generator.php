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
		add_action( 'admin_init', [ $this, 'form_settings' ] );
		add_action( 'admin_init', [ $this, 'download_xml_file' ] );

		add_action( 'plugin_action_links_' . plugin_basename( SMNTCS_COUPON_CODE_PLUGIN_FILE ), [ $this, 'add_plugin_settings_link' ] );
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @param array $url The original URL.
	 * @return array The updated URL.
	 * @since 1.0.0
	 */
	public function add_plugin_settings_link( $url ) {
		$admin_url     = admin_url( 'tools.php?page=coupon-code-generator' );
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			$admin_url,
			__( 'Settings', 'smntcs-nord-admin-theme' )
		);
		array_unshift( $url, $settings_link );

		return $url;
	}


	/**
	 * Enqueue scripts and styles
	 *
	 * @param string $hook The current admin page.
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook ) {
		// Only enqueue scripts on the plugin page.
		if ( 'tools_page_coupon-code-generator' !== $hook ) {
			return;
		}

		// Enqueue datepicker scripts and styles.
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script(
			'coupon-generator-datepicker-script',
			SMNTCS_COUPON_CODE_PLUGIN_URL . 'js/datepicker.js',
			[ 'jquery', 'jquery-ui-datepicker' ],
			SMNTCS_COUPON_CODE_PLUGIN_VERSION,
			[ 'in_footer' => true ]
		);
		wp_enqueue_style(
			'coupon-generator-datepicker-styles',
			SMNTCS_COUPON_CODE_PLUGIN_URL . '/js/jquery-ui.css',
			[],
			SMNTCS_COUPON_CODE_PLUGIN_VERSION
		);

		// Enqueue copy to clipboard script.
		wp_enqueue_script(
			'coupon-generator-copy-script',
			SMNTCS_COUPON_CODE_PLUGIN_URL . 'js/copyToClipboard.js',
			[],
			SMNTCS_COUPON_CODE_PLUGIN_VERSION,
			[ 'in_footer' => true ]
		);
	}

	/**
	 * Register the plugin page
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
	 * Register the form settings
	 *
	 * @since 1.0.0
	 */
	public function form_settings() {
		add_settings_section(
			'generate_coupon_codes_section',
			__( 'Generate coupon codes', 'textdomain' ),
			[ $this, 'smntcs_coupon_code_generator_section_callback' ],
			'generate_coupon_codes_section'
		);

		add_settings_field(
			'prefix',
			__( 'Prefix:', 'textdomain' ),
			[ $this, 'text_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'label_for' => 'prefix',
				'class'     => 'smntcs-row',
			]
		);

		add_settings_field(
			'discount',
			__( 'Discount in percentage:', 'textdomain' ),
			[ $this, 'number_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'type'      => 'checkbox',
				'label_for' => 'discount',
				'class'     => 'smntcs-row',

			]
		);

		add_settings_field(
			'number_of_coupons',
			__( 'Number of coupon codes:', 'textdomain' ),
			[ $this, 'number_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'label_for' => 'number_of_coupons',
				'class'     => 'smntcs-row',
			]
		);

		add_settings_field(
			'quantity_of_coupon_code',
			__( 'Quantity of coupon code:', 'textdomain' ),
			[ $this, 'number_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'label_for' => 'quantity_of_coupon_code',
				'class'     => 'smntcs-row',
			]
		);

		add_settings_field(
			'valid_from',
			__( 'Coupon valid from:', 'textdomain' ),
			[ $this, 'text_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'label_for' => 'valid_from',
				'class'     => 'smntcs-row',
			]
		);

		add_settings_field(
			'valid_to',
			__( 'Coupon valid to:', 'textdomain' ),
			[ $this, 'text_field_callback' ],
			'generate_coupon_codes_section',
			'generate_coupon_codes_section',
			[
				'label_for' => 'valid_to',
				'class'     => 'smntcs-row',
			]
		);
	}

	/**
	 * Display the text field
	 *
	 * @param array $args The field arguments.
	 * @return void
	 * @since 1.0.0
	 */
	public function text_field_callback( $args ) {
		if ( isset( $_POST['coupon_code_generator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coupon_code_generator_nonce'] ) ), 'coupon_code_generator_action' ) ) {
			return;
		}

		$value = isset( $_POST[ $args['label_for'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $args['label_for'] ] ) ) : '';
		echo '<input type="text" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $value ) . '">';
	}

	/**
	 * Display the number field
	 *
	 * @param array $args The field arguments.
	 * @return void
	 * @since 1.0.0
	 */
	public function number_field_callback( $args ) {
		if ( isset( $_POST['coupon_code_generator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coupon_code_generator_nonce'] ) ), 'coupon_code_generator_action' ) ) {
			return;
		}

		$value = isset( $_POST[ $args['label_for'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $args['label_for'] ] ) ) : '';
		echo '<input type="number" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $value ) . '">';
	}

	/**
	 * Display the plugin page
	 *
	 * @since 1.0.0
	 */
	public function display_plugin_page() {
		$coupon_codes = get_transient( 'smntcs_coupon_codes' );
		$xml_file_url = get_transient( 'smntcs_coupon_codes_xml_url' );

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'coupon_code_generator_action', 'coupon_code_generator_nonce' );

			$validation_errors = $this->validate_form_data();

			if ( empty( $validation_errors ) && isset( $_POST['number_of_coupons'] ) && isset( $_POST['prefix'] ) ) {
				$coupon_codes = $this->generate_coupon_codes( intval( wp_unslash( $_POST['number_of_coupons'] ) ), sanitize_text_field( wp_unslash( $_POST['prefix'] ) ) );
				set_transient( 'smntcs_coupon_codes', $coupon_codes, 10 * MINUTE_IN_SECONDS );

				$xml_file_url = $this->generate_xml( $coupon_codes );
				set_transient( 'smntcs_coupon_codes_xml_url', $xml_file_url, 10 * MINUTE_IN_SECONDS );
			}
		}

		require_once plugin_dir_path( SMNTCS_COUPON_CODE_PLUGIN_FILE ) . 'views/admin-page.php';
	}

	/**
	 * Validate form data
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function validate_form_data() {
		if ( isset( $_POST['coupon_code_generator_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coupon_code_generator_nonce'] ) ), 'coupon_code_generator_action' ) ) {
			return;
		}

		$error           = false;
		$required_fields = [
			'discount'                => 'Discount',
			'number_of_coupons'       => 'Number of coupons',
			'quantity_of_coupon_code' => 'Quantity of coupon code',
		];

		foreach ( $required_fields as $key => $value ) {
			if ( empty( $_POST[ $key ] ) ) {
				$error = true;
				add_settings_error(
					'generate_coupon_codes_section',
					"{$key}_error",
					"Field {$value} is required.",
					'error'
				);
			}
		}

		return $error;
	}

	/**
	 * Generate coupon codes
	 *
	 * @param int    $number Number of coupon codes to generate.
	 * @param string $prefix Prefix for coupon codes.
	 * @return array
	 * @since 1.0.0
	 */
	private function generate_coupon_codes( $number, $prefix ) {
		$codes = [];
		for ( $i = 0; $i < $number; $i++ ) {
			$code    = $prefix ? "{$prefix}_" . wp_generate_password( 8, false ) : wp_generate_password( 8, false );
			$codes[] = $code;
		}
		return $codes;
	}

	/**
	 * Generate XML file
	 *
	 * @param array $coupon_codes Coupon codes.
	 * @return string
	 * @since 1.0.0
	 */
	private function generate_xml( $coupon_codes ) {
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
	 * Display the section
	 *
	 * @param array $args The field arguments.
	 * @return void
	 * @since 1.0.0
	 */
	public function smntcs_coupon_code_generator_section_callback( $args ) {
		wp_nonce_field( 'coupon_code_generator_action', 'coupon_code_generator_nonce' );
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

new SMNTCS_Coupon_Code_Generator();
