<?php
/**
 * Setup methods class file
 * 
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Setup {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }


    public function enqueue_scripts() {
        $suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

        if ( apply_filters( 'edd_commissions_payouts_load_frontend_css', true ) ) {
            wp_enqueue_style( 'edd-commissions-payouts', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/css/edd-commissions-payouts' . $suffix . '.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
            wp_enqueue_style( 'edd-commissions-payouts-icons', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/icons/style.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
        }

        wp_register_script( 'edd-commissions-payouts', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/js/edd-commissions-payouts' . $suffix . '.js', array( 'jquery', 'fes_form', 'fes_sw', 'fes_spin', 'fes_spinner' ), EDD_COMMISSIONS_PAYOUTS_VER, true );
    
        wp_localize_script( 'edd-commissions-payouts', 'eddcp_obj', array(
            'user_process_payout_method_nonce'        => wp_create_nonce( 'enable_payout_method' ),
            'strings'                                 => array(
                'error'                               => __( 'Error', 'edd-commissions-payouts' ),
                'success'                             => __( 'Success', 'edd-commissions-payouts' ),
                'loading'                             => __( 'Loading', 'edd-commissions-payouts' ),
                'cancel'                              => __( 'Cancel', 'edd-commissions-payouts' ),
            )
        ) );

        wp_enqueue_script( 'edd-commissions-payouts' );
    }

}