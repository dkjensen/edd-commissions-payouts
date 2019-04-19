<?php
/**
 * Class PayoutTest
 *
 * @package EDD Commissions Payouts
 */

/**
 * EDD_Commissions_Payout Tests
 */
class PayoutTest extends WP_UnitTestCase {

    public static function setUpBeforeClass() {
        edd_update_option( 'edd_commissions_payout_methods', array( 'paypal' => 'PayPal' ) );
    }


    public function test_execute() {
        add_filter( 'edd_commissions_payout_note', function() { return 'ERRPYO002'; } );
        EDD_Helper_Payment::create_bulk_payments( 5 );
        EDD_Helper_Payment::create_simple_payment( array(
            'email'     => 'sandbox@dkjensen.com',
        ) );

        $payout = new EDD_Commissions_Payout;
        $payout->execute();

        var_dump( $payout->get_recipients() );

        var_dump( $payout->get_errors() );

        var_dump( $payout->has_errors() );
    }

}
