<?php
/**
 * Misc functions
 * 
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Easily readable time format
 *
 * @return string
 */
function edd_commissions_payouts_time_format() {
    return apply_filters( 'edd_commissions_payouts_time_format', 'l, F jS, Y \a\t h:ia' );
}


function edd_commissions_payouts_convert_utc_timestamp( $utc_timestamp ) {
    return $utc_timestamp + ( get_option( 'gmt_offset' ) * 3600 );
}