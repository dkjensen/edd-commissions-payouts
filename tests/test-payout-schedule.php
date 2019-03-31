<?php
/**
 * Class PayoutScheduleTest
 *
 * @package EDD Commissions Payouts
 */

/**
 * EDD_Commissions_Payouts_Helper Tests
 */
class PayoutScheduleTest extends WP_UnitTestCase {


    public static function setUpBeforeClass() {
        update_option( 'timezone_string', 'UTC' );
    }


    /**
     * Test enabling / disabling the payout schedule
     *
     * @return void
     */
    public function test_is_enabled() {
        $this->assertFalse( EDD_Commissions_Payouts()->schedule->is_enabled() );

        EDD_Commissions_Payouts()->schedule->enable( false );

        $this->assertTrue( EDD_Commissions_Payouts()->schedule->is_enabled() );

        EDD_Commissions_Payouts()->schedule->disable();

        $this->assertFalse( EDD_Commissions_Payouts()->schedule->is_enabled() );
    }

    /**
     * Test creating a payout schedule
     * 
     * @return void
     */
    public function test_schedule_payouts() {
        edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array( 'su', 'mo' ) );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '10' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '00' );

        EDD_Commissions_Payouts()->schedule->enable( false );

        $this->assertTrue( EDD_Commissions_Payouts()->schedule->schedule_payouts() );
    }

    /**
     * Test the calculate payout schedule method
     *
     * @return void
     */
    public function test_calculate_payout_schedule() {
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( '\When\When', $schedule );

        /**
         * Test default (empty) payout schedule settings
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( '', '', array(), '', '' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid mode
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'randomstring', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid interval
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '0', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid days of week
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'randomstring' ), '10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid days of week 2
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', 'randomstring', '10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time hour
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '24', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time hour 2
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), 'a', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time hour 3
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '123', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time hour 4
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '0', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '-10', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time minute
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '0', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '60' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time minute 2
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '0', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '-00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Invalid time minute 3
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '0', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '+1' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( 'Exception', $schedule );

        /**
         * Valid schedules
         */
        try {
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'weekly', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'monthly', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'yearly', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '10', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '100', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'sa' ), '10', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'sa' ), '12', '00' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '23', '59' );
            $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), '00', '00' );
        }catch( Exception $e ) {
            $schedule = $e;
        }
        $this->assertInstanceOf( '\When\When', $schedule );

        /**
         * Test if attempting to trigger payout today, it does not generate a payout time for today which has already passed
         */
        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), 0, 0 );
        $schedule->generateOccurrences();

        $tomorrow = new DateTime;
        $tomorrow->setTime( 0, 0 );
        $tomorrow->modify( '+1 day' );

        $this->assertEquals( $tomorrow, current( $schedule->occurrences ) );

        

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), 0, 0 );
        $schedule->generateOccurrences();

        $tomorrow = new DateTime;
        $tomorrow->setTime( 0, 0 );
        $tomorrow->modify( '+1 day' );

        $this->assertEquals( $tomorrow, current( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), 3, 33, new DateTime( '1997-09-01 03:22:00' ) );
        $schedule->generateOccurrences();

        $this->assertEquals( new DateTime( '1997-09-01 3:33:00' ), current( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'mo' ), 19, 15, new DateTime( '1997-09-01 16:55:00' ) );
        $schedule->generateOccurrences();

        $this->assertEquals( new DateTime( '1997-09-01 19:15:00' ), current( $schedule->occurrences ) );

        /**
         * Test payout will trigger today if set to a time later in the day
         */
        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), 23, 59 );
        $schedule->generateOccurrences();

        $today = new DateTime;
        $today->setTime( 23, 59 );

        $this->assertEquals( $today, current( $schedule->occurrences ) );

        /**
         * Test payout will start tomorrow if time is set to the current time
         */
        $current_time = time();

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), date( 'G', $current_time ), date( 'i', $current_time ) );
        $schedule->generateOccurrences();

        $tomorrow = new DateTime;
        $tomorrow->setTime( date( 'G', $current_time ), date( 'i', $current_time ) );
        $tomorrow->modify( '+1 day' );

        $this->assertEquals( $tomorrow, current( $schedule->occurrences ) );


        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), date( 'G', $current_time ), date( 'i', $current_time ) );
        $schedule->generateOccurrences();

        $tomorrow = new DateTime;
        $tomorrow->setTime( date( 'G', $current_time ), date( 'i', $current_time ) );
        $tomorrow->modify( '+1 day' );

        $this->assertEquals( $tomorrow, current( $schedule->occurrences ) );

        $tomorrow->modify( '+1 day' );

        $this->assertEquals( $tomorrow, next( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su' ), 12, 00 );
        $schedule->generateOccurrences();

        $day = new DateTime( 'next sunday' );
        $day->setTime( 12, 00 );

        $this->assertEquals( $day, current( $schedule->occurrences ) );

        $day->modify( '+7 days' );

        $this->assertEquals( $day, next( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'weekly', '2', array( 'su', 'tu' ), 12, 00, new DateTime( '1997-09-01 09:00:00' ) );
        $schedule->generateOccurrences();

        $this->assertEquals( new DateTime( '1997-09-02 12:00:00' ), current( $schedule->occurrences ) );
        $this->assertEquals( new DateTime( '1997-09-07 12:00:00' ), next( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'tu' ), 12, 00, new DateTime( '1997-09-01 09:00:00' ) );
        $schedule->generateOccurrences();

        $this->assertEquals( new DateTime( '1997-09-02 12:00:00' ), current( $schedule->occurrences ) );
        $this->assertEquals( new DateTime( '1997-09-09 12:00:00' ), next( $schedule->occurrences ) );
        $this->assertEquals( new DateTime( '1997-09-16 12:00:00' ), next( $schedule->occurrences ) );

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'weekly', '1', array( 'mo' ), 12, 00, new DateTime( '1997-09-01 09:00:00' ) );
        $schedule->generateOccurrences();

        $this->assertEquals( new DateTime( '1997-09-01 12:00:00' ), current( $schedule->occurrences ) );

        
        update_option( 'timezone_string', 'America/Los_Angeles' );

        $hour = 14;
        $min = 37;

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), $hour, $min );
        $schedule->generateOccurrences();

        $date = new DateTime( null, new DateTimeZone( get_option( 'timezone_string' ) ) );

        if ( $date->format( 'G' ) > $hour || ( $date->format( 'G' ) == $hour && $date->format( 'i' ) >= $min ) ) {
            $date->modify( '+1 day' );
        }
        
        $date->setTime( $hour, $min );

        $this->assertEquals( $date, current( $schedule->occurrences ) );

        $hour = 1;
        $min = 3;

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), $hour, $min );
        $schedule->generateOccurrences();

        $date = new DateTime( null, new DateTimeZone( get_option( 'timezone_string' ) ) );

        if ( $date->format( 'G' ) > $hour || ( $date->format( 'G' ) == $hour && $date->format( 'i' ) >= $min ) ) {
            $date->modify( '+1 day' );
        }
        
        $date->setTime( $hour, $min );

        $this->assertEquals( $date, current( $schedule->occurrences ) );

        update_option( 'timezone_string', 'America/New_York' );

        $hour = 20;
        $min = 59;

        $schedule = EDD_Commissions_Payouts()->schedule->calculate_payout_schedule( 'daily', '1', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ), $hour, $min );
        $schedule->generateOccurrences();

        $date = new DateTime( null, new DateTimeZone( get_option( 'timezone_string' ) ) );

        if ( $date->format( 'G' ) > $hour || ( $date->format( 'G' ) == $hour && $date->format( 'i' ) >= $min ) ) {
            $date->modify( '+1 day' );
        }
        
        $date->setTime( $hour, $min );

        $this->assertEquals( $date, current( $schedule->occurrences ) );
    }


    /**
     * Test daily payout schedule
     *
     * @return void
     */
    public function test_payout_schedule_daily() {
        update_option( 'timezone_string', 'America/New_York' );

        edd_update_option( 'edd_commissions_payout_schedule_mode', 'daily' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ) );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '20' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '05' );

        EDD_Commissions_Payouts()->schedule->enable();

        $payout_count = apply_filters( 'edd_commissions_number_scheduled_payouts', 10 );

        $date = new DateTime( null, new DateTimeZone( get_option( 'timezone_string' ) ) );

        if ( $date->format( 'G' ) < 20 || ( $date->format( 'G' ) == 20 && $date->format( 'i' ) < 05 ) ) {
            $date->modify( '-1 day' );
        }

        $schedule = EDD_Commissions_Payouts()->schedule->get_payout_schedule();

        for( $i = 0; $i < $payout_count; $i++ ) {
            $date->modify( '+1 day' );
            $date->setTime( 20, 05, 00 );

            $this->assertEquals( $date->getTimestamp(), $schedule [ $i ] );
        }
    }


    /**
     * Test weekly payout schedule
     *
     * @return void
     */
    public function test_payout_schedule_weekly() {
        edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array( 'mo' ) );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '10' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '00' );

        EDD_Commissions_Payouts()->schedule->enable();

        $payout_count = apply_filters( 'edd_commissions_number_scheduled_payouts', 10 );

        $date = new DateTime( null, new DateTimeZone( get_option( 'timezone_string' ) ) );

        $schedule = EDD_Commissions_Payouts()->schedule->get_payout_schedule();

        for( $i = 0; $i < $payout_count; $i++ ) {
            $date->modify( 'next monday' );
            $date->setTime( 10, 00, 00 );

            $this->assertEquals( $date->getTimestamp(), $schedule [ $i ] );
        }
    }


    /**
     * Test various scenarios of invalid payout schedule settings
     * 
     * @return void
     */
    public function test_schedule_payouts_invalid_settings() {
        EDD_Commissions_Payouts()->schedule->enable( false );

        // Start with valid settings
        edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array( 'su', 'mo' ) );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '10' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '00' );

        $this->assertTrue( EDD_Commissions_Payouts()->schedule->schedule_payouts() );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_mode', 'randomstring' );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
            edd_update_option( 'edd_commissions_payout_schedule_interval', 'randomstring' );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
            edd_update_option( 'edd_commissions_payout_schedule_interval', '0' );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
            edd_update_option( 'edd_commissions_payout_schedule_on', 'randomstring' );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_on', array( 'randomstring', 'su' ) );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );

        try {
            edd_update_option( 'edd_commissions_payout_schedule_on', array( 'su', 'mo' ) );
            edd_update_option( 'edd_commissions_payout_schedule_time_hr', '25' );

            $schedule = EDD_Commissions_Payouts()->schedule->schedule_payouts();
        }catch( Exception $e ) {
            $schedule = $e;
        }

        $this->assertInstanceOf( 'Exception', $schedule );
    }

    /**
     * Test removing a payout method from user which is not currently enabled
     * 
     * @expectedException Exception
     *
     * @return void
     */
    public function test_schedule_payouts_not_enabled() {
        EDD_Commissions_Payouts()->schedule->disable();

        EDD_Commissions_Payouts()->schedule->schedule_payouts();
    }

    /**
     * Test getting the next scheduled payout
     *
     * @return void
     */
    public function test_get_next_scheduled_payout() {
        edd_update_option( 'edd_commissions_payout_schedule_mode', '' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array() );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '' );

        $this->assertFalse( EDD_Commissions_Payouts()->schedule->get_next_scheduled_payout() );

        edd_update_option( 'edd_commissions_payout_schedule_mode', 'weekly' );
        edd_update_option( 'edd_commissions_payout_schedule_interval', '1' );
        edd_update_option( 'edd_commissions_payout_schedule_on', array( 'su', 'mo' ) );
        edd_update_option( 'edd_commissions_payout_schedule_time_hr', '10' );
        edd_update_option( 'edd_commissions_payout_schedule_time_min', '00' );

        $this->assertFalse( EDD_Commissions_Payouts()->schedule->get_next_scheduled_payout() );

        EDD_Commissions_Payouts()->schedule->enable( false );

        $this->assertFalse( EDD_Commissions_Payouts()->schedule->get_next_scheduled_payout() );

        EDD_Commissions_Payouts()->schedule->schedule_payouts();

        $this->assertGreaterThan( time(), EDD_Commissions_Payouts()->schedule->get_next_scheduled_payout() );
    }


    public function test_convert_utc_timestamp_to_local_wp() {
        update_option( 'timezone_string', 'America/New_York' );

        $local_time = edd_commissions_payouts_convert_utc_timestamp( '1553972993' );

        $this->assertEquals( '1553958593', $local_time );

        update_option( 'timezone_string', 'America/Los_Angeles' );

        $local_time = edd_commissions_payouts_convert_utc_timestamp( '1553972993' );

        $this->assertEquals( '1553947793', $local_time );
    }
}
