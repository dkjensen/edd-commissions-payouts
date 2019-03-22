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
        if ( apply_filters( 'edd_commissions_payouts_load_frontend_css', true ) ) {
            $suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

            wp_enqueue_style( 'edd-commissions-payouts', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/css/edd-commissions-payouts' . $suffix . '.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
            wp_enqueue_style( 'edd-commissions-payouts-icons', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/icons/style.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
        }
    }

}