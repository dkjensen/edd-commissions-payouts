<?php
/**
 * Class PayoutsHelperTest
 *
 * @package EDD Commissions Payouts
 */

/**
 * EDD_Commissions_Payouts_Helper Tests
 */
class PayoutsHelperTest extends WP_UnitTestCase {

    public static function setUpBeforeClass() {
        edd_update_option( 'edd_commissions_payout_methods', array( 'paypal' => 'PayPal', 'stripe' => 'Stripe' ) );
    }

	/**
     * Test get array of available payout method class names
     *
     * @return void
     */
	public function test_get_payout_method_class_names() {
        $payout_methods = EDD_Commissions_Payouts()->helper->get_payout_methods();

        $this->assertTrue( is_array( $payout_methods ) );
        $this->assertInstanceOf( 'EDD_Commissions_Payouts_Method', current( $payout_methods ) );
    }
    
    /**
     * Test get user payout methods
     * 
     * @after unset_site_payout_methods
     *
     * @return void
     */
    public function test_get_payout_methods_user() {
        $user_id = $this->factory->user->create();

        $currently_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertEmpty( $currently_enabled );

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );

        $newly_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertEquals( sizeof( $newly_enabled ), 1 );
    }

    /**
     * Test add payout method to user
     *
     * @return void
     */
    public function test_add_payout_method_to_user() {
        $user_id = $this->factory->user->create();
        
        $response = EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );

        $this->assertInstanceOf( 'EDD_Commissions_Payouts_Method', $response );

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'stripe', $user_id );

        $currently_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertTrue( in_array( 'paypal', $currently_enabled ) );
        $this->assertTrue( in_array( 'stripe', $currently_enabled ) );
    }

    /**
     * Test adding payout method that is not enabled on the site
     * 
     * @return void
     */
    public function test_add_payout_method_to_user_not_enabled_site() {
        edd_update_option( 'edd_commissions_payout_methods', array() );

        try {
            $user_id = $this->factory->user->create();
            $response = EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
        }catch( Exception $e ) {
            $response = $e;
        }

        $this->assertInstanceOf( 'Exception', $response );

        self::setUpBeforeClass();
    }

    /**
     * Test adding payout method which is already enabled for the user
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_add_payout_method_to_user_already_exists() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
    }

    /**
     * Test adding payout method which class does not exist
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_add_payout_method_to_user_invalid_class_name() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'randomstring', $user_id );
    }

    /**
     * Test removing a valid payout method from user
     *
     * @return void
     */
    public function test_remove_payout_method_from_user() {
        $user_id = $this->factory->user->create();

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );

        $response = EDD_Commissions_Payouts()->helper->remove_user_payout_method( 'paypal', $user_id );

        $this->assertInstanceOf( 'EDD_Commissions_Payouts_Method', $response );

        $currently_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertEmpty( $currently_enabled );

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'stripe', $user_id );

        EDD_Commissions_Payouts()->helper->remove_user_payout_method( 'paypal', $user_id );

        $currently_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertTrue( in_array( 'stripe', $currently_enabled ) );
        $this->assertEquals( sizeof( $currently_enabled ), 1 );
    }

    /**
     * Test removing a payout method that exists in the usermeta but the object no longer exists
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_remove_invalid_payout_method_from_user() {
        $user_id = $this->factory->user->create();

        update_user_meta( $user_id, 'edd_enabled_payout_methods', array( 'randomstring' ) );

        EDD_Commissions_Payouts()->helper->remove_user_payout_method( 'randomstring', $user_id );
    }

    /**
     * Test removing a payout method from user which is not currently enabled
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_remove_payout_method_from_user_not_enabled() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->remove_user_payout_method( 'paypal', $user_id );
    }

    /**
     * Test setting a users preferred payout method that is enabled
     *
     * @return void
     */
    public function test_set_user_preferred_payout_method() {
        $user_id = $this->factory->user->create();

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
        $response = EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'paypal', $user_id );

        $this->assertInstanceOf( 'EDD_Commissions_Payouts_Method', $response );
    }

    /**
     * Test setting a users preferred payout method that is not enabled on the site
     *
     * @return void
     */
    public function test_set_user_preferred_payout_method_not_enabled_site() {
        edd_update_option( 'edd_commissions_payout_methods', array() );

        try {
            $user_id = $this->factory->user->create();

            EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
            $response = EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'paypal', $user_id );
        }catch( Exception $e ) {
            $response = $e;
        }

        $this->assertInstanceOf( 'Exception', $response );

        self::setupBeforeclass();
    }

    /**
     * Test setting a users preferred payout method that is not enabled for the user
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_set_user_preferred_payout_method_not_enabled() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'paypal', $user_id );
    }
    
    /**
     * Test setting a users preferred payout method that does not exist
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_set_user_preferred_payout_method_invalid() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'randomstring', $user_id );
    }

    /**
     * Test when removing a payout method which is preferred, the preferred method is automatically
     * toggled to the next enabled payout method
     *
     * @return void
     */
    public function test_set_user_preferred_payout_method_after_removing_current_preferred_method() {
        $user_id = $this->factory->user->create();

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );
        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'stripe', $user_id );
        
        EDD_Commissions_Payouts()->helper->set_user_preferred_payout_method( 'paypal', $user_id );

        $response = EDD_Commissions_Payouts()->helper->get_user_preferred_payout_method( $user_id );

        $this->assertEquals( 'paypal', $response );

        EDD_Commissions_Payouts()->helper->remove_user_payout_method( 'paypal', $user_id );

        $response = EDD_Commissions_Payouts()->helper->get_user_preferred_payout_method( $user_id );

        $this->assertEquals( 'stripe', $response );
    }

    public function test_set_user_preferred_payout_method_initial_enabling() {
        $user_id = $this->factory->user->create();

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'paypal', $user_id );

        $preferred_meta = get_user_meta( $user_id, 'edd_preferred_payout_method', true );

        $this->assertEquals( $preferred_meta, 'paypal' );

        EDD_Commissions_Payouts()->helper->enable_user_payout_method( 'stripe', $user_id );

        $preferred_meta = get_user_meta( $user_id, 'edd_preferred_payout_method', true );

        $this->assertEquals( $preferred_meta, 'paypal' );
    }

    /**
     * Test getting vendor dashboard page containing payout methods
     *
     * @return string
     */
    public function test_get_payout_methods_uri() {
        $uri = EDD_Commissions_Payouts()->helper->get_payout_methods_dashboard_uri();

        $this->assertEquals( $uri, home_url() );

        $vendor_dashboard = $this->factory->post->create_object( array( 
            'post_type'     => 'page',
            'post_content'  => '[vendor-dashboard]'
        ) );

        EDD_FES()->helper->set_option( 'fes-vendor-dashboard-page', $vendor_dashboard );

        $this->assertGreaterThan( 0, strpos( EDD_Commissions_Payouts()->helper->get_payout_methods_dashboard_uri(), 'page_id=' . $vendor_dashboard ) );
    }
}
