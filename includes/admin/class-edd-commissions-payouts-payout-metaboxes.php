<?php
/**
 * Manage the single payout view meta boxes
 * 
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Payout_Metaboxes {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
    }


    /**
     * Register payout metaboxes
     *
     * @return void
     */
    public function register_metaboxes() {
        add_meta_box( 'payout_recipients', __( 'Recipients', 'edd-commissions-payouts' ), array( $this, 'metabox_recipients' ), 'edd_payout', 'normal' );
        add_meta_box( 'payout_notes', __( 'Payout Notes', 'edd-commissions-payouts' ), array( $this, 'metabox_notes' ), 'edd_payout', 'side' );
        add_meta_box( 'payout_errors', __( 'Payout Errors', 'edd-commissions-payouts' ), array( $this, 'metabox_errors' ), 'edd_payout', 'side' );
    }


    /**
     * Payout recipients metabox
     *
     * @return void
     */
    public function metabox_recipients() {
        global $post;

        $payout = new EDD_Commissions_Payout( $post->ID );

        $recipients = $payout->get_recipients();

        if ( $recipients ) : 
        ?>

        <table class="edd-commissions-payouts-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e( 'User', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Receiver', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Commission', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Paid', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Fee', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Currency', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Payout Method', 'edd-commissions-payouts' ); ?></th>
                    <th><?php _e( 'Status', 'edd-commissions-payouts' ); ?></th>
                </tr>
            </thead>
            <tbody>

            <?php 
            foreach ( $recipients as $user_id => $user_info ) : 
                
                $payout_status = ! empty( $user_info['payout_status'] ) ? $user_info['payout_status'] : __( 'Failed', 'edd-commissions-payouts' );
            ?>

                <tr>
                    <td><?php print esc_html( $user_id ); ?></td>
                    <td><?php print esc_html( $user_info['receiver'] ); ?></td>
                    <td><?php print esc_html( $user_info['payout_amount'] ); ?></td>
                    <td><?php print esc_html( $user_info['payout_paid'] ); ?></td>
                    <td><?php print esc_html( $user_info['payout_fees'] ); ?></td>
                    <td><?php print esc_html( $user_info['payout_currency'] ); ?></td>
                    <td><?php print esc_html( $user_info['payout_method'] ); ?></td>
                    <td><?php print esc_html( $payout_status ); ?></td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>

        <?php
        endif;
    }


    public function metabox_notes() {
        global $post;

        $payout = new EDD_Commissions_Payout( $post->ID );

        if ( $payout->has_notes() ) :
            foreach ( $payout->get_notes() as $note ) :
            ?>

            <div class="edd_payout_note"><?php print apply_filters( 'comment_text', $note ); ?></div>

            <?php
            endforeach;
        endif;
    }


    public function metabox_errors() {
        global $post;

        $payout = new EDD_Commissions_Payout( $post->ID );

        if ( $payout->has_errors() ) :
            foreach ( $payout->get_errors() as $error ) :
            ?>

            <div class="edd_payout_note payout_error"><?php print apply_filters( 'comment_text', $error ); ?></div>

            <?php
            endforeach;
        endif;
    }

}

return new EDD_Commissions_Payouts_Payout_Metaboxes;