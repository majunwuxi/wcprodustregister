<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WC_Product_Registration
 * @subpackage WC_Product_Registration/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php
    // Display import result message
    if (isset($_GET['imported'])) {
        $imported_count = intval($_GET['imported']);
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . sprintf(__('Successfully imported %d serial numbers.', 'wc-product-registration'), $imported_count) . '</p>';
        echo '</div>';
    }
    ?>

    <form action="options.php" method="post">
    <?php
        settings_fields('wc_product_registration_options_group');
        do_settings_sections('wc_product_registration_settings');
        submit_button('Save Settings');
    ?>
    </form>

    <div class="import-section">
        <h2><?php esc_html_e('Import Serial Numbers', 'wc-product-registration'); ?></h2>
        <form id="wc-product-registration-import-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="wc_product_registration_import">
            <?php wp_nonce_field('wc_product_registration_import', 'wc_product_registration_import_nonce'); ?>
            <input type="file" name="serial_numbers_file" accept=".csv,.xlsx" required>
            <p class="description"><?php esc_html_e('Upload a CSV or XLSX file containing serial numbers and product IDs.', 'wc-product-registration'); ?></p>
            <button type="submit" class="button button-primary"><?php esc_html_e('Import', 'wc-product-registration'); ?></button>
        </form>
    </div>

    <div class="registered-products">
        <h2><?php esc_html_e('Registered Products', 'wc-product-registration'); ?></h2>
        <?php
        $registrations = $this->get_registrations();
        if (!empty($registrations)) :
        ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('User', 'wc-product-registration'); ?></th>
                        <th><?php esc_html_e('Product', 'wc-product-registration'); ?></th>
                        <th><?php esc_html_e('Serial Number', 'wc-product-registration'); ?></th>
                        <th><?php esc_html_e('Registration Date', 'wc-product-registration'); ?></th>
                        <th><?php esc_html_e('Actions', 'wc-product-registration'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration) : ?>
                        <tr>
                            <td><?php echo esc_html(get_userdata($registration->user_id)->user_login); ?></td>
                            <td><?php echo esc_html(get_the_title($registration->product_id)); ?></td>
                            <td><?php echo esc_html($registration->serial_number); ?></td>
                            <td><?php echo esc_html($registration->registration_date); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin-post.php?action=edit_serial&id=' . $registration->id)); ?>" class="button button-small"><?php esc_html_e('Edit', 'wc-product-registration'); ?></a>
                                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=delete_serial&id=' . $registration->id), 'delete_serial_' . $registration->id)); ?>" class="button button-small delete-serial"><?php esc_html_e('Delete', 'wc-product-registration'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php esc_html_e('No product registrations found.', 'wc-product-registration'); ?></p>
        <?php endif; ?>
    </div>
</div>