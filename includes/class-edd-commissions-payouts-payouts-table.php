<?php
/**
 * EDD_Commissions_Payouts_Payouts_Table
 *
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EDD_Commissions_Payouts_Payouts_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 */
	public $per_page = 50;


	/**
	 * Base URL
	 *
	 * @var int
	 */
	public $base;


	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular' => 'payout',
			'plural'   => 'payouts',
			'ajax'     => false,
		) );

		$this->base = admin_url( 'edit.php?post_type=edd_payout&page=edd-payouts' );
	}


	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns
	 */
	public function get_columns() {
		$columns = array(
            'id'                => __( 'ID', 'edd-commissions-payouts' ),
            'txn_id'            => __( 'Transaction ID', 'edd-commissions-payouts' ),
            'date'              => __( 'Date', 'edd-commissions-payouts' ),
            'amount'            => __( 'Amount', 'edd-commissions-payouts' ),
            'fees'              => __( 'Fees', 'edd-commissions-payouts' ),
            'recipients'        => __( 'Recipients', 'edd-commissions-payouts' ),
            'status'            => __( 'Status', 'edd-commissions-payouts' ),
            'notes'             => sprintf( '<span><span class="vers comment-grey-bubble" title="%1$s"><span class="screen-reader-text">%1$s</span></span></span>', __( 'Notes', 'edd-commissions-payouts' ) ),
        );
        
		return $columns;
	}


	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array
	 * @param string
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
    }


	/**
	 * Retrieve the current page number

	 * @return int
	 */
	function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}


	/**
	 * Outputs the log views
     * 
	 * @return void
	 */
	public function bulk_actions( $which = '' ) {
		//edd_log_views();
	}


	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 */
	function prepare_items() {
		global $wpdb;

        $paged  = $this->get_paged();
        $offset = ( $this->per_page * ( $paged - 1 ) );

        $items = $wpdb->get_results( $wpdb->prepare( "
            SELECT SQL_CALC_FOUND_ROWS
                ID as id,
                MAX(CASE WHEN $wpdb->postmeta.meta_key = 'txn_id' THEN $wpdb->postmeta.meta_value ELSE NULL END) as txn_id,
                MAX(CASE WHEN $wpdb->postmeta.meta_key = 'amount' THEN $wpdb->postmeta.meta_value ELSE NULL END) as amount,
                MAX(CASE WHEN $wpdb->postmeta.meta_key = 'fees' THEN $wpdb->postmeta.meta_value ELSE NULL END) as fees,
                MAX(CASE WHEN $wpdb->postmeta.meta_key = 'status' THEN $wpdb->postmeta.meta_value ELSE NULL END) as status,
                MAX(CASE WHEN $wpdb->postmeta.meta_key = 'errors' THEN $wpdb->postmeta.meta_value ELSE NULL END) as notes,
                post_date as date
            FROM        $wpdb->posts
            LEFT JOIN   $wpdb->postmeta
            ON          $wpdb->posts.ID = $wpdb->postmeta.post_id
            WHERE       $wpdb->posts.post_type = 'edd_payout'
            AND         $wpdb->posts.post_status = 'publish'
            GROUP BY    $wpdb->posts.ID
            ORDER BY    $wpdb->posts.post_date DESC
            LIMIT       %d
            OFFSET      %d
        ", $this->per_page, $offset ), ARRAY_A );

        $this->items = $items;

        $found_rows = $wpdb->get_var( "SELECT FOUND_ROWS();" );

        /*
        $items = new WP_Query( array(
            'post_type'         => 'edd_payout',
            'posts_per_page'    => $this->per_page,
            'post_status'       => 'publish',
            'offset'            => isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) * $this->per_page : 0
        ) );

        foreach ( $items->posts as $item ) {
            $payout = new EDD_Commissions_Payout( $item->ID );

            $this->items[] = array(
                'id'            => $payout->get_id(),
                'txn_id'        => $payout->get_txn_id(),
                'date'          => $item->post_date,
                'amount'        => $payout->get_formatted_amount(),
                'fees'          => $payout->get_formatted_fees(),
                'recipients'    => sizeof( $payout->get_recipients() ),
                'status'        => $payout->get_status(),
                'notes'         => intval( $payout->has_errors() )
            );
        }
        */

		$this->set_pagination_args( array(
				'total_items'  => $found_rows,
				'per_page'     => $this->per_page,
				'total_pages'  => ceil( $found_rows / $this->per_page )
			)
        );
        
        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
	}
}
