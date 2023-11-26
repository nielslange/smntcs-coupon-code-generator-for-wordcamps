<?php
/**
 * Admin page template
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Retrieve any data passed to this template.
$errors       = isset( $errors ) ? $errors : [];
$coupon_codes = isset( $coupon_codes ) ? $coupon_codes : [];
$xml_file_url = isset( $xml_file_url ) ? $xml_file_url : '';

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! empty( $errors ) ) : ?>
		<div class="notice notice-error">
			<ul>
				<?php foreach ( $errors as $error ) : ?>
					<li><?php echo esc_html( $error ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'coupon_code_generator_action', 'coupon_code_generator_nonce' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="prefix">Prefix:</label></th>
					<td><input type="text" name="prefix" id="prefix" value="<?php echo esc_attr( $_POST['prefix'] ?? '' ); ?>" class="medium-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="discount">Discount in percentage:</label></th>
					<td><input type="number" name="discount" id="discount" value="<?php echo esc_attr( $_POST['discount'] ?? '100' ); ?>" class="small-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="number_of_coupons">Number of coupon codes:</label></th>
					<td><input type="number" name="number_of_coupons" id="number_of_coupons" value="<?php echo esc_attr( $_POST['number_of_coupons'] ?? '100' ); ?>" class="small-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="quantity_of_coupon_code">Quantity of coupon code:</label></th>
					<td><input type="number" name="quantity_of_coupon_code" id="quantity_of_coupon_code" value="<?php echo esc_attr( $_POST['quantity_of_coupon_code'] ?? '1' ); ?>" class="small-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="valid_from">Coupon valid from:</label></th>
					<td><input type="text" name="valid_from" id="valid_from" value="<?php echo esc_attr( $_POST['valid_from'] ?? '' ); ?>" class="medium-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="valid_to">Coupon valid to:</label></th>
					<td><input type="text" name="valid_to" id="valid_to" value="<?php echo esc_attr( $_POST['valid_to'] ?? '' ); ?>" class="medium-text"></td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td>
						<input type="submit" class="button button-primary" value="Submit">

						<?php if ( get_transient( 'smntcs_coupon_codes' ) ) : ?>
							<textarea id="coupon_codes" style="display:none;"><?php echo implode( "\n", get_transient( 'smntcs_coupon_codes' ) ); ?></textarea>
							<button class="button button-secondary" onclick="copyToClipboard()">Copy Coupon Codes</button>
						<?php endif; ?>

						<?php if ( get_transient( 'smntcs_coupon_codes_xml_url' ) ) : ?>
							<a href="<?php echo admin_url( 'admin.php?smntcs_download=xml' ); ?>" class="button button-secondary">Download XML File</a>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
	function copyToClipboard() {
		var copyText = document.getElementById("coupon_codes");
		copyText.style.display = "block";
		copyText.select();
		document.execCommand("copy");
		copyText.style.display = "none";
		alert("Copied to clipboard");
	}
</script>
