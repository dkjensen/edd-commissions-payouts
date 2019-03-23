( function($) {

    $('.edd_payout_method_action').on('click', function(e) {
        e.preventDefault();

        // Display loading modal
        swal({
            title: eddcp_obj.strings.loading + "...",
            icon: 'custom',
            button: eddcp_obj.strings.cancel,
            showCancelButton: true,
            showConfirmButton: false,
            customClass: 'edd_commissions_payouts_alert loading'
        });

        // Payout method string
        var payout_method = jQuery(this).attr('data-payout-method') || '';

        var form_data = {
            action: 'edd_user_process_payout_method',
            _wpnonce: eddcp_obj.user_process_payout_method_nonce,
            edd_enable_payout_method: payout_method
        };

        var request = null;

        request = jQuery.ajax({
            type:       'POST',
            dataType:   'json',
            url:        ajaxurl,
            data:       form_data,
        }).done( function( data ) {
            if( data.type == 'error' ) {
                swal({
                    title: eddcp_obj.strings.error,
                    type: 'error'
                });
            }else {
                window.location.href = data.redirect;
            }
        });

        // Abort AJAX request on cancel
        jQuery('body').on('click', '.edd_commissions_payouts_alert .cancel', function(e) {
            if( null !== request ) {
                request.abort();
            }
        });
    });

} )(jQuery);