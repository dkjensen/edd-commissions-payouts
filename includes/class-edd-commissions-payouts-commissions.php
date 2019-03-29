<?php
/**
 * EDD Commissions integration class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Commissions {

    public function __construct() {
        add_filter( 'eddc_settings', array( $this, 'settings' ) );
        add_action( 'edd_edd_commissions_payout_schedule_status', array( $this, 'field_payout_schedule_status' ) );
        add_action( 'edd_edd_commissions_payout_schedule', array( $this, 'field_payout_schedule' ) );
    }

    /**
     * Registers our settings in Commissions tab
     *
     * @param array $settings array the existing plugin settings
     * @return array The new EDD settings array with our settings added
     */
    public function settings( $settings ) {
        $payout_method_objects = EDD_Commissions_Payouts()->helper->get_payout_methods();
        $payout_methods = array_filter( array_map( array( $this, 'convert_object_to_name' ), $payout_method_objects ) );

        $settings[] = array(
            'id'      => 'edd_commissions_payout_methods',
            'name'    => __( 'Enabled Payout Methods', 'edd-commissions-payouts' ),
            'type'    => 'multicheck',
            'options' => $payout_methods
        );

        $settings[] = array(
            'id'      => 'edd_commissions_payout_schedule_status',
            'name'    => __( 'Payout Schedule Status', 'edd-commissions-payouts' ),
            'type'    => 'hook',
        );

        $settings[] = array(
            'id'      => 'edd_commissions_payout_schedule',
            'name'    => __( 'Payout Schedule', 'edd-commissions-payouts' ),
            'type'    => 'hook',
        );

        foreach ( $payout_method_objects as $payout_method ) {
            if ( $payout_method->settings() ) {
                $settings = array_merge( $settings, $payout_method->settings() );
            }
        }

        return $settings;
    }


    public function field_payout_schedule_status() {
        $status      = EDD_Commissions_Payouts()->schedule->is_enabled();
        $toggle_url  = wp_nonce_url( admin_url(), 'toggle_payout_schedule_status' );
        $next_payout = edd_commissions_payouts_convert_utc_timestamp( EDD_Commissions_Payouts()->schedule->get_next_scheduled_payout() );

        if ( $status ) : ?>

        <p><a href="<?php print esc_url( add_query_arg( array( 'edd_toggle_payout_schedule_status' => 1, 'status' => 'disable' ), $toggle_url ) ); ?>" class="button button-secondary"><?php _e( 'Disable Scheduled Payouts', 'edd-commissions-payouts' ); ?></a></p>
        <p class="success-message">
            <strong><?php printf( __( 'Next payout scheduled for %s', 'edd-commissions-payouts' ), date_i18n( edd_commissions_payouts_time_format(), $next_payout ) ); ?></strong>
        </p>

        <?php else : ?>

        <p><a href="<?php print esc_url( add_query_arg( array( 'edd_toggle_payout_schedule_status' => 1, 'status' => 'enable' ), $toggle_url ) ); ?>" class="button button-primary"><?php _e( 'Enable Scheduled Payouts', 'edd-commissions-payouts' ); ?></a></p>

        <?php endif; ?>

        <?php
    }


    /**
     * Renders the payout schedule form and preview
     *
     * @return void
     */
    public function field_payout_schedule() {
        $mode       = EDD_Commissions_Payouts()->schedule->get_mode();
        $interval   = EDD_Commissions_Payouts()->schedule->get_interval();
        $repeats_on = EDD_Commissions_Payouts()->schedule->get_repeats_on();
        $hour       = EDD_Commissions_Payouts()->schedule->get_time_hour();
        $minute     = EDD_Commissions_Payouts()->schedule->get_time_min();

        if ( empty( $mode ) ) {
            $mode == 'weekly';
        }
        ?>

        <div id="edd-payout-schedule-form">
            <div class="edd-payout-schedule-field">
                <span class="edd-fixed-label"><?php _e( 'Mode', 'edd-commissions-payouts' ); ?></span>
                <div class="edd-payout-schedule-input">
                    <select id="edd_settings[edd_commissions_payout_schedule_mode]" name="edd_settings[edd_commissions_payout_schedule_mode]" class="regular-text">
                        <option value="daily" <?php selected( $mode, 'daily' ); ?>><?php _e( 'Daily', 'edd-commissions-payouts' ); ?></option>
                        <option value="weekly" <?php selected( $mode, 'weekly' ); ?>><?php _e( 'Weekly', 'edd-commissions-payouts' ); ?></option>
                        <option value="monthly" <?php selected( $mode, 'monthly' ); ?>><?php _e( 'Monthly', 'edd-commissions-payouts' ); ?></option>
                        <option value="yearly" <?php selected( $mode, 'yearly' ); ?>><?php _e( 'Yearly', 'edd-commissions-payouts' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="edd-payout-schedule-field">
                <span class="edd-fixed-label"><?php _e( 'Interval', 'edd-commissions-payouts' ); ?></span> 
                <div class="edd-payout-schedule-input">
                    <input type="text" class="regular-text" id="edd_settings[edd_commissions_payout_schedule_interval]" name="edd_settings[edd_commissions_payout_schedule_interval]" value="<?php print $interval ? esc_attr( $interval ) : ''; ?>" step="1" min="1" /> 
                </div>
            </div>
            <div class="edd-payout-schedule-field">
                <span class="edd-fixed-label"><?php _e( 'On', 'edd-commissions-payouts' ); ?></span>
                <div class="edd-payout-schedule-input">
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'su', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][su]" value="su" />
                        <?php _e( 'Sunday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'mo', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][mo]" value="mo" />
                        <?php _e( 'Monday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'tu', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][tu]" value="tu" />
                        <?php _e( 'Tuesday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'we', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][we]" value="we" />
                        <?php _e( 'Wednesday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'th', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][th]" value="th" />
                        <?php _e( 'Thursday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'fr', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][fr]" value="fr" />
                        <?php _e( 'Friday', 'edd-commissions-payouts' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" <?php checked( true, in_array( 'sa', $repeats_on ) ); ?> name="edd_settings[edd_commissions_payout_schedule_on][]" id="edd_settings[edd_commissions_payout_schedule_on][sa]" value="sa" />
                        <?php _e( 'Saturday', 'edd-commissions-payouts' ); ?>
                    </label>
                </div>
            </div>
            <div class="edd-payout-schedule-field">
                <span class="edd-fixed-label"><?php _e( 'Time', 'edd-commissions-payouts' ); ?></span>
                <div class="edd-payout-schedule-input">
                    <input type="text" maxlength="2" class="tiny-text" id="edd_settings[edd_commissions_payout_schedule_time_hr]" name="edd_settings[edd_commissions_payout_schedule_time_hr]" value="<?php print esc_attr( $hour ); ?>" /> :
                    <input type="text" maxlength="2" class="tiny-text" id="edd_settings[edd_commissions_payout_schedule_time_min]" name="edd_settings[edd_commissions_payout_schedule_time_min]" value="<?php print esc_attr( $minute ); ?>" />
                    <span class="description"><?php _e( '24 hour format', 'edd-commissions-payouts' ); ?></span>
                </div>
            </div>
            <p>&nbsp;</p>
            <p class="description" id="edd-next-payout"></p>
        </div>

        <?php
    }


    /**
     * Retrieves name of payout method from object
     *
     * @param object $class Object that inherits EDD_Commissions_Payouts_Method
     * @return mixed
     */
    public function convert_object_to_name( $class ) {
        if( $class instanceof EDD_Commissions_Payouts_Method ) {
            return $class->get_name();
        }

        return null;
    }


    public function get_unpaid_commissions() {
        if ( function_exists( 'eddc_get_unpaid_commissions' ) ) {
            $commissions = eddc_get_unpaid_commissions( array( 
                'query_args'     => array(
                    'date_query' => array(
                        'after'       => array(
                            'year'    => $from[2],
                            'month'   => $from[0],
                            'day'     => $from[1],
                        ),
                        'before'      => array(
                            'year'    => $to[2],
                            'month'   => $to[0],
                            'day'     => $to[1],
                        ),
                        'inclusive' => true
                    )
                )
            ) );

            if ( $commissions ) {
                $payouts = array();

                foreach ( $commissions as $commission ) {

                    $user          = get_userdata( $commission->user_id );
                    $custom_paypal = get_user_meta( $commission->user_id, 'eddc_user_paypal', true );
                    $email         = is_email( $custom_paypal ) ? $custom_paypal : $user->user_email;
                    $key           = md5( $email . $commission->currency );

                    if ( array_key_exists( $key, $payouts ) ) {
                        $payouts[ $key ]['amount'] += $commission->amount;
                        $payouts[ $key ]['ids'][]   = $commission->id;
                    } else {
                        $payouts[ $key ] = array(
                            'email'      => $email,
                            'amount'     => $commission->amount,
                            'currency'   => $commission->currency,
                            'ids'        => array( $commission->id ),
                            'user_id'    => $commission->user_id,
                        );
                    }
                }

                return $payouts;
            }
        }else {
            throw new Exception( __( 'Please confirm the Commissions add-on for Easy Digital Downloads is active.', 'edd-commissions-payouts' ) );
        }
    }
}