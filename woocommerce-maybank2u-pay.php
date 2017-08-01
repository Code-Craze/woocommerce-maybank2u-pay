<?php
/*
Plugin Name: Maybank2u Pay Gateway for WooCommerce
Plugin URI: https://codecraze.io
Description: Maybank2u Pay Gateway for WooCommerce
Version: 1.0.0
Author: codeCraze
Author URI:  https://codecraze.io
Text Domain: woocommerce-maybank2u-pay
Domain Path: /languages
Copyright: © 2017, codeCraze.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( in_array( 'woocommerce/woocommerce.php' , apply_filters( 'active_plugins' , get_option( 'active_plugins' ) ) ) ){

	if(!class_exists('Woocommerce_Maybank2u_Pay')){
		
		class Woocommerce_Maybank2u_Pay{
			
			public $gateway_instance;
			private function __construct(){
				
				define( 'WC_MAYBANK2U_PAY_VERSION' , '1.0.0' );
//				define( 'WC_MAYBANK2U_PAY_TEMPLATE_PATH' , untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
				define( 'WC_MAYBANK2U_PAY_PLUGIN_URL' , untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ) ) ) );
				add_action( 'woocommerce_init' , array( $this , 'early_includes' ) );
				add_action( 'init' , array( $this , 'load_plugin_textdomain' ) );
				add_filter( 'woocommerce_payment_gateways', array($this,'add_gateway_class') );
			
			}
			
			
			public static function &get_singleton(){
				if( ! isset( $GLOBALS[ 'wc_maybank2u_pay' ] ) )
					$GLOBALS[ 'wc_maybank2u_pay' ] = new Woocommerce_Maybank2u_Pay();
				return $GLOBALS[ 'wc_maybank2u_pay' ];
			}
			
			
			public function add_gateway_class($gateways){
				$gateways[] = 'WC_Maybank2u_Pay_Gateway';
				return $gateways;
			}
			public function early_includes(){
				
				include('includes/class-wc-maybank2u-pay-gateway.php');
				$this->gateway_instance = new WC_Maybank2u_Pay_Gateway();
			}
			public function load_plugin_textdomain(){
				load_plugin_textdomain( 'woocommerce-maybank2u-pay' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
				
			}
		}
	}
	
	// Install the singleton instance
	Woocommerce_Maybank2u_Pay::get_singleton();
	register_activation_hook( __FILE__ , array( 'Woocommerce_Maybank2u_Pay' , 'plugin_activated' ) );
	register_deactivation_hook( __FILE__ , array( 'Woocommerce_Maybank2u_Pay' , 'plugin_deactivated' ) );
	
	
}