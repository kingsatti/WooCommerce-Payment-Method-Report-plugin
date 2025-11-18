<?php
/*
  Plugin Name: Qaisar Satti Dashboard Payment
  Description: Qaisar Satti Dashboard Payment
  Author: Qaisar Satti
  Version: 1.0.0
  Author URI: https://store.qaisarsatti.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Qaisarsatti_Dashboard_Payment {
    public function __construct() {
        $this->init();
    }
    
    public function init(){
        add_action( "wp_dashboard_setup", array( $this, "qaisarsatti_dashboard_payment_widgets" ) );
    }
    
    public function qaisarsatti_dashboard_payment_widgets() {
        wp_add_dashboard_widget( "qaisarsatti_dashboard_payment", "Payment Method Base Report", array( $this, "qaisarsatti_dashboard_payment" ) );

    }
    
    public function qaisarsatti_dashboard_payment() {
        include_once("templates/payment.php");
    }
    
}

$qaisarsatti_dashboard_widgets = new Qaisarsatti_Dashboard_Payment();