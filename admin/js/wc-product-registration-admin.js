(function( $ ) {
    'use strict';

    $(function() {
        // Handle file import
        $('#wc-product-registration-import-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('action', 'import_serial_numbers');
            formData.append('nonce', wc_product_registration_admin.nonce);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Confirm deletion of serial number
        $('.delete-serial').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this serial number?')) {
                e.preventDefault();
            }
        });
    });

})( jQuery );