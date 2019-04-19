<?php
/**
 * Setup methods class file
 * 
 * @package EDD Commissions Payouts
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class EDD_Commissions_Payouts_Setup {

    public function __construct() {
        // Scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        // Post types
        add_action( 'init', array( $this, 'register_post_type' ) );

        // Logs
        add_filter( 'edd_log_types', array( $this, 'register_log_type' ) );
        add_filter( 'edd_log_views', array( $this, 'register_log_view' ) );
        add_action( 'edd_logs_view_payouts', array( $this, 'log_view' ) );
    }


    /**
     * Front end scripts
     *
     * @return void
     */
    public function enqueue_scripts() {
        $suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

        if ( apply_filters( 'edd_commissions_payouts_load_frontend_css', true ) ) {
            wp_enqueue_style( 'edd-commissions-payouts', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/css/edd-commissions-payouts' . $suffix . '.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
            wp_enqueue_style( 'edd-commissions-payouts-icons', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/icons/style.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );
        }

        wp_register_script( 'edd-commissions-payouts', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/js/edd-commissions-payouts' . $suffix . '.js', array( 'jquery', 'fes_form', 'fes_sw', 'fes_spin', 'fes_spinner' ), EDD_COMMISSIONS_PAYOUTS_VER, true );
    
        wp_localize_script( 'edd-commissions-payouts', 'eddcp_obj', array(
            'user_payout_method_nonce'  => wp_create_nonce( 'process_payout_method' ),
            'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
            'strings'                   => array(
                'error'                 => __( 'Error', 'edd-commissions-payouts' ),
                'success'               => __( 'Success', 'edd-commissions-payouts' ),
                'loading'               => __( 'Loading', 'edd-commissions-payouts' ),
                'cancel'                => __( 'Cancel', 'edd-commissions-payouts' ),
            )
        ) );

        wp_enqueue_script( 'edd-commissions-payouts' );
    }


    /**
     * Back end scripts
     *
     * @return void
     */
    public function admin_enqueue_scripts() {
        $suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

        wp_enqueue_style( 'edd-commissions-payouts-admin', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/css/edd-commissions-payouts-admin' . $suffix . '.css', array(), EDD_COMMISSIONS_PAYOUTS_VER );

        wp_register_script( 'edd-commissions-payouts-admin', EDD_COMMISSIONS_PAYOUTS_PLUGIN_URL . 'assets/js/edd-commissions-payouts-admin' . $suffix . '.js', array( 'jquery' ), EDD_COMMISSIONS_PAYOUTS_VER, true );
    
        wp_localize_script( 'edd-commissions-payouts-admin', 'eddcp_admin_obj', array(
            'next_payout_nonce'                         => wp_create_nonce( 'next_payout_nonce' ),
            'ajaxurl'                                   => admin_url( 'admin-ajax.php' ),
            'strings'                                   => array(
                'next_payout_loading'                   => __( 'Loading payout schedule preview', 'edd-commissions-payouts' ) . '&hellip;',
                'next_payout_failed'                    => __( 'AJAX request to get the payout schedule preview failed', 'edd-commissions-payouts' ),
                'confirm_enable_automatic_payouts'      => __( 'Enabling automatic payouts will automatically pay any commissions due according to the payout schedule you have set. Are you sure you would like to proceed?', 'edd-commissions-payouts' ),
                'confirm_disable_automatic_payouts'     => __( 'Disabling automatic payouts will cancel any scheduled commissions payouts from occuring. Are you sure you would like to proceed?', 'edd-commissions-payouts' )
            )
        ) );

        wp_enqueue_script( 'edd-commissions-payouts-admin' );
    }


    /**
     * Registers the payout post type
     *
     * @return void
     */
    public function register_post_type() {
        $args = array(
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => 'edit.php?post_type=download',
            'show_in_admin_bar' => false,
            'rewrites'          => false,
            'hierarchical'      => false,
			'query_var'         => false,
			'supports'          => false,
			'can_export'        => true,
            'capability_type'   => 'post',
			'capabilities'      => array(
				'publish_posts'       => 'cap_that_doesnt_exist',
				'edit_posts'          => 'manage_shop_settings',
				'edit_others_posts'   => 'manage_shop_settings',
				'delete_posts'        => 'cap_that_doesnt_exist',
				'delete_others_posts' => 'cap_that_doesnt_exist',
				'read_private_posts'  => 'cap_that_doesnt_exist',
				'edit_post'           => 'manage_shop_settings',
				'delete_post'         => 'cap_that_doesnt_exist',
				'read_post'           => 'manage_shop_settings',
				'create_posts'        => 'cap_that_doesnt_exist',
			),
            'labels'            => array(
                'name'               => _x( 'Payouts', 'post type general name', 'edd-commissions-payouts' ),
                'singular_name'      => _x( 'Payout', 'post type singular name', 'edd-commissions-payouts' ),
                'menu_name'          => _x( 'Payouts', 'admin menu', 'edd-commissions-payouts' ),
                'name_admin_bar'     => _x( 'Payout', 'add new on admin bar', 'edd-commissions-payouts' ),
                'add_new'            => _x( 'Add New', 'payout', 'edd-commissions-payouts' ),
                'add_new_item'       => __( 'Add New Payout', 'edd-commissions-payouts' ),
                'new_item'           => __( 'New Payout', 'edd-commissions-payouts' ),
                'edit_item'          => __( 'Edit Payout', 'edd-commissions-payouts' ),
                'view_item'          => __( 'View Payout', 'edd-commissions-payouts' ),
                'all_items'          => __( 'Payouts', 'edd-commissions-payouts' ),
                'search_items'       => __( 'Search Payouts', 'edd-commissions-payouts' ),
                'parent_item_colon'  => __( 'Parent Payouts:', 'edd-commissions-payouts' ),
                'not_found'          => __( 'No payouts found.', 'edd-commissions-payouts' ),
                'not_found_in_trash' => __( 'No payouts found in Trash.', 'edd-commissions-payouts' )
            )
        );

        register_post_type( 'edd_payout', apply_filters( 'edd_payout_post_type_args', $args ) );
    }


    /**
     * Register the log view
     *
     * @param array $types
     * @return array
     */
    public function register_log_type( $types ) {
        $types[] = 'payouts';

        return $types;
    }


    /**
     * Register the log view label
     *
     * @param array $views
     * @return array
     */
    public function register_log_view( $views ) {
        $views['payouts'] = __( 'Payouts', 'edd-commissions-payouts' );

        return $views;
    }


    /**
     * Payouts log view
     *
     * @return void
     */
    public function log_view() {
        $logs_table = new EDD_Commissions_Payouts_Log_Table();
        $logs_table->prepare_items();
        ?>

        <div class="wrap">
            <form id="edd-logs-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-reports&tab=logs' ); ?>">
                <?php $logs_table->display(); ?>
                <input type="hidden" name="page" value="edd-reports" />
                <input type="hidden" name="tab" value="logs" />
            </form>
        </div>
    
        <?php
    }
}