<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}


include 'SDK/m2upay_backend/M2UPay.php';
use M2U\M2UPay;

if( ! class_exists( 'WC_Maybank2u_Pay_Gateway' ) ){
	

	class WC_Maybank2u_Pay_Gateway extends WC_Payment_Gateway  {
		
		public function __construct(){
			
			$this->id = 'maybank2u_pay';
			$this->icon = WC_MAYBANK2U_PAY_PLUGIN_URL.'/includes/SDK/Maybank2uPay_button.png';
			$this->method_description = __('Maybank2u Pay is an online debit payment gateway solution for your business. Your customers can now pay for purchases instantly and securely via Maybank Current or Saving account. Apply now for Maybank2u Pay to utilize a secure online payment system to meet your business needs.','woocommerce-maybank2u-pay');
			$this->has_fields = true;
			$this->supports           = array( 'products');
			
			
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woocommerce-maybank2u-pay' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Maybank2u Pay Gateway', 'woocommerce-maybank2u-pay' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'woocommerce-maybank2u-pay' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-maybank2u-pay' ),
					'default' => __( 'Maybank2u Pay', 'woocommerce-maybank2u-pay' ),
					'desc_tip'      => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'woocommerce-maybank2u-pay' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-maybank2u-pay' ),
					'default'     => __( "Pay for purchases instantly and securely via Maybank Current or Saving account.", 'woocommerce-maybank2u-pay' ),
				),
			);
			
			
			// Define user set variables.
			$this->title          = $this->get_option( 'title' );
			$this->description    = $this->get_option( 'description' );
			$this->testmode       = 'yes' === $this->get_option( 'testmode', 'no' );
			$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );
			$this->order_button_text ='Continue to Payment';
			
			
			
			$this->init_form_fields();
			$this->init_settings();
			
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_receipt_maybank2u_pay', array( $this, 'show_payment_window' ) );
		}
		
		function show_payment_window($order_id){
			
			global $woocommerce;
			
			
			
			$order = wc_get_order($order_id);
			
			//Pass in required parameters
			$total = number_format(floatval($order->get_total()),2);
			
			$m2u_json= array(
				'amount'=> $total,
				'accountNumber'=>$order->get_id(), //This “accountNumber” field is for you to pass the purchase ref number / invoice number/ bill number. Maybank will pass the same purchase ref number / invoice number/ bill number back to you (under parameters 'AcctId') to match the transaction status when Maybank send you the  Realtime Payment Notification (RPN).
				'payeeCode'=>"123"
			);
			
			$envType = 0;
			$M2U_Pay = new M2UPay();
			$encrypt_json = $M2U_Pay->getEncryptionString($m2u_json, $envType);
			wp_enqueue_script('wc_m2upay_frontend',WC_MAYBANK2U_PAY_PLUGIN_URL.'/includes/SDK/m2upay_frontend/m2upay_frontend.js',array('jquery'));
			wp_enqueue_script('wc-maybank2u-pay-checkout',WC_MAYBANK2U_PAY_PLUGIN_URL.'/assets/js/checkout.js',array('jquery','wc_m2upay_frontend'));
			wp_localize_script( 'wc-maybank2u-pay-checkout', 'wc_maybank2u_params', array(
				'encrypt_json'    => $encrypt_json,
			) );
		}
		
		
		function process_payment( $order_id ) {
			
			$order = wc_get_order($order_id);
			return array(
				'result' => 'success',
				'redirect' => $order->get_checkout_payment_url(true)
			);
		}
	}
}