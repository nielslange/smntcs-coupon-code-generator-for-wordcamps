<?php
/**
 * Plugin Name:         SMNTCS Coupon Code Generator for WordCamps
 * Description:         This plugin generates coupon codes for WordCamp tickets.
 * Requires at least:   6.1
 * Requires PHP:        7.4
 * Version:             1.0
 * Author:              Niels Lange
 * Author URI:          https://nielslange.de
 * License:             GPL-2.0-or-later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         smntcs-coupon-code-generator-for-wordcamps
 *
 * @package SMNTCS_Coupon_Code_Generator
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin file.
require_once ABSPATH . 'wp-admin/includes/plugin.php';
define( 'SMNTCS_COUPON_CODE_PLUGIN_DIR', __DIR__ );
define( 'SMNTCS_COUPON_CODE_PLUGIN_FILE', __FILE__ );
define( 'SMNTCS_COUPON_CODE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMNTCS_COUPON_CODE_PLUGIN_VERSION', '1.0.0' );

// Load plugin classes.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-scripts.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-file-manager.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-utils.php';
