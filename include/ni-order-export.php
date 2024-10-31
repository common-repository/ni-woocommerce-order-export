<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('ni-function.php'); 
if( !class_exists( 'ni_order_export' ) ) {
	class ni_order_export extends ni_function
	{
		var $constant_variable = array();
		
		public function __construct($constant_variable){
			//echo $page =  isset($_REQUEST["page"]) ;
		
			
			
			$this->constant_variable = $constant_variable;
			add_action( 'admin_menu',  array(&$this,'admin_menu' ));
			
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action( 'wp_ajax_ni_order_export_action',  array(&$this,'ni_order_export_action' )); /*used in form field name="action" value="my_action"*/
			add_action('admin_init', array( &$this, 'admin_init' ) );
			add_filter( 'plugin_row_meta',  array(&$this,'ni_order_plugin_row_meta' ), 10, 2 );
			
		}
		function ni_order_plugin_row_meta($links, $file){
			if ( $file == "ni-woocommerce-order-export/ni-woocommerce-order-export.php" ) {
				$row_meta = array(
				
				'ni_pro_version'=> '<a target="_blank" href="http://naziinfotech.com/product/ni-woocommerce-sales-report-pro">Buy Pro Version</a>',
				
				'ni_pro_review'=> '<a target="_blank" href="https://wordpress.org/support/plugin/ni-woocommerce-sales-report/reviews/">Write a Review</a>'	);
					
	
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}
		/*Add Admin Menu*/
		function admin_menu(){
			
			/*
			add_menu_page('Order Export','Order Export','manage_options',$this->constant_variable['plugin_menu'],array(&$this,'add_menu_page')
		,plugins_url( '../images/icon.png', __FILE__ )
		,58.558);
		*/
		add_menu_page('Order Export','Order Export','manage_options',$this->constant_variable['plugin_menu'],array(&$this,'add_menu_page')
		,'dashicons-migrate'
		,59.558);
    	add_submenu_page($this->constant_variable['plugin_menu'], 'Summary', 'Order Summary', 'manage_options',$this->constant_variable['plugin_menu'] , array(&$this,'add_menu_page'));
    	add_submenu_page($this->constant_variable['plugin_menu'], 'Order List', 'Order List', 'manage_options', 'nioe-order-list' , array(&$this,'add_menu_page'));
		
		add_submenu_page($this->constant_variable['plugin_menu'], 'Billing Address', 'Billing Address', 'manage_options', 'nioe-billing-address' , array(&$this,'add_menu_page'));
		
		add_submenu_page($this->constant_variable['plugin_menu'], 'Shipping Address', 'Shipping Address', 'manage_options', 'nioe-shipping-address' , array(&$this,'add_menu_page'));
		
		add_submenu_page($this->constant_variable['plugin_menu'], 'Settings', 'Settings', 'manage_options', 'nioe-order-settings' , array(&$this,'add_menu_page'));
		add_submenu_page($this->constant_variable['plugin_menu'], 'Add-ons', 'Add-ons', 'manage_options', 'nioe-add-ons' , array(&$this,'add_menu_page'));
		}
		/*Add page to menu*/
		function add_menu_page()
		{
			$page=$this->get_request("page");
			
			if ($page=="nioe-order-list"){
				include_once("ni-order-list.php");
				$obj =  new ni_order_list();
				$obj->page_init();
			}
			/*Billing Address*/
			if ($page=="nioe-billing-address"){
				include_once("ni-order-billing-address.php");
				$obj =  new ni_order_billing_address();
				$obj->page_init();
				
			}
			/*Shipping Address*/
			if ($page =="nioe-shipping-address"){
				include_once("ni-order-shipping-address.php");
				$obj =  new ni_order_shipping_address();
				$obj->page_init();
			}
			
			if ($page=="nioe-order-export")
			{
				include_once("ni-order-summary.php");
				$obj =  new ni_order_summary();
				//$obj->page_init();
				
			}
			
			if ($page=="nioe-add-ons"){
				include_once("ni-addons.php");
				$obj =  new ni_addons();
				$obj->page_init();
				
			}
			if ($page == "nioe-order-settings"){
				include_once("ni-order-setting.php");
				$obj =  new Ni_Order_Setting();
				$obj->page_init();
			}
		}
		function admin_enqueue_scripts($hook){
			if (isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				if ($page == "nioe-order-export"){
					wp_register_style('ni-sales-report-summary-css', plugins_url( '../assets/css/ni-sales-report-summary.css', __FILE__ ));
		 			wp_enqueue_style('ni-sales-report-summary-css');
					
					wp_register_style('ni-font-awesome-css', plugins_url( '../assets/css/font-awesome.css', __FILE__ ));
		 			wp_enqueue_style('ni-font-awesome-css');
					
					wp_register_script( 'ni-amcharts-script', plugins_url( '../assets/js/amcharts/amcharts.js', __FILE__ ) );
					wp_enqueue_script('ni-amcharts-script');
				
		
					wp_register_script( 'ni-light-script', plugins_url( '../assets/js/amcharts/light.js', __FILE__ ) );
					wp_enqueue_script('ni-light-script');
				
					wp_register_script( 'ni-pie-script', plugins_url( '../assets/js/amcharts/pie.js', __FILE__ ) );
					wp_enqueue_script('ni-pie-script');	
				}else{
					//nioe-order-settings nioe-shipping-address `1 nioe-order-list
					if ($page == "nioe-order-settings" || $page == "nioe-billing-address" || $page == "nioe-shipping-address"|| $page == "nioe-order-list"){
						wp_register_style( 'ni-order-export-style', plugins_url( '../assets/css/ni-order-export-style.css', __FILE__ ));
						wp_enqueue_style( 'ni-order-export-style' );
					}
				}
				
				wp_enqueue_script( 'ajax-script-order-export', plugins_url( '../assets/js/ni-order-export-script.js', __FILE__ ), array('jquery') );
				wp_localize_script( 'ajax-script-order-export', 'ni_order_export_ajax_object',array( 'ni_order_export_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			}
			
		
			
			
		}
		function ni_order_export_action(){
			$ni_action_ajax	= $this->get_request("ni_action_ajax");
			$page			= $this->get_request("page");
			//echo json_encode($_REQUEST);
			//die;
			if($ni_action_ajax =="ni_order_list")
			{
				//$this->print_data($_REQUEST);
				include_once("ni-order-list.php");
				$obj =  new ni_order_list();
				$obj->get_order_list();	
			}
			//$ni_action_ajax=$this->get_request("ni_action_ajax");
			if($ni_action_ajax =="ni_order_billing_address")
			{
				//$this->print_data($_REQUEST);
				include_once("ni-order-billing-address.php");
				$obj =  new ni_order_billing_address();
				$obj->get_order_list();	
			}
			if($ni_action_ajax =="ni_order_shipping_address")
			{//echo "dsad";
			//die;
				//$this->print_data($_REQUEST);
				include_once("ni-order-shipping-address.php");
				$obj =  new ni_order_shipping_address();
				$obj->get_order_list();	
			}
			if ($page=="nioe-order-settings"){
				include_once("ni-order-setting.php");
				$obj =  new Ni_Order_Setting();
				$obj->page_ajax();
			}
			die;
		}
		function admin_init(){
			
			/*Order Export*/
			if(isset($_REQUEST['btn_excel_export'])){
				$today = date_i18n("Y-m-d-H-i-s");				
				$FileName = "order-list"."-".$today.".xls";	
				
				include_once("ni-order-list.php");
				$obj = new ni_order_list();
				$obj->ni_order_export($FileName,"xls");
				die;
			}	
			if(isset($_REQUEST['btn_print'])){
				include_once("ni-order-list.php");
				$obj = new ni_order_list();
				$obj->get_print_content();
				die;
			}	
			/*Billing Address Export*/
			if(isset($_REQUEST['btn_excel_export_billing'])){
				$today = date_i18n("Y-m-d-H-i-s");				
				$FileName = "ni-order-billing-address"."-".$today.".xls";	
				
				include_once("ni-order-billing-address.php");
				$obj =  new ni_order_billing_address();
				$obj->ni_order_export($FileName,"xls");
				die;
			}
			/*Export Shipping Address*/
			/*btn_excel_export_shipping*/	
			if(isset($_REQUEST['btn_excel_export_shipping'])){
				$today = date_i18n("Y-m-d-H-i-s");				
				$FileName = "order-shipping-address"."-".$today.".xls";	
				
				include_once("ni-order-shipping-address.php");
				$obj =  new ni_order_shipping_address();
				$obj->ni_order_export($FileName,"xls");
				die;
			}
			
		}
	}
}
?>