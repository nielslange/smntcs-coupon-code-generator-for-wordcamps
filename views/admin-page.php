<?php
/**
 * Admin page template
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="">
	<?php
		settings_errors( 'generate_coupon_codes_section' );
		settings_fields( 'generate_coupon_codes_section' );
		do_settings_sections( 'generate_coupon_codes_section' );
	?>
		<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Submit', 'textdomain' ); ?>">
	</form>

	<?php if ( get_transient( 'smntcs_coupon_codes' ) || get_transient( 'smntcs_coupon_codes_xml_url' ) ) : ?>
		<div class="coupon-codes-download">
			<h2><?php echo esc_html( __( 'Download coupon codes', 'textdomain' ) ); ?></h2>

			<?php if ( get_transient( 'smntcs_coupon_codes' ) ) : ?>
				<textarea id="coupon_codes" style="display:none;"><?php echo esc_textarea( implode( "\n", get_transient( 'smntcs_coupon_codes' ) ) ); ?></textarea>
				<button class="button button-secondary" onclick="copyToClipboard()">Copy Coupon Codes</button>
			<?php endif; ?>

			<?php if ( get_transient( 'smntcs_coupon_codes_xml_url' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?coupon_codes=xml' ) ); ?>" class="button button-secondary">Download XML File</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
