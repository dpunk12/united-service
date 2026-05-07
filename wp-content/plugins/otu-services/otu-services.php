<?php
/**
 * Plugin Name: OTU Services
 * Plugin URI: https://onetenunited.com
 * Description: Complete services, booking, LMS integration, certificates and dashboard for One Ten United Services.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: One Ten United Services
 * Author URI: https://onetenunited.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: otu
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OTU_VERSION', '1.0.1' );
define( 'OTU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OTU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Require all includes files.
require_once OTU_PLUGIN_DIR . 'includes/class-otu-setup.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-cpt.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-forms.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-woocommerce.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-booking.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-lms.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-certificate.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-dashboard.php';
require_once OTU_PLUGIN_DIR . 'includes/class-otu-emails.php';
require_once OTU_PLUGIN_DIR . 'admin/class-otu-admin.php';

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, array( 'OTU_Setup', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'OTU_Setup', 'deactivate' ) );

/**
 * Initialise all plugin classes.
 */
function otu_init_plugin() {
	new OTU_CPT();
	new OTU_Forms();
	new OTU_WooCommerce();
	new OTU_Booking();
	new OTU_LMS();
	new OTU_Certificate();
	new OTU_Dashboard();
	new OTU_Emails();
	new OTU_Admin();
}
add_action( 'plugins_loaded', 'otu_init_plugin' );

/**
 * Flush rewrite rules once when the plugin version changes, so updated CPT
 * rewrite slugs take effect without requiring the admin to manually visit
 * Settings → Permalinks or to deactivate/reactivate the plugin.
 */
function otu_maybe_flush_rewrites() {
	if ( get_option( 'otu_plugin_version' ) !== OTU_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'otu_plugin_version', OTU_VERSION );
	}
}
add_action( 'init', 'otu_maybe_flush_rewrites', 99 );

/**
 * Load plugin text domain.
 */
function otu_load_textdomain() {
	load_plugin_textdomain( 'otu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'otu_load_textdomain' );

/**
 * Enqueue frontend assets.
 */
function otu_enqueue_frontend_assets() {
	wp_enqueue_style(
		'otu-frontend',
		OTU_PLUGIN_URL . 'assets/css/otu-frontend.css',
		array(),
		OTU_VERSION
	);

	wp_enqueue_script(
		'otu-frontend',
		OTU_PLUGIN_URL . 'assets/js/otu-frontend.js',
		array( 'jquery' ),
		OTU_VERSION,
		true
	);

	wp_localize_script(
		'otu-frontend',
		'otu_params',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'otu_frontend_nonce' ),
			'home_url' => esc_url( home_url( '/' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'otu_enqueue_frontend_assets' );

/**
 * Enqueue admin assets.
 */
function otu_enqueue_admin_assets( $hook ) {
	wp_enqueue_style(
		'otu-admin',
		OTU_PLUGIN_URL . 'admin/assets/css/otu-admin.css',
		array(),
		OTU_VERSION
	);

	wp_enqueue_script(
		'otu-admin',
		OTU_PLUGIN_URL . 'admin/assets/js/otu-admin.js',
		array( 'jquery', 'jquery-ui-datepicker' ),
		OTU_VERSION,
		true
	);

	wp_localize_script(
		'otu-admin',
		'otu_admin_params',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'otu_admin_nonce' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'otu_enqueue_admin_assets' );
