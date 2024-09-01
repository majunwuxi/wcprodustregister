(function( $ ) {
    'use strict';

    $(function() {
        $('#wc-product-registration-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('action', 'register_product');
            formData.append('nonce', wc_product_registration.nonce);

            $.ajax({
                url: wc_product_registration.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        $('#wc-product-registration-form')[0].reset();
                        // Optionally, refresh the registered products list
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });

})( jQuery );