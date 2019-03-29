<?php
/**
 * Form processing class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Form_Handler {

    public function __construct() {
        // Process non AJAX requests
        add_action( 'wp_loaded', array( $this, 'user_process_toggle_payout_method' ) );
        add_action( 'wp_loaded', array( $this, 'user_process_preferred_payout_method' ) );
        add_action( 'wp_loaded', array( $this, 'toggle_scheduled_payout_status' ) );

        // AJAX requests
        add_action( 'wp_ajax_edd_user_process_toggle_payout_method', array( $this, 'user_process_toggle_payout_method' ) );
        add_action( 'wp_ajax_edd_user_process_preferred_payout_method', array( $this, 'user_process_preferred_payout_method' ) );

        if ( is_admin() ) {
            add_action( 'wp_ajax_edd_preview_updated_payout_schedule', array( $this, 'preview_updated_payout_schedule' ) );
        }
    }
    

    /**
     * Process both AJAX and non AJAX request to toggle the enabled status of user payout method
     *
     * @return void
     */
    public function user_process_toggle_payout_method() {
        if ( isset( $_REQUEST['edd_action'] ) && $_REQUEST['edd_action'] == 'toggle_payout_method' ) {
            try {
                $payout_method = isset( $_REQUEST['edd_payout_method'] ) ? $_REQUEST['edd_payout_method'] : '';
                $payout_action = isset( $_REQUEST['edd_payout_action'] ) ? $_REQUEST['edd_payout_action'] : '';

                if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'process_payout_method' ) ) {
                    throw new Exception( __( 'Invalid nonce, please refresh the page and try again.', 'edd-commissions-payouts' ) );
                }

                if ( ! is_user_logged_in() ) {
                    throw new Exception( __( 'You must be logged in to do that.', 'edd-commissions-payouts' ) );
                }

                if ( ! EDD_FES()->vendors->vendor_is_status( 'approved' ) ) {
                    throw new Exception( __( 'You must be an approved vendor to do that.', 'edd-commissions-payouts' ) );
                }

                if ( ! in_array( $payout_action, array( 'enable', 'remove' ) ) ) {
                    throw new Exception( __( 'Invalid request.', 'edd-commissions-payouts' ) );
                }

                if ( 'enable' === $payout_action ) {
                    // Returns object instance of EDD_Commissions_Payouts_Method
                    $response = EDD_Commissions_Payouts()->helper->enable_user_payout_method( $payout_method );
                }else {
                    // Returns object instance of EDD_Commissions_Payouts_Method
                    $response = EDD_Commissions_Payouts()->helper->remove_user_payout_method( $payout_method );
                }

                // AJAX response
                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    wp_send_json( array(
                        'type'          => 'success',
                        'message'       => __( 'Success', 'edd-commissions-payouts' ),
                        'redirect'      => $response->get_redirect_uri()
                    ) );
                }
            }catch( Exception $e ) {
                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    wp_send_json( array(
                        'type'          => 'error',
                        'message'       => $e->getMessage() 
                    ) );
                }
            }
        }
    }


    /**
     * Process both AJAX and non AJAX request to toggle the preferred payout method of a user
     *
     * @return void
     */
    public function user_process_preferred_payout_method() {
        if ( isset( $_REQUEST['edd_action'] ) && $_REQUEST['edd_action'] == 'preferred_payout_method' ) {
            try {
                $payout_method = isset( $_REQUEST['edd_payout_method'] ) ? $_REQUEST['edd_payout_method'] : '';

                if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'process_payout_method' ) ) {
                    throw new Exception( __( 'Invalid nonce, please refresh the page and try again.', 'edd-commissions-payouts' ) );
                }

                if ( ! is_user_logged_in() ) {
                    throw new Exception( __( 'You must be logged in to do that.', 'edd-commissions-payouts' ) );
                }

                if ( ! EDD_FES()->vendors->vendor_is_status( 'approved' ) ) {
                    throw new Exception( __( 'You must be an approved vendor to do that.', 'edd-commissions-payouts' ) );
                }

                // Returns object instance of EDD_Commissions_Payouts_Method
                $response = EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( $payout_method );

                // AJAX response
                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    wp_send_json( array(
                        'type'          => 'success',
                        'message'       => __( 'Success', 'edd-commissions-payouts' ),
                        'redirect'      => $response->get_redirect_uri()
                    ) );
                }
            }catch( Exception $e ) {
                if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                    wp_send_json( array(
                        'type'          => 'error',
                        'message'       => $e->getMessage() 
                    ) );
                }
            }
        }
    }


    /**
     * AJAX request to return a preview of the updated payout schedule
     *
     * @return void
     */
    public function preview_updated_payout_schedule() {
        try {
            $fields = isset( $_REQUEST['fields'] ) ? (array) $_REQUEST['fields'] : array();

            if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'next_payout_nonce' ) ) {
                throw new Exception( __( 'Invalid nonce, please refresh the page and try again.', 'edd-commissions-payouts' ) );
            }

            if ( ! current_user_can( 'manage_shop_settings' ) ) {
                throw new Exception( __( 'You do not have the required capabilities to view the next payout date.', 'edd-commissions-payouts' ) );
            }

            $mode       = isset( $fields['mode'] ) ? $fields['mode'] : '';
            $interval   = isset( $fields['interval'] ) ? $fields['interval'] : '';
            $repeats_on = isset( $fields['on'] ) ? $fields['on'] : '';
            $hour       = isset( $fields['hour'] ) ? $fields['hour'] : '';
            $minute     = isset( $fields['min'] ) ? $fields['min'] : '';

            /**
             * Convert user given time to UTC
             */
            $utc_date = new DateTime;
            $utc_date->setTime( $hour, $minute );
            $utc_date->setTimestamp( $utc_date->getTimestamp() - ( get_option( 'gmt_offset' ) * 3600 ) );
            
            $payout_schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( $mode, $interval, $repeats_on, $utc_date->format( 'G' ), $utc_date->format( 'i' ) );
            $payout_schedule->count( 5 )
                            ->generateOccurrences();

            $occurrences = $payout_schedule->occurrences;

            $schedule  = '<p><strong>' . __( 'Updated payout schedule preview', 'edd-commissions-payouts' ) . '</strong></p>';

            foreach ( $occurrences as $key => $occurrence ) {
                $schedule .= '<div class="edd-payout-occurrence">' . date_i18n( edd_commissions_payouts_time_format(), edd_commissions_payouts_convert_utc_timestamp( $occurrence->getTimestamp() ) )  . '</div>';
            }

            $schedule .= '<br>';
            $schedule .= '<div class="edd-payout-current-time">' . __( 'Current time', 'edd-commissions-payouts' ) . ': ' . date_i18n( edd_commissions_payouts_time_format() ) . '</div>';

            wp_die( $schedule );
        }catch( Exception $e ) {
            wp_die( $e->getMessage() );
        }
    }


    /**
     * Process both AJAX and non AJAX request to toggle the preferred payout method of a user
     *
     * @return void
     */
    public function toggle_scheduled_payout_status() {
        if ( is_admin() && isset( $_REQUEST['edd_toggle_payout_schedule_status'] ) ) {
            try {
                $new_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';

                if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'toggle_payout_schedule_status' ) ) {
                    throw new Exception( __( 'Invalid nonce, please refresh the page and try again.', 'edd-commissions-payouts' ) );
                }
    
                if ( ! current_user_can( 'manage_shop_settings' ) ) {
                    throw new Exception( __( 'You do not have the required capabilities to view the next payout date.', 'edd-commissions-payouts' ) );
                }

                if ( $new_status !== 'enable' && $new_status !== 'disable' ) {
                    throw new Exception( __( 'Invalid payout schedule status.', 'edd-commissions-payouts' ) );
                }

                if ( 'enable' === $new_status ) {
                    EDD_Commissions_Payouts()->schedule->enable();
                }else {
                    EDD_Commissions_Payouts()->schedule->disable();
                }

                $goback = add_query_arg( 'settings-updated', 'true',  remove_query_arg( array( 'edd_toggle_payout_schedule_status' ), wp_get_referer() ) );

                wp_safe_redirect( $goback );
                exit;
            }catch( Exception $e ) {
                wp_die( $e->getMessage() );
            }
        }
    }
}