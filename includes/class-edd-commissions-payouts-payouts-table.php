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
	public $per_page = 15;


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
            'amount'            => __( 'Amount', 'edd-commissions-payouts' ),
            'recipients'        => __( 'Recipients', 'edd-commissions-payouts' ),
			'date'              => __( 'Date', 'edd-commissions-payouts' ),
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
		global $edd_logs;

        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

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
                'amount'        => $payout->get_formatted_amount(),
                'date'          => $item->post_date
            );
        }

		$this->set_pagination_args( array(
				'total_items'  => $items->found_posts,
				'per_page'     => $this->per_page,
				'total_pages'  => ceil( $items->found_posts / $this->per_page )
			)
		);
	}
}
