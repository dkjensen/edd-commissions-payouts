<?php
/**
 * EDD_Commissions_Payout class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payout {

    /**
     * Unique payout ID returned by the payment processor
     *
     * @var string
     */
    protected $id;


    /**
     * Error code returned by the payment processor
     *
     * @var string
     */
    protected $error_code;


    /**
     * Error message returned by the payment processor
     *
     * @var string
     */
    protected $error_message;


    /**
     * Error details returned by the payment processor
     *
     * @var string
     */
    protected $error_details;


    /**
     * Returns the payout method payment processor
     *
     * @var string
     */
    protected $payout_method;
    

    /**
     * Payout message
     *
     * @var string
     */
    protected $message;


    /**
     * Total payout amount
     *
     * @var float
     */
    protected $amount = 0.00;


    /**
     * Total payout fees 
     *
     * @var float
     */
    protected $fees = 0.00;


    /**
     * Recipients
     *
     * @var array
     */
    protected $recipients = array();


    /**
     * Set the payout ID
     *
     * @param string $id
     * @return EDD_Commissions_Payout $this
     */
    public function set_id( $id ) {
        $this->id = $id;

        return $this;
    }


    /**
     * Returns unique payout ID returned by the payment processor 
     *
     * @return string
     */
    public function get_id() {
        return $this->id;
    }


    /**
     * Set an error returned by the payment processor
     *
     * @param string $code
     * @param string $message
     * @param string $details
     * @return EDD_Commissions_Payout $this
     */
    public function set_error( $code, $message, $details = '' ) {
        $this->error_code = $code;
        $this->error_message = $message;
        $this->error_details = $details;

        return $this;
    }


    /**
     * Returns error message returned by the payment processor
     *
     * @return string
     */
    public function get_error_message() {
        return $this->error_message;
    }
    

    /**
     * Returns error code returned by the payment processor
     *
     * @return string
     */
    public function get_error_code() {
        return $this->error_code;
    }


    /**
     * Returns error details returned by the payment processor
     *
     * @return string
     */
    public function get_error_details() {
        return $this->error_details;
    }


    /**
     * Set the payout method payment processor
     *
     * @param string $payout_method
     * @return EDD_Commissions_Payout $this
     */
    public function set_payout_method( $payout_method ) {
        $this->payout_method = $payout_method;

        return $this;
    }


    /**
     * Returns the payout method
     *
     * @return string
     */
    public function get_payout_method() {
        return $this->payout_method;
    }


    /**
     * Set the payout message
     *
     * @param string $message
     * @return EDD_Commissions_Payout $this
     */
    public function set_message( $message ) {
        $this->message = $message;

        return $this;
    }


    /**
     * Returns the payout message
     *
     * @return string
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * Sets the total payout amount
     *
     * @param float $amount
     * @return EDD_Commissions_Payout $this
     */
    public function set_payout_amount( $amount ) {
        $this->amount = (float) $amount;

        return $this;
    }


    /**
     * Returns the total payout amount
     *
     * @return float
     */
    public function get_payout_amount() {
        return (float) $this->amount;
    }


    /**
     * Sets the total payout fees
     *
     * @param float $fees
     * @return EDD_Commissions_Payout $this
     */
    public function set_payout_fees( $fees ) {
        $this->fees = (float) $fees;

        return $this;
    }


    /**
     * Returns the total payout fees
     *
     * @return float
     */
    public function get_payout_fees() {
        return (float) $this->fees;
    }


    /**
     * Sets the recipients array
     *
     * @param array $recipients
     * @return EDD_Commissions_Payout $this
     */
    public function set_recipients( array $recipients ) {
        $this->recipients = $recipients;

        return $this;
    }


    /**
     * Returns an array of payout recipients
     *
     * @return array
     */
    public function get_recipients() {
        return (array) $this->recipients;
    }


    /**
     * Returns a human readable string describing the payout
     *
     * @return string
     */
    protected function payout_message() {
        if ( $this->get_payout_amount() === 0.00 ) {
            $message = sprintf( __( 'No payout owed for the period %s', 'edd-commissions-payouts' ), '' );
        }elseif( empty( $this->get_recipients() ) ) {
            $message = sprintf( __( 'No recipients set to receive payout for the period %s', 'edd-commissions-payouts' ), '' );
        }else {
            $message = sprintf( __( 'Payout initiated in the amount of %s for the period %s', 'edd-commissions-payouts' ), $this->get_payout_amount(), '' );
        }

        return apply_filters( 'edd_commissions_payouts_log_message', $message, $this );
    }


    /**
     * Saves a payout record to the database and logs to the EDD payout log
     *
     * @return void
     */
    public function save() {
        $data = array();
        $methods = preg_grep( '/^get_/', get_class_methods( $this ) );

        var_dump( $this->get_payout_amount() );

        if ( $methods ) {
            foreach ( $methods as $method ) {
                if ( is_callable( array( $this, $method ) ) ) {
                    $prop = substr( $method, 4 );

                    $data[ $prop ] = call_user_func( array( $this, $method ) );
                }
            }
        }

        $payout = wp_insert_post( array(
            'post_type'         => 'edd_payout',
            'post_title'        => $this->get_id(),
            'meta_input'        => $data
        ) );

        EDD_Commissions_Payouts()->helper->log( 
            $this->payout_message(), 
            'Payout', 
            array( 'payout_post_id' => $payout, 'response' => $data ), 
            $this->get_payout_method() 
        );
    }
}