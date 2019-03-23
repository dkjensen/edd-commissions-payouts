<?php
/**
 * Plugin Name: Easy Digital Downloads - Commissions Payouts
 * Description: Easy Digital Downloads Commissions Payouts API integration for FES and Commissions
 * Version: 1.0.0
 * Author: David Jensen
 * Author URI: https://dkjensen.com
 * Text Domain: edd-commissions-payouts
 *
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wp_version;

define( 'EDD_COMMISSIONS_PAYOUTS_VER', '1.0.0' );
define( 'EDD_COMMISSIONS_PAYOUTS_PLUGIN_NAME', 'Easy Digital Downloads - Commissions Payouts' );
define( 'EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


if ( ! file_exists( EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    /**
     * Display admin notice if Composer packages not installed
     * 
     * @codeCoverageIgnore
     */
    add_action( 'admin_notices', function() {
        ?>

        <div class="notice notice-error is-dismissible">
            <p><?php printf( __( 'Error loading composer packages for %s', 'edd-commissions-payouts' ), EDD_COMMISSIONS_PAYOUTS_PLUGIN_NAME ); ?></p>
        </div>

        <?php
    } );

    return;
}

if ( ! class_exists( 'Easy_Digital_Downloads' ) || version_compare( EDD_VERSION, '2.4', '<' ) ) {
    /**
     * Display admin notice if not up to date with EDD version
     * 
     * @codeCoverageIgnore
     */
    add_action( 'admin_notices', function() {
        ?>
        
        <div class="notice notice-error is-dismissible">
            <p><?php printf( __( '<strong>Notice:</strong> %s requires Easy Digital Downloads 2.5 or higher in order to function properly.', 'edd-commissions-payouts' ), EDD_COMMISSIONS_PAYOUTS_PLUGIN_NAME ); ?></p>
        </div>
        
        <?php
    } );

    return;
}

if ( version_compare( $wp_version, '4.2', '<' ) ) {
    /**
     * Display admin notice if not up to date with WP version
     * 
     * @codeCoverageIgnore
     */
    add_action( 'admin_notices', function() {
        ?>
        
        <div class="notice notice-error is-dismissible">
            <p><?php printf( __( '<strong>Notice:</strong> %s requires WordPress 4.2 or higher in order to function properly.', 'edd-commissions-payouts' ), EDD_COMMISSIONS_PAYOUTS_PLUGIN_NAME ); ?></p>
        </div>
        
        <?php
    } );

    return;
}

// Load Composer
require EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'vendor/autoload.php';
require EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/class-edd-commissions-payouts.php';

require EDD_COMMISSIONS_PAYOUTS_PLUGIN_DIR . 'includes/misc-functions.php';

function EDD_Commissions_Payouts() {
    return EDD_Commissions_Payouts::instance();
}
EDD_Commissions_Payouts();