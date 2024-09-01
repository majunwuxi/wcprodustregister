<?php
/**
 * Class WC_Product_RegistrationTest
 *
 * @package Wc_Product_Registration
 */

class WC_Product_RegistrationTest extends WP_UnitTestCase {

    public function test_plugin_initialization() {
        $this->assertTrue( class_exists( 'WC_Product_Registration' ) );
    }

    public function test_shortcodes_registered() {
        global $shortcode_tags;
        $this->assertArrayHasKey( 'product_registration_form', $shortcode_tags );
        $this->assertArrayHasKey( 'user_registered_products', $shortcode_tags );
    }

    // Add more tests as needed
}