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
            'EDD_Commissions_Payout_Method_PayPal',
            'EDD_Commissions_Payout_Method_Stripe'
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
     * Returns the payout method object
     *
     * @param string $method
     * @return object Instance of EDD_Commissions_Payouts_Method
     */
    public function get_payout_method( $method ) {
        $payout_methods = $this->get_payout_methods();

        if ( array_key_exists( $method, $payout_methods ) ) {
            return $payout_methods[ $method ];
        }

        return false;
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


    /**
     * Returns a users preferred payout method
     * 
     * Attempts to set one if not already set
     *
     * @param integer $user_id
     * @return string
     */
    public function get_user_preferred_payout_method( $user_id = false ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $preferred_method = get_user_meta( $user_id, 'edd_preferred_payout_method', true );
        $enabled_methods = $this->get_user_enabled_payout_methods( $user_id );

        if ( ! empty( $enabled_methods ) ) {
            if ( empty( $preferred_method ) || ! in_array( $preferred_method, $enabled_methods ) ) {
                $enabled_methods = $this->get_user_enabled_payout_methods( $user_id );

                if ( ! empty( $enabled_methods ) ) {
                    sort( $enabled_methods );

                    $method_object = $this->set_user_preferred_payout_method( current( $enabled_methods ), $user_id );

                    $preferred_method = $method_object->get_id();
                }
            }
        }

        return apply_filters( 'edd_commissions_user_preferred_payout_method', $preferred_method, $user_id );
    }


    /**
     * Sets a preferred payout method for a given user
     *
     * @param string $method
     * @param integer $user_id
     * @return object Payout method object, instance of EDD_Commissions_Payouts_Method
     */
    public function set_user_preferred_payout_method( $method, $user_id = false ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $payout_method = $this->get_payout_method( $method );

        // Check if this is a registered payout method
        if ( $payout_method ) {

            // Check if this payout method is enabled on the site
            if ( array_key_exists( $method, $this->get_enabled_payout_methods() ) ) {

                // Check if the payout method is enabled for the user
                if ( in_array( $method, $this->get_user_enabled_payout_methods( $user_id ) ) ) {
                    update_user_meta( $user_id, 'edd_preferred_payout_method', $method );

                    return $payout_method;
                }else {
                    throw new Exception( sprintf( __( 'Payout method %s must be enabled to be set as the preferred payout method.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
                }
            }else {
                throw new Exception( sprintf( __( 'Payout method %s is not enabled on this site.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
            }
        }else {
            throw new Exception( sprintf( __( 'Payout method %s does not exist.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
        }
    }


    /**
     * Attempts to enable the payout method of a given user
     *
     * @param string $method
     * @param integer $user_id
     * @return object Payout method object, instance of EDD_Commissions_Payouts_Method
     */
    public function enable_user_payout_method( $method, $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $enabled_methods = $this->get_user_enabled_payout_methods( $user_id );

        $payout_method = $this->get_payout_method( $method );

        // Check if this is a registered payout method
        if ( $payout_method ) {

            // Check if this payout method is enabled on the site
            if ( array_key_exists( $method, $this->get_enabled_payout_methods() ) ) {

                // Check if the user already has this payout method enabled
                if ( ! in_array( $method, $enabled_methods ) ) {
                    update_user_meta( $user_id, 'edd_enabled_payout_methods', array_merge( $enabled_methods, array( $method ) ) );

                    // Revalidate users preferred payout method
                    $this->get_user_preferred_payout_method( $user_id );

                    return $payout_method;
                }else {
                    throw new Exception( sprintf( __( 'Payout method %s is already enabled.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
                }
            }else {
                throw new Exception( sprintf( __( 'Payout method %s is not enabled on this site.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
            }
        }else {
            throw new Exception( sprintf( __( 'Payout method %s does not exist.', 'edd-commissions-payouts' ), esc_html( $method ) ) );
        }
    }


    /**
     * Attempts to remove the payout method from a given user
     *
     * @param string $method
     * @param integer $user_id
     * @return object Payout method object, instance of EDD_Commissions_Payouts_Method
     */
    public function remove_user_payout_method( $method, $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $enabled_methods = $this->get_user_enabled_payout_methods( $user_id );

        if ( ( $key = array_search( $method, $enabled_methods ) ) !== false ) {
            unset( $enabled_methods[ $key ] );

            update_user_meta( $user_id, 'edd_enabled_payout_methods', $enabled_methods );

            // Revalidate users preferred payout method
            $this->get_user_preferred_payout_method( $user_id );

            return $this->get_payout_method( $method ) ? $this->get_payout_method( $method ) : true;
        }

        throw new Exception( __( 'Unable to remove payout method due to not being enabled.', 'edd-commissions-payouts' ) );
    }


    /**
     * Returns the url of the page containing the payout methods
     *
     * @return string
     */
    public function get_payout_methods_dashboard_uri() {
        $uri = home_url();

        $page_id = function_exists( 'EDD_FES' ) ? EDD_FES()->helper->get_option( 'fes-vendor-dashboard-page', get_the_ID() ) : get_the_ID();

        if( $permalink = get_permalink( $page_id ) ) {
            $uri = add_query_arg( array( 'task' => 'earnings' ), $permalink );
        }
    
        return apply_filters( 'edd_commissions_payouts_dashboard_uri', esc_url( $uri ), $page_id );
    }
}