<?php
/**
 * PHPUnit bootstrap file
 *
 * @package EDD Commissions Payouts
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    if( file_exists( dirname( dirname( __FILE__ ) ) . '/../easy-digital-downloads/easy-digital-downloads.php' ) ) {
        require dirname( dirname( __FILE__ ) ) . '/../easy-digital-downloads/easy-digital-downloads.php';
    }

    if( file_exists( dirname( dirname( __FILE__ ) ) . '/../edd-fes/edd-fes.php' ) ) {
        require dirname( dirname( __FILE__ ) ) . '/../edd-fes/edd-fes.php';
    }

    if( file_exists( dirname( dirname( __FILE__ ) ) . '/../edd-commissions/edd-commissions.php' ) ) {
        require dirname( dirname( __FILE__ ) ) . '/../edd-commissions/edd-commissions.php';
    }

	require dirname( dirname( __FILE__ ) ) . '/edd-commissions-payouts.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );




// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

echo "Installing Easy Digital Downloads...\n";
activate_plugin( 'easy-digital-downloads/easy-digital-downloads.php' );

edd_run_install();

echo "Installing Commissions...\n";
edd_commissions_install();

/**
 * Helper classes
 */
require_once 'helpers/class-helper-payment.php';
require_once 'helpers/class-helper-download.php';