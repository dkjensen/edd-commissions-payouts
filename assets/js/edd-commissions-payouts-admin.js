( function($) {

    jQuery( '#edd-payout-schedule-form :input' ).on( 'change', function() {

        jQuery('#edd-next-payout').html( eddcp_admin_obj.strings.next_payout_loading );

        var on = jQuery( '[name="edd_settings[edd_commissions_payout_schedule_on][]"]:checked' ).map( function() {
            return jQuery( this ).val();
        } ).get();

        var form_data = {
            action: 'edd_preview_updated_payout_schedule',
            fields: {
                mode:       jQuery( '[name="edd_settings[edd_commissions_payout_schedule_mode]"]' ).val(),
                interval:   jQuery( '[name="edd_settings[edd_commissions_payout_schedule_interval]"]' ).val(),
                on:         on,
                hour:       jQuery( '[name="edd_settings[edd_commissions_payout_schedule_time_hr]"]' ).val(),
                min:        jQuery( '[name="edd_settings[edd_commissions_payout_schedule_time_min]"]' ).val()
            },
            _wpnonce: eddcp_admin_obj.next_payout_nonce,
        };

        jQuery.ajax({
            type:       'GET',
            url:        ajaxurl,
            data:       form_data,
        }).done( function( data ) {
            jQuery('#edd-next-payout').html( data );
        }).fail( function() {
            jQuery('#edd-next-payout').html( eddcp_admin_obj.strings.next_payout_failed );
        });
    } );

    jQuery( '#edd_disable_automatic_payouts' ).on( 'click', function(e) {
        var prompt = confirm( eddcp_admin_obj.strings.confirm_disable_automatic_payouts );

        if ( prompt != true ) {
            e.preventDefault();
        }
    } );

    jQuery( '#edd_enable_automatic_payouts' ).on( 'click', function(e) {
        var prompt = confirm( eddcp_admin_obj.strings.confirm_enable_automatic_payouts );

        if ( prompt != true ) {
            e.preventDefault();
        }
    } );

} )(jQuery);