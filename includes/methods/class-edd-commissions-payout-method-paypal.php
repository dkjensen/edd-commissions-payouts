<?php
/**
 * PayPal payout method
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Commissions_Payout_Method_PayPal extends EDD_Commissions_Payouts_Method {


    public function setup() {
        $this->id           = 'paypal';
        $this->name         = __( 'PayPal', 'edd-commissions-payouts' );
        $this->icon         = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMjQiIGhlaWdodD0iMzMiPjxwYXRoIGZpbGw9IiMyNTNCODAiIGQ9Ik00Ni4yMTEgNi43NDloLTYuODM5YS45NS45NSAwIDAgMC0uOTM5LjgwMmwtMi43NjYgMTcuNTM3YS41Ny41NyAwIDAgMCAuNTY0LjY1OGgzLjI2NWEuOTUuOTUgMCAwIDAgLjkzOS0uODAzbC43NDYtNC43M2EuOTUuOTUgMCAwIDEgLjkzOC0uODAzaDIuMTY1YzQuNTA1IDAgNy4xMDUtMi4xOCA3Ljc4NC02LjUuMzA2LTEuODkuMDEzLTMuMzc1LS44NzItNC40MTUtLjk3Mi0xLjE0Mi0yLjY5Ni0xLjc0Ni00Ljk4NS0xLjc0NnpNNDcgMTMuMTU0Yy0uMzc0IDIuNDU0LTIuMjQ5IDIuNDU0LTQuMDYyIDIuNDU0aC0xLjAzMmwuNzI0LTQuNTgzYS41Ny41NyAwIDAgMSAuNTYzLS40ODFoLjQ3M2MxLjIzNSAwIDIuNCAwIDMuMDAyLjcwNC4zNTkuNDIuNDY5IDEuMDQ0LjMzMiAxLjkwNnptMTkuNjU0LS4wNzloLTMuMjc1YS41Ny41NyAwIDAgMC0uNTYzLjQ4MWwtLjE0NS45MTYtLjIyOS0uMzMyYy0uNzA5LTEuMDI5LTIuMjktMS4zNzMtMy44NjgtMS4zNzMtMy42MTkgMC02LjcxIDIuNzQxLTcuMzEyIDYuNTg2LS4zMTMgMS45MTguMTMyIDMuNzUyIDEuMjIgNS4wMzEuOTk4IDEuMTc2IDIuNDI2IDEuNjY2IDQuMTI1IDEuNjY2IDIuOTE2IDAgNC41MzMtMS44NzUgNC41MzMtMS44NzVsLS4xNDYuOTFhLjU3LjU3IDAgMCAwIC41NjIuNjZoMi45NWEuOTUuOTUgMCAwIDAgLjkzOS0uODAzbDEuNzctMTEuMjA5YS41NjguNTY4IDAgMCAwLS41NjEtLjY1OHptLTQuNTY1IDYuMzc0Yy0uMzE2IDEuODcxLTEuODAxIDMuMTI3LTMuNjk1IDMuMTI3LS45NTEgMC0xLjcxMS0uMzA1LTIuMTk5LS44ODMtLjQ4NC0uNTc0LS42NjgtMS4zOTEtLjUxNC0yLjMwMS4yOTUtMS44NTUgMS44MDUtMy4xNTIgMy42Ny0zLjE1Mi45MyAwIDEuNjg2LjMwOSAyLjE4NC44OTIuNDk5LjU4OS42OTcgMS40MTEuNTU0IDIuMzE3em0yMi4wMDctNi4zNzRoLTMuMjkxYS45NTQuOTU0IDAgMCAwLS43ODcuNDE3bC00LjUzOSA2LjY4Ni0xLjkyNC02LjQyNWEuOTUzLjk1MyAwIDAgMC0uOTEyLS42NzhoLTMuMjM0YS41Ny41NyAwIDAgMC0uNTQxLjc1NGwzLjYyNSAxMC42MzgtMy40MDggNC44MTFhLjU3LjU3IDAgMCAwIC40NjUuOWgzLjI4N2EuOTQ5Ljk0OSAwIDAgMCAuNzgxLS40MDhsMTAuOTQ2LTE1LjhhLjU3LjU3IDAgMCAwLS40NjgtLjg5NXoiLz48cGF0aCBmaWxsPSIjMTc5QkQ3IiBkPSJNOTQuOTkyIDYuNzQ5aC02Ljg0YS45NS45NSAwIDAgMC0uOTM4LjgwMmwtMi43NjYgMTcuNTM3YS41NjkuNTY5IDAgMCAwIC41NjIuNjU4aDMuNTFhLjY2NS42NjUgMCAwIDAgLjY1Ni0uNTYybC43ODUtNC45NzFhLjk1Ljk1IDAgMCAxIC45MzgtLjgwM2gyLjE2NGM0LjUwNiAwIDcuMTA1LTIuMTggNy43ODUtNi41LjMwNy0xLjg5LjAxMi0zLjM3NS0uODczLTQuNDE1LS45NzEtMS4xNDItMi42OTQtMS43NDYtNC45ODMtMS43NDZ6bS43ODkgNi40MDVjLS4zNzMgMi40NTQtMi4yNDggMi40NTQtNC4wNjIgMi40NTRoLTEuMDMxbC43MjUtNC41ODNhLjU2OC41NjggMCAwIDEgLjU2Mi0uNDgxaC40NzNjMS4yMzQgMCAyLjQgMCAzLjAwMi43MDQuMzU5LjQyLjQ2OCAxLjA0NC4zMzEgMS45MDZ6bTE5LjY1My0uMDc5aC0zLjI3M2EuNTY3LjU2NyAwIDAgMC0uNTYyLjQ4MWwtLjE0NS45MTYtLjIzLS4zMzJjLS43MDktMS4wMjktMi4yODktMS4zNzMtMy44NjctMS4zNzMtMy42MTkgMC02LjcwOSAyLjc0MS03LjMxMSA2LjU4Ni0uMzEyIDEuOTE4LjEzMSAzLjc1MiAxLjIxOSA1LjAzMSAxIDEuMTc2IDIuNDI2IDEuNjY2IDQuMTI1IDEuNjY2IDIuOTE2IDAgNC41MzMtMS44NzUgNC41MzMtMS44NzVsLS4xNDYuOTFhLjU3LjU3IDAgMCAwIC41NjQuNjZoMi45NDlhLjk1Ljk1IDAgMCAwIC45MzgtLjgwM2wxLjc3MS0xMS4yMDlhLjU3MS41NzEgMCAwIDAtLjU2NS0uNjU4em0tNC41NjUgNi4zNzRjLS4zMTQgMS44NzEtMS44MDEgMy4xMjctMy42OTUgMy4xMjctLjk0OSAwLTEuNzExLS4zMDUtMi4xOTktLjg4My0uNDg0LS41NzQtLjY2Ni0xLjM5MS0uNTE0LTIuMzAxLjI5Ny0xLjg1NSAxLjgwNS0zLjE1MiAzLjY3LTMuMTUyLjkzIDAgMS42ODYuMzA5IDIuMTg0Ljg5Mi41MDEuNTg5LjY5OSAxLjQxMS41NTQgMi4zMTd6bTguNDI2LTEyLjIxOWwtMi44MDcgMTcuODU4YS41NjkuNTY5IDAgMCAwIC41NjIuNjU4aDIuODIyYy40NjkgMCAuODY3LS4zNC45MzktLjgwM2wyLjc2OC0xNy41MzZhLjU3LjU3IDAgMCAwLS41NjItLjY1OWgtMy4xNmEuNTcxLjU3MSAwIDAgMC0uNTYyLjQ4MnoiLz48cGF0aCBmaWxsPSIjMjUzQjgwIiBkPSJNNy4yNjYgMjkuMTU0bC41MjMtMy4zMjItMS4xNjUtLjAyN0gxLjA2MUw0LjkyNyAxLjI5MmEuMzE2LjMxNiAwIDAgMSAuMzE0LS4yNjhoOS4zOGMzLjExNCAwIDUuMjYzLjY0OCA2LjM4NSAxLjkyNy41MjYuNi44NjEgMS4yMjcgMS4wMjMgMS45MTcuMTcuNzI0LjE3MyAxLjU4OS4wMDcgMi42NDRsLS4wMTIuMDc3di42NzZsLjUyNi4yOThhMy42OSAzLjY5IDAgMCAxIDEuMDY1LjgxMmMuNDUuNTEzLjc0MSAxLjE2NS44NjQgMS45MzguMTI3Ljc5NS4wODUgMS43NDEtLjEyMyAyLjgxMi0uMjQgMS4yMzItLjYyOCAyLjMwNS0xLjE1MiAzLjE4M2E2LjU0NyA2LjU0NyAwIDAgMS0xLjgyNSAyYy0uNjk2LjQ5NC0xLjUyMy44NjktMi40NTggMS4xMDktLjkwNi4yMzYtMS45MzkuMzU1LTMuMDcyLjM1NWgtLjczYy0uNTIyIDAtMS4wMjkuMTg4LTEuNDI3LjUyNWEyLjIxIDIuMjEgMCAwIDAtLjc0NCAxLjMyOGwtLjA1NS4yOTktLjkyNCA1Ljg1NS0uMDQyLjIxNWMtLjAxMS4wNjgtLjAzLjEwMi0uMDU4LjEyNWEuMTU1LjE1NSAwIDAgMS0uMDk2LjAzNUg3LjI2NnoiLz48cGF0aCBmaWxsPSIjMTc5QkQ3IiBkPSJNMjMuMDQ4IDcuNjY3Yy0uMDI4LjE3OS0uMDYuMzYyLS4wOTYuNTUtMS4yMzcgNi4zNTEtNS40NjkgOC41NDUtMTAuODc0IDguNTQ1SDkuMzI2Yy0uNjYxIDAtMS4yMTguNDgtMS4zMjEgMS4xMzJMNi41OTYgMjYuODNsLS4zOTkgMi41MzNhLjcwNC43MDQgMCAwIDAgLjY5NS44MTRoNC44ODFjLjU3OCAwIDEuMDY5LS40MiAxLjE2LS45OWwuMDQ4LS4yNDguOTE5LTUuODMyLjA1OS0uMzJjLjA5LS41NzIuNTgyLS45OTIgMS4xNi0uOTkyaC43M2M0LjcyOSAwIDguNDMxLTEuOTIgOS41MTMtNy40NzYuNDUyLTIuMzIxLjIxOC00LjI1OS0uOTc4LTUuNjIyYTQuNjY3IDQuNjY3IDAgMCAwLTEuMzM2LTEuMDN6Ii8+PHBhdGggZmlsbD0iIzIyMkQ2NSIgZD0iTTIxLjc1NCA3LjE1MWE5Ljc1NyA5Ljc1NyAwIDAgMC0xLjIwMy0uMjY3IDE1LjI4NCAxNS4yODQgMCAwIDAtMi40MjYtLjE3N2gtNy4zNTJhMS4xNzIgMS4xNzIgMCAwIDAtMS4xNTkuOTkyTDguMDUgMTcuNjA1bC0uMDQ1LjI4OWExLjMzNiAxLjMzNiAwIDAgMSAxLjMyMS0xLjEzMmgyLjc1MmM1LjQwNSAwIDkuNjM3LTIuMTk1IDEwLjg3NC04LjU0NS4wMzctLjE4OC4wNjgtLjM3MS4wOTYtLjU1YTYuNTk0IDYuNTk0IDAgMCAwLTEuMDE3LS40MjkgOS4wNDUgOS4wNDUgMCAwIDAtLjI3Ny0uMDg3eiIvPjxwYXRoIGZpbGw9IiMyNTNCODAiIGQ9Ik05LjYxNCA3LjY5OWExLjE2OSAxLjE2OSAwIDAgMSAxLjE1OS0uOTkxaDcuMzUyYy44NzEgMCAxLjY4NC4wNTcgMi40MjYuMTc3YTkuNzU3IDkuNzU3IDAgMCAxIDEuNDgxLjM1M2MuMzY1LjEyMS43MDQuMjY0IDEuMDE3LjQyOS4zNjgtMi4zNDctLjAwMy0zLjk0NS0xLjI3Mi01LjM5MkMyMC4zNzguNjgyIDE3Ljg1MyAwIDE0LjYyMiAwaC05LjM4Yy0uNjYgMC0xLjIyMy40OC0xLjMyNSAxLjEzM0wuMDEgMjUuODk4YS44MDYuODA2IDAgMCAwIC43OTUuOTMyaDUuNzkxbDEuNDU0LTkuMjI1IDEuNTY0LTkuOTA2eiIvPjwvc3ZnPg==';
        $this->authentication = true;

        // EDD setting using custom hook
        add_action( 'edd_commissions_payout_method_settings_paypal_status', array( $this, 'status_field' ) );
    }


    /**
     * Custom settings for admin authentication
     *
     * @return array
     */
    public function settings() {
        return array( 
            array(
                'id'   => 'commissions_payout_method_settings_paypal_header',
                'name' => '<h3>' . sprintf( __( '%s Payout Settings', 'edd-commissions-payouts' ), $this->get_name() ) . '</h3>',
                'type' => 'header',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_status',
                'name'      => __( 'Status', 'edd-commissions-payouts' ),
                'type'      => 'hook',
                'hook'      => 'commissions_payout_method_settings_paypal_status',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_mode',
                'name'      => __( 'Mode', 'edd-commissions-payouts' ),
                'type'      => 'radio',
                'std'       => $this->get_mode(),
                'options'   => array(
                    'live'     => __( 'Live', 'edd-commissions-payouts' ),
                    'sandbox'  => __( 'Sandbox', 'edd-commissions-payouts' )
                )
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_client_id_live',
                'name'      => __( 'Live Client ID', 'edd-commissions-payouts' ),
                'type'      => 'text',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_secret_live',
                'name'      => __( 'Live Secret', 'edd-commissions-payouts' ),
                'type'      => 'password',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_client_id_sandbox',
                'name'      => __( 'Sandbox Client ID', 'edd-commissions-payouts' ),
                'type'      => 'text',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_secret_sandbox',
                'name'      => __( 'Sandbox Secret', 'edd-commissions-payouts' ),
                'type'      => 'password',
            ),
            array(
                'id'        => 'commissions_payout_method_settings_paypal_help',
                'type'      => 'descriptive_text',
                'desc'      => '<p>' . __( 'Create a new PayPal REST API app by visiting the following link to receive your Client ID and Secret', 'edd-commissions-payouts' ) . '</p>' .
                               '<p>' . make_clickable( 'https://developer.paypal.com/developer/applications/' ) . '</p>'
            )
        );
    }


    /**
     * Returns the mode to be used for PayPal API calls
     *
     * @return string
     */
    public function get_mode() {
        $mode = edd_get_option( 'commissions_payout_method_settings_paypal_mode', 'live' );

        if ( ! in_array( $mode, array( 'live', 'sandbox' ) ) ) {
            return 'live';
        }

        return $mode;
    }


    /**
     * Returns the PayPal app client ID for live or production depending on mode set
     *
     * @return string
     */
    public function get_client_id() {
        $mode = $this->get_mode();

        return edd_get_option( 'commissions_payout_method_settings_paypal_client_id_' . $mode, '' );
    }


    /**
     * Returns the PayPal app secret for live or production depending on mode set
     *
     * @return string
     */
    public function get_secret() {
        $mode = $this->get_mode();

        return edd_get_option( 'commissions_payout_method_settings_paypal_secret_' . $mode, '' );
    }


    /**
     * Attempt to retrieve the PayPal API access token
     *
     * @param boolean $log Whether to log this request
     * @return string
     */
    public function retrieve_access_token( $log = true ) {
        if ( ! empty( $this->get_client_id() ) && ! empty( $this->get_secret() ) ) {
            try {
                $credentials = new \PayPal\Auth\OAuthTokenCredential( $this->get_client_id(), $this->get_secret() );
                $access_token = $credentials->getAccessToken( array( 'mode' => $this->get_mode() ) );

                return $access_token;
            }catch( \PayPal\Exception\PayPalConnectionException $e ) {
                if ( $log ) {
                    $this->log_error( $e->getMessage(), $e->getData() );
                }

                throw new Exception( $e->getMessage() );
            }
        }else {
            throw new Exception( sprintf( __( '%s Client ID and Secret must be set to retrieve access token.', 'edd-commissions-payouts' ), ucfirst( $this->get_mode() ) ) );
        }
    }


    /**
     * Renders the status of the PayPal API connection
     *
     * @return void
     */
    public function status_field() {
        try {
            // Will throw Exception if fail
            $this->retrieve_access_token( false );

            print '<p><strong>' . __( 'Connected', 'edd-commissions-payouts' ) . '</strong></p>';
        }catch( Exception $e ) {
            print '<p><strong>' . __( 'Not connected', 'edd-commissions-payouts' ) . '</strong></p>';
            print '<p class="error-message">' . esc_html( $e->getMessage() ) . '</p>';
        }
    }
}