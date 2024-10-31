<?php 
/*
Plugin Name: Ni WooCommerce Order Export
Description: Ni WooCommerce Order Export plugin provides the functionality to export the sales order information into CSV or excel format.
Author: anzia
Version: 3.1.6
Author URI: http://naziinfotech.com
Plugin URI: https://wordpress.org/plugins/ni-woocommerce-order-export/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Requires at least: 4.7
Tested up to: 6.4.3
WC requires at least: 3.0.0
WC tested up to: 8.7.0
Last Updated Date: 24-March-2024
Requires PHP: 7.0
*/
if ( ! defined( 'ABSPATH' ) ) { exit;}
if ( !class_exists( 'Ni_Woocommerce_Order_Export' ) ) {
	class Ni_Woocommerce_Order_Export {
		var $constant_variable = array();
		 function __construct() {
			 if ( is_admin() ) {
			 	
				
				include_once('include/ni-order-export.php'); 
				
				$this->constant_variable = array(
					'plugin_name' => 'Ni Woocommerce Order Export',
					'plugin_role' => 'manage_options',
					'plugin_key' => 'ni_order_export',
					'plugin_menu' => 'nioe-order-export',
					"plugin_file" 			=> __FILE__
				);
				
				add_filter( 'plugin_action_links', array( $this, 'nioe_add_action_plugin' ), 10,5);
				$GLOBALS['ni_order_export'] = new ni_order_export($this->constant_variable );
				
				
			 }
			 
		 }
		 function nioe_add_action_plugin( $actions, $plugin_file ) 
			{
				static $plugin;
			
				if (!isset($plugin))
					$plugin = plugin_basename(__FILE__);
				if ($plugin == $plugin_file) {
						  $settings_url = admin_url() . 'admin.php?page=nioe-order-settings';
							$settings = array('settings' => '<a href='. $settings_url.'>' . __('Settings', '') . '</a>');
							$site_link = array('support' => '<a href="http://naziinfotech.com" target="_blank">Support</a>');
							$email_link = array('email' => '<a href="mailto:support@naziinfotech.com" target="_top">Email</a>');
					
							$actions = array_merge($settings, $actions);
							$actions = array_merge($site_link, $actions);
							$actions = array_merge($email_link, $actions);
						
					}
					
					return $actions;
			}
	}
	$obj  = new Ni_Woocommerce_Order_Export();
}
/*
$constant_variable = array();
include_once('include/ni-order-export.php'); 
$constant_variable = array(
	'plugin_name' => 'Ni Woocommerce Order Export',
	'plugin_role' => 'manage_options',
	'plugin_key' => 'ni_order_export',
	'plugin_menu' => 'nioe-order-export',
	"plugin_file" 			=> __FILE__
);*/
//$GLOBALS['ni_order_export'] = new ni_order_export($constant_variable );
//add_filter( 'plugin_action_links', 'nioe_add_action_plugin', 10, 5 );
/*function nioe_add_action_plugin( $actions, $plugin_file ) 
{
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
			  $settings_url = admin_url() . 'admin.php?page=nioe-order-settings';
				$settings = array('settings' => '<a href='. $settings_url.'>' . __('Settings', '') . '</a>');
				$site_link = array('support' => '<a href="http://naziinfotech.com" target="_blank">Support</a>');
				$email_link = array('email' => '<a href="mailto:support@naziinfotech.com" target="_top">Email</a>');
		
    			$actions = array_merge($settings, $actions);
				$actions = array_merge($site_link, $actions);
				$actions = array_merge($email_link, $actions);
			
		}
		
		return $actions;
}*/
?>