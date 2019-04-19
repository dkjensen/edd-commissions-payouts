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
     * Payout post ID
     *
     * @var string
     */
    protected $id;


    /**
     * Unique payout ID returned by the payment processor
     *
     * @var string
     */
    protected $txn_id;


    /**
     * Notes during interaction with the payment processor
     *
     * @var string
     */
    protected $notes = array();


    /**
     * Errors received during interaction with the payment processor
     *
     * @var string
     */
    protected $errors = array();


    /**
     * Payout status
     *
     * @var string
     */
    protected $status;

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
     * API response details
     *
     * @var string
     */
    protected $details;


    /**
     * If payout ID is given populate the object with post meta
     *
     * @param mixed $payout_id
     */
    public function __construct( $payout_id = null ) {
        if ( null !== $payout_id ) {
            $methods = preg_grep( '/^get_/', get_class_methods( $this ) );

            if ( $methods ) {
                foreach ( $methods as $method ) {
                    if ( is_callable( array( $this, $method ) ) ) {
                        $prop = substr( $method, 4 );

                        $this->{$prop} = get_post_meta( $payout_id, $prop, true );
                    }
                }
            }

            $this->id = $payout_id;
        }
    }


    /**
     * Get post ID if set
     *
     * @return integer
     */
    public function get_id() {
        return $this->id;
    }


    /**
     * Set transaction ID
     *
     * @return EDD_Commissions_Payout $this
     */
    public function set_txn_id( $txn_id ) {
        $this->txn_id = $txn_id;

        return $this;
    }


    /**
     * Get transaction ID from payout gateway response
     *
     * @return string
     */
    public function get_txn_id() {
        return $this->txn_id;
    }


    /**
     * Adds a note returned by the payment processor
     *
     * @param string $note
     * @return EDD_Commissions_Payout $this
     */
    public function add_note( $note ) {
        $this->notes[] = $note;

        return $this;
    }


    /**
     * Returns notes returned by the payment processor
     *
     * @return array
     */
    public function get_notes() {
        return array_filter( (array) $this->notes );
    }


    /**
     * Returns whether or not any notes have been added
     *
     * @return boolean
     */
    public function has_notes() {
        return ! empty( $this->get_notes() );
    }


    /**
     * Adds a error returned by the payment processor
     *
     * @param string $error
     * @return EDD_Commissions_Payout $this
     */
    public function add_error( $error ) {
        $this->errors[] = $error;

        return $this;
    }


    /**
     * Returns errors returned by the payment processor
     *
     * @return array
     */
    public function get_errors() {
        return array_filter( (array) $this->errors );
    }


    /**
     * Returns whether or not any errors have been added
     *
     * @return boolean
     */
    public function has_errors() {
        return ! empty( $this->get_errors() );
    }

    /**
     * Set the payout status
     *
     * @param string $status
     * @return EDD_Commissions_Payout $this
     */
    public function set_status( $status ) {
        $this->status = $status;

        return $this;
    }


    /**
     * Returns the payout status
     *
     * @return string
     */
    public function get_status() {
        return $this->status;
    }


    /**
     * Set the payout details
     *
     * @param string $details
     * @return EDD_Commissions_Payout $this
     */
    public function set_details( $details ) {
        $this->details = $details;

        return $this;
    }


    /**
     * Returns the payout details
     *
     * @return string
     */
    public function get_details() {
        return $this->details;
    }


    /**
     * Sets the total payout amount
     *
     * @param float $amount
     * @return EDD_Commissions_Payout $this
     */
    public function set_amount( $amount ) {
        $this->amount = (float) $amount;

        return $this;
    }


    /**
     * Returns the total payout amount
     *
     * @return float
     */
    public function get_amount() {
        return (float) $this->amount;
    }


    /**
     * Returns the formatted total payout amount with currency symbol
     *
     * @return void
     */
    public function get_formatted_amount() {
        return edd_currency_filter( edd_format_amount( $this->get_amount() ) );
    }


    /**
     * Sets the total payout fees
     *
     * @param float $fees
     * @return EDD_Commissions_Payout $this
     */
    public function set_fees( $fees ) {
        $this->fees = (float) $fees;

        return $this;
    }


    /**
     * Returns the total payout fees
     *
     * @return float
     */
    public function get_fees() {
        return (float) $this->fees;
    }


    /**
     * Returns the formatted total payout fees with currency symbol
     *
     * @return void
     */
    public function get_formatted_fees() {
        return edd_currency_filter( edd_format_amount( $this->get_fees() ) );
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
     * Returns an array of payout recipients, filtered by payout method
     *
     * @param string $payout_method
     * @return array
     */
    public function get_recipients_by_payout_method( $payout_method ) {
        return (array) array_filter( $this->recipients, function( $recipient ) use ( $payout_method ) {
            return $recipient['payout_method'] == $payout_method;
        } );
    }


    /**
     * Sets the recipients array
     *
     * @param array $recipients
     * @return EDD_Commissions_Payout $this
     */
    public function update_recipient( array $data ) {
        $user_id = isset( $data['user_id'] ) ? intval( $data['user_id'] ) : 0;

        if ( isset( $this->recipients[ $user_id ] ) ) {
            $this->recipients[ $user_id ] = array_merge( $this->recipients[ $user_id ], $data );
        }else {
            $this->recipients[ $user_id ] = wp_parse_args( $data, array(
                'user_id'           => $user_id,
                'payout_amount'     => 0,
                'payout_fees'       => 0,
                'payout_currency'   => edd_get_currency(),
                'payout_method'     => null,
                'payout_paid'       => 0,
                'payout_status'     => null,
                'receiver'          => null
            ) );
        }

        return $this;
    }


    public function execute() {
        $payout = array();

        $enabled_payout_methods = EDD_Commissions_Payouts()->helper->get_enabled_payout_methods();

        foreach ( EDD_Commissions_Payouts()->helper->get_payout_data() as $commission ) {
            $user_preferred_method = EDD_Commissions_Payouts()->helper->get_user_preferred_payout_method( $commission['user_id'] );

            $this->update_recipient( array(
                'user_id'           => $commission['user_id'],
                'payout_amount'     => number_format( (float) $commission['amount'], 2 ),
                'payout_method'     => $user_preferred_method
            ) );
        }

        foreach ( $enabled_payout_methods as $key => $payout_method ) {
            $payout_method->process_batch_payout( $this );
        }

        $this->save();
    }


    /**
     * Saves a payout record to the database and logs to the EDD payout log
     *
     * @return EDD_Commissions_Payout
     */
    public function save() {
        $payout = wp_insert_post( array(
            'ID'                => $this->get_id(),
            'post_type'         => 'edd_payout',
            'post_status'       => 'publish',
            'post_title'        => $this->get_id(),
            'meta_input'        => array(
                'txn_id'            => $this->get_txn_id(),
                'notes'             => $this->get_notes(),
                'errors'            => $this->get_errors(),
                'status'            => $this->get_status(),
                'details'           => $this->get_details(),
                'amount'            => $this->get_amount(),
                'fees'              => $this->get_fees(),
                'recipients'        => $this->get_recipients(),
            )
        ) );

        EDD_Commissions_Payouts()->helper->log( 
            __( 'Payout initiated', 'edd-commissions-payouts' ), 
            'Payout', 
            array( 'payout_post_id' => $payout, 'response' => $this->get_details() )
        );

        return new EDD_Commissions_Payout( $payout );
    }
}