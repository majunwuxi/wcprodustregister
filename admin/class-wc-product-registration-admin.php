<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WC_Product_Registration
 * @subpackage WC_Product_Registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WC_Product_Registration
 * @subpackage WC_Product_Registration/admin
 * @author     Your Name <email@example.com>
 */
class WC_Product_Registration_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . $plugin_name, array($this, 'add_action_links'));
        add_action('admin_post_wc_product_registration_import', array($this, 'handle_import'));
    }

    // ... (其他方法保持不变)

    /**
     * Add a single serial number
     *
     * @since    1.0.0
     * @param    string    $serial_number    The serial number to add.
     * @param    int       $product_id       The product ID associated with the serial number.
     * @return   bool                        True if added successfully, false otherwise.
     */
    private function add_serial_number($serial_number, $product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_registrations';

        $result = $wpdb->insert(
            $table_name,
            array(
                'serial_number' => $serial_number,
                'product_id' => $product_id,
                'user_id' => 0,
                'registration_date' => current_time('mysql')
            ),
            array('%s', '%d', '%d', '%s')
        );

        if ($result === false) {
            error_log('Failed to insert serial number: ' . $wpdb->last_error); // Debug log
        }

        return $result !== false;
    }

    /**
     * Get all product registrations
     *
     * @since 1.0.0
     * @return array
     */
    public function get_registrations() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_registrations';
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY registration_date DESC");
    }
}