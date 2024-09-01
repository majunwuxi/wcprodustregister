<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    WooCommerce_Product_Registration
 * @subpackage WooCommerce_Product_Registration/public
 */

class WC_Product_Registration_Public {

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
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-product-registration-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-product-registration-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wc_product_registration', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wc-product-registration-nonce' )
        ));
    }

    /**
     * Display the product registration form
     *
     * @since    1.0.0
     */
    public function display_registration_form() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Please log in to register your product.', 'wc-product-registration' ) . '</p>';
        }

        ob_start();
        include_once( 'partials/wc-product-registration-form.php' );
        return ob_get_clean();
    }

    /**
     * Display user's registered products
     *
     * @since    1.0.0
     */
    public function display_user_registered_products() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Please log in to view your registered products.', 'wc-product-registration' ) . '</p>';
        }

        $user_id = get_current_user_id();
        $registrations = $this->get_user_registered_products( $user_id );

        ob_start();
        include_once( 'partials/wc-product-registration-user-products.php' );
        return ob_get_clean();
    }

    /**
     * Get user's registered products
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     * @return   array    The user's registered products.
     */
    private function get_user_registered_products( $user_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_product_registrations';
        
        $query = $wpdb->prepare(
            "SELECT pr.*, p.post_title as product_name 
            FROM $table_name pr
            LEFT JOIN {$wpdb->posts} p ON pr.product_id = p.ID
            WHERE pr.user_id = %d
            ORDER BY pr.registration_date DESC",
            $user_id
        );

        return $wpdb->get_results( $query );
    }

    /**
     * AJAX handler for product registration
     *
     * @since    1.0.0
     */
    public function ajax_register_product() {
        check_ajax_referer( 'wc-product-registration-nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to register a product.', 'wc-product-registration' ) ) );
            return;
        }

        $serial_number = sanitize_text_field( $_POST['serial_number'] );
        $product_id = intval( $_POST['product_id'] );
        $user_id = get_current_user_id();

        // Validate serial number
        $validation_result = $this->validate_serial_number( $serial_number, $product_id );
        if ( is_wp_error( $validation_result ) ) {
            wp_send_json_error( array( 'message' => $validation_result->get_error_message() ) );
            return;
        }

        // Register the product
        $registration_result = $this->register_product( $user_id, $product_id, $serial_number );
        if ( is_wp_error( $registration_result ) ) {
            wp_send_json_error( array( 'message' => $registration_result->get_error_message() ) );
            return;
        }

        wp_send_json_success( array( 'message' => __( 'Product successfully registered.', 'wc-product-registration' ) ) );
    }

    /**
     * Validate serial number
     *
     * @since    1.0.0
     * @param    string    $serial_number    The serial number to validate.
     * @param    int       $product_id       The product ID.
     * @return   true|WP_Error    True if valid, WP_Error if not.
     */
    private function validate_serial_number( $serial_number, $product_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_product_registrations';

        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE serial_number = %s AND product_id = %d",
            $serial_number,
            $product_id
        ) );

        if ( ! $result ) {
            return new WP_Error( 'invalid_serial', __( 'Invalid serial number.', 'wc-product-registration' ) );
        }

        if ( $result->user_id != 0 ) {
            return new WP_Error( 'already_registered', __( 'This serial number has already been registered.', 'wc-product-registration' ) );
        }

        return true;
    }

    /**
     * Register a product
     *
     * @since    1.0.0
     * @param    int       $user_id         The user ID.
     * @param    int       $product_id      The product ID.
     * @param    string    $serial_number   The serial number.
     * @return   true|WP_Error    True if registered successfully, WP_Error if not.
     */
    private function register_product( $user_id, $product_id, $serial_number ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_product_registrations';

        $result = $wpdb->update(
            $table_name,
            array(
                'user_id' => $user_id,
                'registration_date' => current_time( 'mysql' )
            ),
            array(
                'serial_number' => $serial_number,
                'product_id' => $product_id
            ),
            array( '%d', '%s' ),
            array( '%s', '%d' )
        );

        if ( $result === false ) {
            return new WP_Error( 'registration_failed', __( 'Failed to register product.', 'wc-product-registration' ) );
        }

        $this->send_confirmation_emails( $user_id, $product_id, $serial_number );

        return true;
    }

    /**
     * Send confirmation emails
     *
     * @since    1.0.0
     * @param    int       $user_id         The user ID.
     * @param    int       $product_id      The product ID.
     * @param    string    $serial_number   The serial number.
     */
    private function send_confirmation_emails( $user_id, $product_id, $serial_number ) {
        $user = get_userdata( $user_id );
        $product = wc_get_product( $product_id );
        $admin_email = get_option( 'admin_email' );

        // Customer email
        $customer_subject = sprintf( __( 'Your product %s has been registered', 'wc-product-registration' ), $product->get_name() );
        $customer_message = sprintf( __( "Thank you for registering your product. Here are the details:\n\nProduct: %s\nSerial Number: %s", 'wc-product-registration' ), $product->get_name(), $serial_number );
        wp_mail( $user->user_email, $customer_subject, $customer_message );

        // Admin email
        $admin_subject = sprintf( __( 'New product registration: %s', 'wc-product-registration' ), $product->get_name() );
        $admin_message = sprintf( __( "A new product has been registered:\n\nUser: %s\nProduct: %s\nSerial Number: %s", 'wc-product-registration' ), $user->user_login, $product->get_name(), $serial_number );
        wp_mail( $admin_email, $admin_subject, $admin_message );
    }
}