<?php 
/**
 * Template file for displaying FES dashboard page Payout Methods
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( EDD_FES()->integrations->is_commissions_active() ) : ?>

<div id="edd_user_payout_methods">   
    <h3><?php _e( 'Get Paid', 'edd-commissions-payouts' ); ?></h3>

    <p><?php _e( 'Select one of the payment methods below to begin receiving payouts.', 'edd-commissions-payouts' ); ?></p>

    
    <?php
    /**
     * Render the enabled payout method boxes
     */
    $payout_methods  = EDD_Commissions_Payouts()->helper->get_enabled_payout_methods();
    $enabled_methods = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods();
    
    if ( $payout_methods ) : ?>

    <div class="edd_payout_methods">

        <?php foreach ( $payout_methods as $id => $method ) : $enabled = in_array( $id, $enabled_methods ); ?>

        <div class="edd_payout_method <?php print esc_attr( $id ); ?>" data-name="<?php print esc_attr( $method->get_name() ); ?>">
            <div class="edd_payout_method_icon"><?php print $method->get_icon(); ?></div>
            <a href="<?php print $method->get_enable_uri(); ?>"><?php print ! $enabled ? __( 'Enable', 'edd-commissions-payouts' ) : __( 'Remove', 'edd-commissions-payouts' ); ?></a>
        </div>

        <?php endforeach; ?>

    </div>
    
    <?php else : ?>

    <p class="edd_no_payout_methods"><?php _e( 'No payout methods available.', 'edd-commissions-payouts' ); ?></p>

    <?php endif; ?>
</div>

<?php endif; ?>