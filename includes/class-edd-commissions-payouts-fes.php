<?php
/**
 * FES integration class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_FES {

    public function __construct() {
        add_filter( 'fes_template_paths', array( $this, 'template_paths' ) );
        add_action( 'eddc_before_commissions_overview', array( $this, 'frontend_payout_methods' ) );

        add_action( 'edd_dashboard_earnings_before_payout_methods', array( $this, 'payout_method_notices' ) );
    }


    /**
     * Load the Payout Methods template file
     *
     * @param integer $user_id
     * @return void
     */
    public function frontend_payout_methods( $user_id ) {
        EDD_FES()->templates->fes_get_template_part( 'frontend', 'payout-methods' );
    }


    /**
     * Add our template directory to the FES template locator
     *
     * @param array $file_paths
     * @return array
     */
    public function template_paths( $file_paths ) {
        $file_paths[9876] = EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'templates';

        return $file_paths;
    }


    public function payout_method_notices() {
        
    }

}