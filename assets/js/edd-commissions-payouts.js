( function($) {

    function swalLoading() {
        swal({
            title: eddcp_obj.strings.loading + "...",
            icon: 'custom',
            button: eddcp_obj.strings.cancel,
            showCancelButton: true,
            showConfirmButton: false,
            customClass: 'edd_commissions_payouts_alert loading'
        });
    }

    jQuery('.edd_payout_method_action').on('click', function(e) {
        e.preventDefault();

        // Display loading modal
        swalLoading();

        // Payout method string
        var payout_method = jQuery(this).attr('data-payout-method') || '';
        var payout_action = jQuery(this).attr('data-payout-action') || '';

        var form_data = {
            action: 'edd_user_process_toggle_payout_method',
            _wpnonce: eddcp_obj.user_payout_method_nonce,
            edd_payout_method: payout_method,
            edd_payout_action: payout_action,
            edd_action: 'toggle_payout_method'
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
                    text: data.message,
                    type: 'error'
                });
            }else {
                window.location.href = data.redirect;
            }
        });
    });

    jQuery('.edd_preferred_payout_method_action').on('click', function(e) {
        e.preventDefault();

        // Display loading modal
        swalLoading();

        // Payout method string
        var payout_method = jQuery(this).attr('data-payout-method') || '';

        var form_data = {
            action: 'edd_user_process_preferred_payout_method',
            _wpnonce: eddcp_obj.user_payout_method_nonce,
            edd_payout_method: payout_method,
            edd_action: 'preferred_payout_method'
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
                    text: data.message,
                    type: 'error'
                });
            }else {
                window.location.href = data.redirect;
            }
        });
    });

    // Abort AJAX request on cancel
    jQuery('body').on('click', '.edd_commissions_payouts_alert .cancel', function(e) {
        if( null !== request ) {
            request.abort();
        }
    });

} )(jQuery);