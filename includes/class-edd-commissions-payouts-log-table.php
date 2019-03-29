<?php
/**
 * EDD_RP_Logs_Table Class
 *
 * Renders the file downloads log view
 *
 * @since 1.2.6
 */

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EDD_Commissions_Payouts_Log_Table extends WP_List_Table {

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

		$this->base = admin_url( 'edit.php?post_type=download&page=edd-reports&tab=logs&view=payouts' );
	}


	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns
	 */
	public function get_columns() {
		$columns = array(
			'id'                => __( 'ID', 'edd-commissions-payouts' ),
			'type'              => __( 'Type', 'edd-commissions-payouts' ),
            'message'           => __( 'Message', 'edd-commissions-payouts' ),
            'details'           => __( 'Details', 'edd-commissions-payouts' ),
			'payout_method'     => __( 'Payout Method', 'edd-commissions-payouts' ),
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
	 * Output log details column
	 *
	 * @param array $item Contains all the data of the log
	 * @return void
	 */
	public function column_details( $item ) {
        $details = get_post_meta( $item['id'], '_edd_log_details', true );
        ?>
            <a href="#TB_inline?width=640&amp;inlineId=log-details-<?php echo $item['id']; ?>" class="thickbox"><?php _e( 'View Details', 'edd-commissions-payouts' ); ?></a>
            <div id="log-details-<?php echo $item['id']; ?>" style="display: none;">
                <p><pre><?php print esc_html( print_r( $details, true ) ); ?></pre></p>
            </div>
        <?php
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
		edd_log_views();
	}


	/**
	 * Gets the log entries for the current view
	 *
	 * @return array
	 */
	function get_logs() {
		global $edd_logs;

		// Prevent the queries from getting cached. Without this there are occasional memory issues for some installs
		wp_suspend_cache_addition( true );

		$logs_data = array();
		$log_query = array(
			'log_type'       => 'payouts',
			'paged'          => $this->get_paged(),
			'posts_per_page' => $this->per_page,
			'orderby'        => 'ID',
		);

		$logs = $edd_logs->get_connected_logs( $log_query );

		if ( $logs ) {
			foreach ( $logs as $log ) {
				$logs_data[] = array(
					'id'            => $log->ID,
					'type'          => get_post_meta( $log->ID, '_edd_log_type', true ),
					'message'       => get_post_meta( $log->ID, '_edd_log_message', true ),
					'details'       => get_post_meta( $log->ID, '_edd_log_details', true ),
					'payout_method' => get_post_meta( $log->ID, '_edd_log_payout_method', true ),
					'date'          => $log->post_date
				);
			}
		}

		return $logs_data;
	}


	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 */
	function prepare_items() {
		global $edd_logs;

        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        
        $this->items = $this->get_logs();
        
        $total_items = $edd_logs->get_log_count( null, 'payouts', array() );
        
		$this->set_pagination_args( array(
				'total_items'  => $total_items,
				'per_page'     => $this->per_page,
				'total_pages'  => ceil( $total_items / $this->per_page )
			)
		);
	}
}
