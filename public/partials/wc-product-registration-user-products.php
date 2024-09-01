<div class="wc-registered-products">
    <h2><?php esc_html_e( 'Your Registered Products', 'wc-product-registration' ); ?></h2>
    <?php if ( ! empty( $registrations ) ) : ?>
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Product Name', 'wc-product-registration' ); ?></th>
                    <th><?php esc_html_e( 'Serial Number', 'wc-product-registration' ); ?></th>
                    <th><?php esc_html_e( 'Registration Date', 'wc-product-registration' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $registrations as $registration ) : ?>
                    <tr>
                        <td><?php echo esc_html( $registration->product_name ); ?></td>
                        <td><?php echo esc_html( $registration->serial_number ); ?></td>
                        <td><?php echo esc_html( $registration->registration_date ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e( 'You have no registered products.', 'wc-product-registration' ); ?></p>
    <?php endif; ?>
</div>