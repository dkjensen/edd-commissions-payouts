<?php
/**
 * Class PayoutsMethodPayPalTest
 *
 * @package EDD Commissions Payouts
 */

/**
 * EDD_Commissions_Payouts_Helper Tests
 */
class PayoutsMethodPayPalTest extends WP_UnitTestCase {


    public static function setUpBeforeClass() {
        edd_update_option( 'edd_commissions_calc_base', '50' );
    }


    public function setUp() {
        $vendor_id   = $this->factory->user->create();
        $customer_id = $this->factory->user->create();
		$post_id     = $this->factory->post->create( array( 'post_title' => 'Test Download', 'post_type' => 'download', 'post_status' => 'publish', 'post_author' => $vendor_id ) );

        update_post_meta( $post_id, 'edd_price', '10.50' );
        update_post_meta( $post_id, '_edd_price_options_mode', 'on' );
        update_post_meta( $post_id, '_edd_product_type', 'default' );
        update_post_meta( $post_id, '_edd_commisions_enabled', 1 );
        update_post_meta( $post_id, '_edd_commission_settings', array( 'user_id' => $vendor_id ) );

        EDD_FES()->vendors->make_user_vendor( $vendor_id );

        $purchase_data = array(
			'price'             => '10.50',
			'date'              => date( 'Y-m-d H:i:s', time() ),
			'purchase_key'      => strtolower( md5( uniqid() ) ),
			'user_email'        => 'testadmin@domain.com',
			'user_info'         => array( 
                'id' => $customer_id, 
                'email' => 'testadmin@domain.com', 
                'first_name' => 'John',
                'last_name' => 'Doe',
                'discount' => 'none'
            ),
			'currency'          => 'USD',
			'downloads'         => array( array( 'id' => $post_id, 'options' => array( 'price_id' => 1 ) ) ),
			'cart_details'      => array(),
			'status'            => 'pending',
			'tax'               => '0.00'
		);

        $payment_id = edd_insert_payment( $purchase_data );

        edd_update_payment_status( $payment_id, 'complete' );
	}


    public function test_get_access_token() {
        $paypal = EDD_Commissions_Payouts()->helper->get_payout_method( 'paypal' );

        $access_token = $paypal->get_access_token();

        $this->assertTrue( is_string( $access_token ) );
    }


    /**
     * Test getting access token with invalid credentials

     * @return void
     */
    public function test_get_access_token_invalid_credentials() {
        $paypal = EDD_Commissions_Payouts()->helper->get_payout_method( 'paypal' );

        $client_id = $paypal->get_client_id();
        $secret    = $paypal->get_secret();

        putenv( 'EDD_COMMISSIONS_PAYOUT_PAYPAL_CLIENT_ID_SANDBOX=randomstring' );
        putenv( 'EDD_COMMISSIONS_PAYOUT_PAYPAL_SECRET_SANDBOX=randomstring' );

        try {
            $access_token = $paypal->get_access_token();
        }catch( Exception $e ) {
            $access_token = $e;
        }

        $this->assertInstanceOf( 'Exception', $access_token );

        putenv( 'EDD_COMMISSIONS_PAYOUT_PAYPAL_CLIENT_ID_SANDBOX=' . $client_id );
        putenv( 'EDD_COMMISSIONS_PAYOUT_PAYPAL_SECRET_SANDBOX=' . $secret );
    }


    /**
     * Test getting payout data
     * 
     * @return void
     */
    /*
    public function test_get_payout_data() {
        $paypal = EDD_Commissions_Payouts()->helper->get_payout_method( 'paypal' );

        $data = $paypal->get_payout_data();
    }
    */
    

    /**
     * Test process batch payout
     * 
     * @return void
     */
    public function test_process_batch_payout() {
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

		$commissions = eddc_get_commissions( array( 'payment_id' => $_payment->ID ) );
		$_commission = eddc_get_commission( $commissions[0] );

        $paypal = EDD_Commissions_Payouts()->helper->get_payout_method( 'paypal' );

        $payout = $paypal->process_batch_payout();

        var_dump( $payout );
    }
}
