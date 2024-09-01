<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    WooCommerce_Product_Registration
 * @subpackage WooCommerce_Product_Registration/includes
 */

class WC_Product_Registration_Activator {

    /**
     * Create necessary database tables on plugin activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        self::create_tables();
        self::add_custom_capabilities();
        self::set_activation_transient();
    }

    /**
     * Create necessary database tables
     *
     * @since    1.0.0
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'wc_product_registrations';

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            serial_number varchar(255) NOT NULL,
            registration_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            purchase_proof varchar(255) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY serial_number (serial_number)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        // Debug: Log table creation result
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        error_log('WC Product Registration - Table creation result: ' . ($table_exists ? 'Success' : 'Failed'));
    }

    /**
     * Add custom capability for managing product registrations
     *
     * @since    1.0.0
     */
    private static function add_custom_capabilities() {
        $role = get_role( 'administrator' );
        if ( $role ) {
            $role->add_cap( 'manage_product_registrations' );
            error_log('WC Product Registration - Custom capability added to administrator role');
        } else {
            error_log('WC Product Registration - Failed to add custom capability: administrator role not found');
        }
    }

    /**
     * Set a transient to trigger a one-time welcome notice
     *
     * @since    1.0.0
     */
    private static function set_activation_transient() {
        set_transient( 'wc_product_registration_activated', true, 5 );
        error_log('WC Product Registration - Activation transient set');
    }
}