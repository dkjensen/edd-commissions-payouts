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


    public function get_id() {
        return $this->id;
    }


    /**
     * Returns the rendered name of the payment method
     *
     * @return string
     */
    public function get_name() {
        return apply_filters( 'edd_commissions_payout_method_name', $this->name, $this->id );
    }


    /**
     * Returns the payout method icon
     *
     * @return string
     */
    public function get_icon() {
        $icon = apply_filters( 'edd_commissions_payout_method_icon', $this->icon, $this->id );

        return $icon ? sprintf( '<img src="%s" alt="%s" />', esc_attr( $icon ), esc_attr( $this->get_name() ) ) : '';
    }


    /**
     * Returns the url endpoint to activate/enable the payout method
     *
     * @return string
     */
    public function get_enable_uri() {
        $enable_uri = $this->enable_uri ? wp_nonce_url( add_query_arg( array( 'edd_enable_payout_method' => $this->get_id() ), esc_url( $this->enable_uri ) ), 'enable_payout_method' ) : '';

        return apply_filters( 'edd_commissions_payout_method_enable_uri', $enable_uri, $this->id );
    }


    /**
     * Returns redirect URL after attempting to enable payout method
     *
     * @return string
     */
    public function get_redirect_uri() {
        $redirect_uri = $this->redirect_uri ? esc_url( $this->redirect_uri ) : '';

        return apply_filters( 'edd_commissions_payout_method_redirect_uri', $redirect_uri, $this->id );
    }

}