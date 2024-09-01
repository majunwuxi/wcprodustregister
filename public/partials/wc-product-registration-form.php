<div class="wc-product-registration-form">
    <h2><?php esc_html_e( 'Register Your Product', 'wc-product-registration' ); ?></h2>
    <form id="wc-product-registration-form">
        <label for="serial_number"><?php esc_html_e( 'Serial Number:', 'wc-product-registration' ); ?></label>
        <input type="text" id="serial_number" name="serial_number" required>

        <label for="product_id"><?php esc_html_e( 'Product ID:', 'wc-product-registration' ); ?></label>
        <input type="text" id="product_id" name="product_id" required>

        <button type="submit"><?php esc_html_e( 'Register Product', 'wc-product-registration' ); ?></button>
    </form>
</div>