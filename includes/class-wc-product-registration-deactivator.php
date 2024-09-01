<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    WooCommerce_Product_Registration
 * @subpackage WooCommerce_Product_Registration/includes
 */

class WC_Product_Registration_Deactivator {

    /**
     * Perform cleanup tasks on plugin deactivation.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Remove custom capability
        $role = get_role( 'administrator' );
        if ( $role ) {
            $role->remove_cap( 'manage_product_registrations' );
        }

        // Optional: Remove the plugin's database table
        // Uncomment the following lines if you want to remove the table on deactivation
        /*
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_product_registrations';
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
        */

        // Clear any scheduled hooks
        wp_clear_scheduled_hook( 'wc_product_registration_daily_event' );
    }
}