<?php
/**
 * Stripe payout method
 * 
 * @package EDD Commissions Payouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Commissions_Payout_Method_Stripe extends EDD_Commissions_Payouts_Method {

    public function setup() {
        $this->id           = 'stripe';
        $this->name         = __( 'Stripe', 'edd-commissions-payouts' );
        $this->icon         = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzNjAiIGhlaWdodD0iMTUwIj48c3R5bGU+LnN0MHtmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtmaWxsOiM2NzcyZTV9PC9zdHlsZT48cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzYwIDc3LjRjMC0yNS42LTEyLjQtNDUuOC0zNi4xLTQ1LjgtMjMuOCAwLTM4LjIgMjAuMi0zOC4yIDQ1LjYgMCAzMC4xIDE3IDQ1LjMgNDEuNCA0NS4zIDExLjkgMCAyMC45LTIuNyAyNy43LTYuNVY5NmMtNi44IDMuNC0xNC42IDUuNS0yNC41IDUuNS05LjcgMC0xOC4zLTMuNC0xOS40LTE1LjJoNDguOWMwLTEuMy4yLTYuNS4yLTguOXptLTQ5LjQtOS41YzAtMTEuMyA2LjktMTYgMTMuMi0xNiA2LjEgMCAxMi42IDQuNyAxMi42IDE2aC0yNS44ek0yNDcuMSAzMS42Yy05LjggMC0xNi4xIDQuNi0xOS42IDcuOGwtMS4zLTYuMmgtMjJ2MTE2LjZsMjUtNS4zLjEtMjguM2MzLjYgMi42IDguOSA2LjMgMTcuNyA2LjMgMTcuOSAwIDM0LjItMTQuNCAzNC4yLTQ2LjEtLjEtMjktMTYuNi00NC44LTM0LjEtNDQuOHptLTYgNjguOWMtNS45IDAtOS40LTIuMS0xMS44LTQuN2wtLjEtMzcuMWMyLjYtMi45IDYuMi00LjkgMTEuOS00LjkgOS4xIDAgMTUuNCAxMC4yIDE1LjQgMjMuMyAwIDEzLjQtNi4yIDIzLjQtMTUuNCAyMy40ek0xNjkuOCAyNS43bDI1LjEtNS40VjBsLTI1LjEgNS4zek0xNjkuOCAzMy4zaDI1LjF2ODcuNWgtMjUuMXpNMTQyLjkgNDAuN2wtMS42LTcuNGgtMjEuNnY4Ny41aDI1VjYxLjVjNS45LTcuNyAxNS45LTYuMyAxOS01LjJ2LTIzYy0zLjItMS4yLTE0LjktMy40LTIwLjggNy40ek05Mi45IDExLjZsLTI0LjQgNS4yLS4xIDgwLjFjMCAxNC44IDExLjEgMjUuNyAyNS45IDI1LjcgOC4yIDAgMTQuMi0xLjUgMTcuNS0zLjNWOTljLTMuMiAxLjMtMTkgNS45LTE5LTguOVY1NC42aDE5VjMzLjNoLTE5bC4xLTIxLjd6TTI1LjMgNTguN2MwLTMuOSAzLjItNS40IDguNS01LjQgNy42IDAgMTcuMiAyLjMgMjQuOCA2LjRWMzYuMmMtOC4zLTMuMy0xNi41LTQuNi0yNC44LTQuNkMxMy41IDMxLjYgMCA0Mi4yIDAgNTkuOSAwIDg3LjUgMzggODMuMSAzOCA5NWMwIDQuNi00IDYuMS05LjYgNi4xLTguMyAwLTE4LjktMy40LTI3LjMtOHYyMy44YzkuMyA0IDE4LjcgNS43IDI3LjMgNS43IDIwLjggMCAzNS4xLTEwLjMgMzUuMS0yOC4yLS4xLTI5LjgtMzguMi0yNC41LTM4LjItMzUuN3oiLz48L3N2Zz4=';
    }


    /**
     * Executes the payout request to Stripe
     *
     * @param EDD_Commissions_Payout Instance of the payout
     * @return void
     */
    public function process_batch_payout( EDD_Commissions_Payout &$payout ) {
        //
    }
}