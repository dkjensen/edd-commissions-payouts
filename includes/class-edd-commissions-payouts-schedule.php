<?php
/**
 * EDD Payouts schedule
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Schedule {


    /**
     * Is the payout schedule enabled?
     *
     * @return boolean
     */
    public function is_enabled() {
        $enabled = get_option( 'edd_commissions_payout_schedule_enabled', '' );

        if ( 'enabled' === $enabled ) {
            return true;
        }

        return false;
    }


    /**
     * Enable the payout schedule
     *
     * @param boolean $schedule Whether to schedule the events in cron
     * @return boolean
     */
    public function enable( $schedule = true ) {
        update_option( 'edd_commissions_payout_schedule_enabled', 'enabled' );

        if ( $schedule ) {
            $this->schedule_payouts();
        }

        return true;
    }


    /**
     * Disable and clear the payout schedule
     *
     * @return boolean
     */
    public function disable() {
        wp_clear_scheduled_hook( 'edd_commissions_payout' );

        update_option( 'edd_commissions_payout_schedule_enabled', '' );

        return true;
    }


    /**
     * Schedules the payouts
     * 
     * Will clear the existing payout schedule if it is already set
     *
     * @return void
     */
    public function schedule_payouts() {
        if ( $this->is_enabled() ) {
            if ( wp_next_scheduled( 'edd_commissions_payout' ) ) {
                wp_clear_scheduled_hook( 'edd_commissions_payout' );
            }

            $occurrences = $this->get_payout_occurrences( apply_filters( 'edd_commissions_number_scheduled_payouts', 10 ) );

            if ( sizeof( $occurrences ) ) {
                foreach ( $occurrences as $occurrence ) {
                    wp_schedule_single_event( $occurrence->getTimestamp(), 'edd_commissions_payout' );
                }

                return true;
            }else {
                throw new Exception( __( 'No payouts have been scheduled due to the payout schedule settings not allowing for any occurrences to take place.', 'edd-commissions-payouts' ) );
            }
        }else {
            throw new Exception( __( 'Please enable the payout schedule under the Easy Digital Downloads - Commissions add-on settings page.', 'edd-commissions-payouts' ) );
        }
    }


    /**
     * Returns a \When\When object used to calculate the payout schedule
     * 
     * @see https://github.com/tplaner/When
     *
     * @param string $mode
     * @param integer $interval
     * @param array $repeats_on
     * @param string $hour
     * @param string $min
     * @param DateTime $start
     * @return When \When\When object
     */
    public function calculate_payout_schedule( $mode, $interval, $repeats_on = array(), $hour = '00', $min = '00', $start = null ) {
        try {
            if ( ! in_array( $mode, array( 'daily', 'weekly', 'monthly', 'yearly' ) ) ) {
                throw new Exception( __( 'Scheduled payout mode is invalid.', 'edd-commissions-payouts' ) );
            }

            if ( ! is_array( $repeats_on ) || array_diff( $repeats_on, array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' ) ) ) {
                throw new Exception( __( 'Days scheduled payouts can occur on is invalid.', 'edd-commissions-payouts' ) );
            }

            if ( ! ctype_digit( (string) $interval ) || $interval < 1 ) {
                throw new Exception( __( 'Scheduled payout interval must be a number greater than 0.', 'edd-commissions-payouts' ) );
            }

            if ( ! ctype_digit( (string) $hour ) || ! ctype_digit( (string) $min ) || strlen( $hour ) > 2 || strlen( $min ) > 2 || $hour > 23 || $min > 59 ) {
                throw new Exception( __( 'Scheduled payout time must be in a valid 24 hour format.', 'edd-commissions-payouts' ) );
            }

            if ( ! $start instanceof DateTime ) {
                $start = new DateTime;
            }

            /**
             * If the set payout time is past the current time, set earliest start date to tomorrow
             */
            if ( $start->format( 'G' ) > $hour || ( $start->format( 'G' ) == $hour && $start->format( 'i' ) >= $min ) ) {
                $start->modify( '+1 day' );
            }

            $start->setTime( $hour, $min );

            $repeats_on_rrule = ! empty( $repeats_on ) ? ";BYDAY=" . strtoupper( implode( ',', $repeats_on ) ) : '';

            $when = new \When\When;
            $when->RFC5545_COMPLIANT = \When\When::IGNORE;

            $rrule = apply_filters( 'edd_commissions_payout_payout_schedule_rrule', sprintf( 'FREQ=%s;INTERVAL=%s%s', strtoupper( $mode ), absint( $interval ), $repeats_on_rrule ), $mode, $interval, $repeats_on, $hour, $min );

            return $when->startDate( $start )->rrule( $rrule );

        }catch( Exception $e ) {
            throw new Exception( $e->getMessage() );
        }
    }


    /**
     * Returns an array of DateTime objects representing the payout schedule
     *
     * @param integer $count
     * @return array
     */
    public function get_payout_occurrences( $count = 10 ) {
        $when = $this->calculate_payout_schedule( $this->get_mode(), $this->get_interval(), $this->get_repeats_on(), $this->get_time_hour(), $this->get_time_min() );
        
        $when->count( $count )
             ->generateOccurrences();

        return $when->occurrences;
    }


    /**
     * Returns timestamp of next scheduled payout
     *
     * @return string
     */
    public function get_next_scheduled_payout() {
        return wp_next_scheduled( 'edd_commissions_payout' );
    }


    /**
     * Returns the scheduled payout mode
     *
     * @return string
     */
    public function get_mode() {
        return edd_get_option( 'edd_commissions_payout_schedule_mode', '' );
    }


    /**
     * Returns the scheduled payout interval 
     *
     * @return integer
     */
    public function get_interval() {
        return absint( edd_get_option( 'edd_commissions_payout_schedule_interval', '' ) );
    }


    /**
     * Returns the days of week scheduled payouts can occur on
     *
     * @return array
     */
    public function get_repeats_on() {
        return (array) edd_get_option( 'edd_commissions_payout_schedule_on', array() );
    }


    /**
     * Returns the scheduled payout hour time
     *
     * @return string
     */
    public function get_time_hour() {
        return sprintf( '%02d', (int) edd_get_option( 'edd_commissions_payout_schedule_time_hr', '' ) );
    }


    /**
     * Returns the scheduled payout minute time
     *
     * @return string
     */
    public function get_time_min() {
        return sprintf( '%02d', (int) edd_get_option( 'edd_commissions_payout_schedule_time_min', '' ) );
    }
    
}