<?php
/**
 * Abstract class for payment methods
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class EDD_Commissions_Payouts_Method {

    /**
     * Slug of the payment method
     *
     * @var string
     */
    public $id = '';

    /**
     * Rendered name of the payment method
     *
     * @var string
     */
    public $name = '';

    /**
     * URL of payout icon
     *
     * @var string
     */
    public $icon = '';

    /**
     * URL endpoint to enable / process payout method
     *
     * @var string
     */
    public $enable_uri = '';

    /**
     * Redirect URL after attempting to enable payout method
     *
     * @var string
     */
    public $redirect_uri = '';


    /**
     * Does this payout method require authentication
     *
     * @var boolean
     */
    public $authentication = false;


    public function __construct() {
        $this->setup();
    }


    /**
     * Extending class will setup variables using this function
     *
     * @return void
     */
    abstract protected function setup();


    /**
     * Performs the actual payout
     *
     * @param EDD_Commissions_Payout Instance of the payout
     * @return void
     */
    abstract public function process_batch_payout( EDD_Commissions_Payout &$payout );


    /**
     * Returns the unique identifier for the payout method
     *
     * @return void
     */
    public function get_id() {
        return $this->id;
    }


    /**
     * Returns the rendered name of the payment method
     *
     * @return string
     */
    public function get_name() {
        return apply_filters( 'edd_commissions_payout_method_name', $this->name, $this );
    }


    /**
     * Returns the payout method icon
     *
     * @return string
     */
    public function get_icon() {
        $icon = apply_filters( 'edd_commissions_payout_method_icon', $this->icon, $this );

        return $icon ? sprintf( '<img src="%s" alt="%s" />', esc_attr( $icon ), esc_attr( $this->get_name() ) ) : '';
    }


    /**
     * Returns the url endpoint to toggle the enabled status of the payout method
     *
     * @return string
     */
    public function get_toggle_status_uri() {
        $enabled_methods  = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods();

        $payout_method_uri = EDD_Commissions_Payouts()->helper->get_payout_methods_dashboard_uri();
        $toggle_status_uri = wp_nonce_url( add_query_arg( array( 
            'edd_payout_method' => $this->get_id(),
            'edd_payout_action' => ! in_array( $this->get_id(), $enabled_methods ) ? 'enable' : 'remove',
            'edd_action'        => 'toggle_payout_method'
        ), $payout_method_uri ), 'process_payout_method' );

        return apply_filters( 'edd_commissions_payout_method_toggle_status_uri', $toggle_status_uri, $this );
    }


    /**
     * Returns the url endpoint to set the preferred payout method
     *
     * @return string
     */
    public function get_set_as_preferred_uri() {
        $payout_method_uri = EDD_Commissions_Payouts()->helper->get_payout_methods_dashboard_uri();
        $set_as_preferred_uri = wp_nonce_url( add_query_arg( array( 
            'edd_payout_method' => $this->get_id(),
            'edd_action'        => 'preferred_payout_method'
        ), $payout_method_uri ), 'process_payout_method' );
    
        return apply_filters( 'edd_commissions_payout_method_set_as_preferred_uri', $set_as_preferred_uri, $this );
    }


    /**
     * Returns redirect URL after attempting to enable payout method
     *
     * @return string
     */
    public function get_redirect_uri() {
        $redirect_uri = $this->redirect_uri ? esc_url( $this->redirect_uri ) : '';

        return apply_filters( 'edd_commissions_payout_method_redirect_uri', $redirect_uri, $this );
    }


    /**
     * Returns the message displayed to the user after enabling the payout method
     *
     * @return string
     */
    public function enabled_message() {
        $message = sprintf( __( '%s payout method has been enabled successfully.', 'edd-commissions-payouts' ) );

        return apply_filters( 'edd_commissions_payout_method_enabled_message', $message, $this );
    }


    /**
     * Returns the message displayed to the user after removing the payout method
     *
     * @return string
     */
    public function removed_message() {
        $message = sprintf( __( '%s payout method has been removed successfully.', 'edd-commissions-payouts' ) );

        return apply_filters( 'edd_commissions_payout_method_removed_message', $message, $this );
    }


    /**
     * Returns whether this payout method requires authentication
     *
     * @return bool
     */
    public function requires_authentication() {
        return (bool) $this->authentication;
    }


    /**
     * Renders admin settings
     *
     * @return string
     */
    public function settings() {
        return array();
    }


    /**
     * Logs a notice to the EDD Payouts log
     *
     * @param string $message
     * @param string $details
     * @return void
     */
    public function log_notice( $message, $details ) {
        EDD_Commissions_Payouts()->helper->log( $message, 'Notice', $details, $this->get_id() );
    }


    /**
     * Logs an error to the EDD Payouts log
     *
     * @param string $message
     * @param string $details
     * @return void
     */
    public function log_error( $message, $details ) {
        EDD_Commissions_Payouts()->helper->log( $message, 'Error', $details, $this->get_id() );
    }
}