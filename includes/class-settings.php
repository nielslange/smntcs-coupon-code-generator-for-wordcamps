<?php
/**
 * Handles the settings and configuration options for the plugin.
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

/**
 * SMNTCS Settings class
 *
 * @since 1.0.0
 */
class SMNTCS_Settings {

	/**
	 * Constructor to set up action hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'plugin_action_links_' . plugin_basename( SMNTCS_COUPON_CODE_PLUGIN_FILE ), [ $this, 'add_plugin_settings_link' ] );
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'form_settings' ] );
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
		$settings_link = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-nord-admin-theme' ) );
		array_unshift( $url, $settings_link );

		return $url;
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

				$xml_file_url = SMNTCS_File_Manager::generate_xml( $coupon_codes );
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
}

new SMNTCS_Settings();
