<?php
/**
 * Manage the payouts table view
 * 
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Payout_Table {

    public function __construct() {
        add_filter( 'manage_edd_payout_posts_columns', array( $this, 'payouts_columns' ) );
        add_action( 'manage_edd_payout_posts_custom_column', array( $this, 'payouts_custom_column' ), 10, 2 );
        add_filter( 'manage_edit-edd_payout_sortable_columns', array( $this, 'payouts_sortable_columns' ) );
        add_filter( 'post_row_actions', array( $this, 'payouts_row_actions' ), 10, 2 );
        add_filter( 'list_table_primary_column', array( $this, 'primary_column' ), 10, 2 );
    }


    /**
     * Custom payouts table columns
     *
     * @param array $columns
     * @return array
     */
    public function payouts_columns( $columns ) {
        $columns = array(
            'id'                => __( 'ID', 'edd-commissions-payouts' ),
            'txn_id'            => __( 'Transaction ID', 'edd-commissions-payouts' ),
            'payout_date'       => __( 'Date', 'edd-commissions-payouts' ),
            'amount'            => __( 'Amount', 'edd-commissions-payouts' ),
            'fees'              => __( 'Fees', 'edd-commissions-payouts' ),
            'recipients'        => __( 'Recipients', 'edd-commissions-payouts' ),
            'status'            => __( 'Status', 'edd-commissions-payouts' ),
            'notes'             => sprintf( '<span><span class="vers comment-grey-bubble" title="%1$s"><span class="screen-reader-text">%1$s</span></span></span>', __( 'Notes', 'edd-commissions-payouts' ) ),
        );

        return $columns;
    }


    /**
     * Override payouts table column data
     *
     * @param string $column
     * @param integer $post_id
     * @return void
     */
    public function payouts_custom_column( $column, $post_id ) {
        $post   = get_post( $post_id );
        $payout = new EDD_Commissions_Payout( $post_id );

        switch ( $column ) {
            case 'id' :
                printf( '<a href="%s">%s</a>', get_edit_post_link( $payout->get_id() ), $payout->get_id() );
                break;

            case 'txn_id' :
                printf( '<a href="%s">%s</a>', get_edit_post_link( $payout->get_id() ), $payout->get_txn_id() );
                break;

            case 'payout_date' :
                print $post->post_date;
                break;

            case 'amount' :
                print $payout->get_formatted_amount();
                break;

            case 'fees' :
                print $payout->get_formatted_fees();
                break;

            case 'recipients' :
                print sizeof( $payout->get_recipients() );
                break;

            case 'status' :
                $this->status_column( $payout->get_status() );
                break;

            case 'notes' :
                $this->notes_column( $payout->get_notes() );
                break;
        }
    }


    protected function status_column( $status ) {
        ?>

        <mark class="payout-status <?php print esc_attr( $status ); ?>">
            <span><?php esc_html_e( ucfirst( $status ), 'edd-commissions-payouts' ); ?></span>
        </mark>

        <?php
    }


    protected function notes_column( $notes ) {
        if ( ! $notes ) {
            ?>

            <a href="https://beatrice.local/wp-admin/edit-comments.php?p=1&amp;comment_status=approved" class="post-com-count post-com-count-approved">
                <span class="comment-count-approved" aria-hidden="true"><?php print sizeof( $notes ); ?></span>
                <span class="screen-reader-text"><?php printf( __( '%d Notes', 'edd-commissions' ), sizeof( $notes ) ); ?></span>
            </a>

            <?php
        }
    }


    /**
     * Change the allowed sortable columns
     *
     * @param array $sortable
     * @return array
     */
    public function payouts_sortable_columns( $sortable ) {
        $sortable = array(
            'id'            => 'id',
            'txn_id'        => 'txn_id',
            'payout_date'   => 'date',
            'amount'        => 'amount',
            'fees'          => 'fees',
            'recipients'    => 'recipients',
            'status'        => 'status',
        );

        return $sortable;
    }


    /**
     * Modify the row actions for the payouts table
     *
     * @param string $actions
     * @param WP_Post $post
     * @return string
     */
    public function payouts_row_actions( $actions, $post ) {
        if ( $post->post_type == 'edd_payout' ) {
            $actions = array(
                'view'      => sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post->ID ), __( 'View Details', 'edd-commissions-payouts' ) )
            );
        }

        return $actions;
    }


    /**
     * Move the row actions under the transaction ID column
     *
     * @param string $default
     * @param string $screen
     * @return string
     */
    public function primary_column( $default, $screen ) {
        if ( $screen == 'edit-edd_payout' ) {
            $default = 'txn_id';
        }

        return $default;
    }
}

return new EDD_Commissions_Payouts_Payout_Table;