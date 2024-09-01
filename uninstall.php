<?php
// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete the database table
global $wpdb;
$table_name = $wpdb->prefix . 'wc_product_registrations';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Delete plugin options
delete_option( 'wc_product_registration_options' );

// Remove custom capabilities
$role = get_role( 'administrator' );
if ( $role ) {
    $role->remove_cap( 'manage_product_registrations' );
}

// Clear any cached data
wp_cache_flush();