<?php
/**
 * EDD Commissions integration class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Commissions {

    public function __construct() {
        add_filter( 'eddc_settings', array( $this, 'settings' ) );
    }

    /**
     * Registers the new Commissions options in Extensions
     *
     * @since       1.2.1
     * @param       $settings array the existing plugin settings
     * @return      array The new EDD settings array with commissions added
     */
    public function settings( $settings ) {
        $payout_methods = EDD_Commissions_Payouts()->helper->get_payout_methods();
        $payout_methods = array_filter( array_map( array( $this, 'convert_object_to_name' ), $payout_methods ) );

        $settings[] = array(
            'id'      => 'edd_commissions_payout_methods',
            'name'    => __( 'Enabled Payout Methods', 'edd-commissions-payouts' ),
            'type'    => 'multicheck',
            'options' => $payout_methods
        );
        
        return $settings;
    }


    /**
     * Retrieves name of payout method from object
     *
     * @param object $class Object that inherits EDD_Commissions_Payouts_Method
     * @return mixed
     */
    public function convert_object_to_name( $class ) {
        if( $class instanceof EDD_Commissions_Payouts_Method ) {
            return $class->get_name();
        }

        return null;
    }
}