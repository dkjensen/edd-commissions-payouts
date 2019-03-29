<?php
/**
 * Main EDD_Commissions_Payouts class file
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts {

    /**
	 * Plugin object
	 */
    private static $instance;

    /**
     * Basic setup methods
     *
     * @var EDD_Commissions_Payouts_Setup
     */
    public $setup;

    /**
     * Helper methods
     *
     * @var EDD_Commissions_Payouts_Helper
     */
    public $helper;

    /**
     * Payout schedule
     *
     * @var EDD_Commissions_Payouts_Schedule
     */
    public $schedule;

    /**
     * FES integration
     *
     * @var EDD_Commissions_Payouts_FES
     */
    public $fes;

    /**
     * EDD Commissions integration
     *
     * @var EDD_Commissions_Payouts_Commissions
     */
    public $commissions;
    
    /**
     * Form processing
     *
     * @var EDD_Commissions_Payouts_Form_Handler
     */
    public $form_handler;


    /**
     * Insures that only one instance of EDD_Commissions_Payouts exists in memory at any one time.
     * 
     * @return EDD_Commissions_Payouts The one true instance of EDD_Commissions_Payouts
     */
    public static function instance() {
        global $wp_version;

        if ( version_compare( $wp_version, '4.2', '<' ) ) {
            add_action( 'admin_notices', array( 'EDD_Commissions_Payouts', 'wp_notice' ) );
            return;
        } else

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Commissions_Payouts ) ) {
            self::$instance = new EDD_Commissions_Payouts;
            self::$instance->includes();

            do_action_ref_array( 'edd_commissions_payouts_loaded', self::$instance ); 

            self::$instance->setup          = new EDD_Commissions_Payouts_Setup;
            self::$instance->helper         = new EDD_Commissions_Payouts_Helper;
            self::$instance->schedule       = new EDD_Commissions_Payouts_Schedule;
            self::$instance->fes            = new EDD_Commissions_Payouts_FES;
            self::$instance->commissions    = new EDD_Commissions_Payouts_Commissions;
            self::$instance->form_handler   = new EDD_Commissions_Payouts_Form_Handler;
        }
        
        return self::$instance;
    }


    /**
     * Include the goodies
     *
     * @return void
     */
    public function includes() {
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payout.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-setup.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-helper.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-schedule.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-form-handler.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-fes.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-commissions.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts-log-table.php';

        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/abstracts/class-edd-commissions-payouts-method.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/methods/class-edd-commissions-payout-method-paypal.php';
        require_once EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/methods/class-edd-commissions-payout-method-stripe.php';
    }


    /**
     * Throw error on object clone
     *
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-commissions-payouts' ), '2.3' );
    }


    /**
     * Disable unserializing of the class
     * 
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-commissions-payouts' ), '2.3' );
    }

}