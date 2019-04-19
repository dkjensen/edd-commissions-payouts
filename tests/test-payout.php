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
        // Let's make some default users for later
		// `Author` can be used to make products that have commissions assigned to him
		$author = array(
			'user_login'  =>  'author',
			'roles'       =>  array( 'author' ),
			'user_pass'   => NULL,
		);

		wp_insert_user( $author ) ;

		// `Subscriber` can be used to check functions that should only work for commission recipients
		$subscriber = array(
			'user_login'  =>  'subscriber',
			'roles'       =>  array( 'subscriber' ),
			'user_pass'   => NULL,
		);

		wp_insert_user( $subscriber ) ;

        $_payment_id  = EDD_Helper_Payment::create_simple_payment();
		$_payment     = new EDD_Payment( $_payment_id );
		$_download_id = $_payment->downloads[ 0 ][ 'id' ];
		$_download    = new EDD_Download( $_download_id );
		$_user        = get_user_by( 'login', 'subscriber' );
		$_author      = get_user_by( 'login', 'author' );

		// Set the product's rates
		$commissions_config = array(
			'type'    => 'percentage',
			'amount'  => '10',
			'user_id' => $_author->ID,
		);

		update_post_meta( $_download_id, '_edd_commisions_enabled', 'commissions_enabled' );
		update_post_meta( $_download_id, '_edd_commission_settings', $commissions_config );

		$_payment->status = 'publish';
		$_payment->save();

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', 2 );
        EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'paypal', 2 );

        wp_update_user( array( 'ID' => 2, 'user_email' => 'recipient@mailinator.com' ) );

		$commissions = eddc_get_commissions( array( 'payment_id' => $_payment->ID ) );
        $_commission = eddc_get_commission( $commissions[0] );
        
        $payout = new EDD_Commissions_Payout;
        $payout->execute();

        var_dump( $payout->get_recipients() );
    }

}
