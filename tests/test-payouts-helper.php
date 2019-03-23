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

	/**
     * Test get array of available payout method class names
     *
     * @return void
     */
	public function test_get_payout_method_class_names() {
        $payout_methods = EDD_Commissions_Payouts()->helper->get_payout_methods();

        $this->assertTrue( is_array( $payout_methods ) );
        $this->assertTrue( current( $payout_methods ) instanceof EDD_Commissions_Payouts_Method );
    }
    
    /**
     * Test get user payout methods
     *
     * @return void
     */
    public function test_get_payout_methods_user() {
        $user_id = $this->factory->user->create();

        $currently_enabled = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods( $user_id );

        $this->assertEmpty( $currently_enabled );

        EDD_Commissions_Payouts()->helper->add_user_payout_method( 'paypal', $user_id );

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
        
        $response = EDD_Commissions_Payouts()->helper->add_user_payout_method( 'paypal', $user_id );

        $this->assertInstanceOf( 'EDD_Commissions_Payouts_Method', $response );
    }

    /**
     * @expectedException Exception
     *
     * @return void
     */
    public function test_add_payout_method_to_user_already_exists() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->add_user_payout_method( 'paypal', $user_id );
        EDD_Commissions_Payouts()->helper->add_user_payout_method( 'paypal', $user_id );
    }

    /**
     * @expectedException Exception
     *
     * @return void
     */
    public function test_add_payout_method_to_user_invalid_class_name() {
        $user_id = $this->factory->user->create();
        EDD_Commissions_Payouts()->helper->add_user_payout_method( 'randomstring', $user_id );
    }

    /*
    public function test_add_payout_method() {
        $payout_method_count = sizeof( EDD_Commissions_Payouts()->helper->get_payout_methods() );

        var_dump( $payout_method_count );

        add_filter( 'edd_commissions_payout_methods', function( $payout_methods ) {
            $payout_methods[] = new class extends EDD_Commissions_Payouts_Method {

            };

            return $payout_methods;
        } );

        $this->assertEquals( $payout_method_count + 1, sizeof( EDD_Commissions_Payouts()->helper->get_payout_methods() ) );
    }
    */
}
