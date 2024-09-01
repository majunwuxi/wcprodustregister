<?php
/**
 * Plugin Name: WooCommerce Product Registration
 * Plugin URI: http://example.com/wc-product-registration
 * Description: Allow customers to register their products using serial numbers.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: http://example.com
 * Text Domain: wc-product-registration
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 *
 * @package WooCommerce_Product_Registration
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'WC_PRODUCT_REGISTRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_wc_product_registration() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-registration-activator.php';
    WC_Product_Registration_Activator::activate();

    // Create the product registrations table
    global $wpdb;
    $table_name = $wpdb->prefix . 'product_registrations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        serial_number varchar(255) NOT NULL,
        product_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        registration_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wc_product_registration() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-registration-deactivator.php';
    WC_Product_Registration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_product_registration' );
register_deactivation_hook( __FILE__, 'deactivate_wc_product_registration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-registration.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wc_product_registration() {
    $plugin = new WC_Product_Registration();
    $plugin->run();
}

run_wc_product_registration();