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

    <?php do_action( 'edd_dashboard_earnings_before_payout_methods' ); ?>

    <div id="edd_user_payout_methods">
        <h3><?php _e( 'Get Paid', 'edd-commissions-payouts' ); ?></h3>

        <?php
        /**
         * Render the enabled payout method boxes
         */
        $payout_methods   = EDD_Commissions_Payouts()->helper->get_enabled_payout_methods();
        $enabled_methods  = EDD_Commissions_Payouts()->helper->get_user_enabled_payout_methods();
        $preferred_method = EDD_Commissions_Payouts()->helper->get_user_preferred_payout_method();

        if ( $payout_methods ) : ?>

        <table class="edd_payout_methods">
            <tbody>

                <?php foreach ( $payout_methods as $id => $method ) : $enabled = in_array( $id, $enabled_methods ); ?>

                <tr class="edd_payout_method <?php print esc_attr( $id ); ?>" data-name="<?php print esc_attr( $method->get_name() ); ?>">
                    <td>
                        <div class="edd_payout_method_icon"><?php print $method->get_icon(); ?></div>
                    </td>
                    <td>
                        <?php if ( $enabled ) : ?>
                            <?php if ( $id == $preferred_method ) : ?>

                            <div class="edd_payout_method_preferred"><?php _e( 'Preferred', 'edd-commissions-payouts' ); ?></div>

                            <?php else : ?>

                            <div class="edd_payout_set_method_preferred">
                                <a href="<?php print $method->get_set_as_preferred_uri(); ?>" class="edd_preferred_payout_method_action" 
                                    data-payout-method="<?php print esc_attr( $id ); ?>"
                                >
                                    <?php _e( 'Set as preferred', 'edd-commissions-payouts' ); ?>
                                </a>
                            </div>

                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php print $method->get_toggle_status_uri(); ?>" class="edd_payout_method_action" 
                            data-payout-action="<?php print ! $enabled ? 'enable' : 'remove'; ?>" 
                            data-payout-method="<?php print esc_attr( $id ); ?>"
                        >
                            <?php print ! $enabled ? __( 'Enable', 'edd-commissions-payouts' ) : __( 'Remove', 'edd-commissions-payouts' ); ?>
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>
           
            </tbody>
        </table>
        
        <?php else : ?>

        <p class="edd_no_payout_methods"><?php _e( 'No payout methods available.', 'edd-commissions-payouts' ); ?></p>

        <?php endif; ?>
    </div>

<?php endif; ?>