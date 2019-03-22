<?php
/**
 * Helper class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Helper {

    /**
     * Array of registered payout methods
     *
     * @var array
     */
    public $payout_methods = null;


    /**
     * Array of payout method class names to be loaded 
     *
     * @return array
     */
    private function get_payout_method_class_names() {
        $payout_methods = array(
            'EDD_Commissions_Payout_Method_PayPal'
        );

        return apply_filters( 'edd_commissions_payout_methods', $payout_methods );
    }


    /**
     * Load all registered payout methods
     *
     * @return void
     */
    private function load_payout_methods() {
        foreach ( $this->get_payout_method_class_names() as $method_class ) {
			$this->register_payout_method( $method_class );
        }
        
        do_action( 'edd_commissions_payouts_load_payout_methods' );

		return $this->get_payout_methods();
    }


    /**
     * Load payout method classes if exists and add to payout_methods property
     *
     * @param string $method
     * @return void
     */
    private function register_payout_method( $method ) {
        if ( ! is_object( $method ) ) {
			if ( ! class_exists( $method ) ) {
				return false;
            }
            
			$method = new $method();
        }
        
		if ( is_null( $this->payout_methods ) ) {
			$this->payout_methods = array();
        }
        
		$this->payout_methods[ $method->id ] = $method;
    }


    /**
     * Return all available payout methods
     *
     * @return array
     */
    public function get_payout_methods() {
		if ( is_null( $this->payout_methods ) ) {
			$this->load_payout_methods();
        }

		return $this->payout_methods;
    }
    

    /**
     * Returns array of enabled payout methods
     *
     * @return array
     */
    public function get_enabled_payout_methods() {
        $enabled_methods = array();

        $enabled = edd_get_option( 'edd_commissions_payout_methods', array() );

        foreach ( $this->get_payout_methods() as $id => $method ) {
            if ( array_key_exists( $id, $enabled ) ) {
                $enabled_methods[ $id ] = $method;
            }
        }

        return (array) apply_filters( 'edd_commissions_payouts_enabled_payout_methods', array_filter( $enabled_methods ) );
    }


    /**
     * Returns array of user enabled payout methods
     *
     * @param integer $user_id
     * @return array
     */
    public function get_user_enabled_payout_methods( $user_id = false ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $enabled_methods = (array) get_user_meta( $user_id, 'edd_enabled_payout_methods', true );

        return apply_filters( 'edd_commissions_user_enabled_payout_methods', array_filter( $enabled_methods ), $user_id );
    }


    public function add_user_payout_method( $method, $user_id = false ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $enabled_methods = $this->get_user_enabled_payout_methods( $user_id );

        // Check if this is a registered payout method
        if ( array_key_exists( $method, $this->get_payout_methods() ) ) {

            // Check if the user already has this payout method enabled
            if ( ! in_array( $method, $enabled_methods ) ) {
                $method_object = new $this->payout_methods[ $method ];

                update_user_meta( $user_id, 'edd_enabled_payout_methods', array_merge( $enabled_methods, array( $method ) ) );

                wp_safe_redirect( $method_object->get_redirect_uri() );
                exit;
            }else {
                throw new Exception( sprintf( __( 'Payout method %s is already enabled.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
            }
        }else {
            throw new Exception( sprintf( __( 'Payout method %s does not exist.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
        }

        return true;
    }

}