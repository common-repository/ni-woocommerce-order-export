<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_function' ) ) {
	class ni_function{
	
		function __construct() {
			
	    }
		/*Get Request*/
		public function get_request($name,$default = NULL,$set = false){
			if(isset($_REQUEST[$name])){
				$newRequest = $_REQUEST[$name];
			
			if(is_array($newRequest)){
				$newRequest = implode(",", $newRequest);
			}else{
				$newRequest = trim($newRequest);
			}
			
			if($set) $_REQUEST[$name] = $newRequest;
			
			return $newRequest;
				}else{
					if($set) 	$_REQUEST[$name] = $default;
				return $default;
			}
		}
		/*Print Data */
		function print_data($r)
		{
			echo '<pre>',print_r($r,1),'</pre>';	
		}
		/*Get Country Name*/
		function get_country_name($code)
		{	$name = "";
			if(strlen($code)>0){
				$name= isset( WC()->countries->countries[ $code])? WC()->countries->countries[ $code]:$code;	
				$name  = isset($name) ? $name : $code;
			}
			return $name;
		}
		function get_request_parameter($request){
			
			//$this->print_data($request);
		 foreach($request as $key => $value):				 
					if(is_array($value)){
						echo "<input type=\"hidden\" name=\"".$key."[".$akey."]\" value=\"".implode(",",$value)."\" />";			
					}else{
						echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\" />";
					}					
				endforeach;
		}
		function get_order_product($order_id =NULL){
			global $wpdb;
			$product_names = "";
			$test312 = "";	
			$query = "";
			$query .= " SELECT ";
			$query .= " order_items.order_item_name as product_name ";
			$query .= ", qty.meta_value as qty ";
			$query .= ", line_total.meta_value as line_total ";
			$query .= " FROM {$wpdb->prefix}woocommerce_order_items as order_items ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			$query .= " WHERE 1=1 ";
			$query .= " AND order_items.order_item_type = 'line_item'";
			$query .= " AND order_items.order_id = {$order_id}";
			$query .= " AND line_total.meta_key = '_line_total'";
			$query .= " AND qty.meta_key = '_qty'";
			$row = $wpdb->get_results($query);		
			
			return $row;
		}
		function get_product_name($order_id =NULL){
			global $wpdb;
		$product_names = "";
		$test312 = "";	
		$query = "";
		$query .= " SELECT order_item_name as product_name FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items ";
		$query .= " WHERE 1=1 ";
		$query .= " AND woocommerce_order_items.order_item_type = 'line_item'";
		$query .= " AND woocommerce_order_items.order_id = {$order_id}";
		$results = $wpdb->get_results($query);		
		if (count($results)==0){
			return '';
		}
		foreach($results as $k=>$v){
			if(strlen($product_names)==0){
				 $product_names =  isset($v->product_name)?$v->product_name:'';
			}else{
				  $product_names .= isset($v->product_name)?", ". $v->product_name:'';
			}
		}
		return $product_names;
		}
		/*Export*/
		function ExportToCsv($filename = 'export.csv',$rows =array(),$columns=array(),$format="csv"){				
			global $wpdb;
			$csv_terminated = "\n";
			$csv_separator = ",";
			$csv_enclosed = '"';
			$csv_escaped = "\\";
			$fields_cnt = count($columns); 
			$schema_insert = '';
			
			if($format=="xls"){
				$csv_terminated = "\r\n";
				$csv_separator = "\t";
			}
				
			foreach($columns as $key => $value):
				$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $value) . $csv_enclosed;
				$schema_insert .= $l;
				$schema_insert .= $csv_separator;
			endforeach;// end for
		 
		   $out = trim(substr($schema_insert, 0, -1));
		   $out .= $csv_terminated;
			
			//printArray($rows);
			
			for($i =0;$i<count($rows);$i++){
				
				//printArray($rows[$i]);
				$j = 0;
				$schema_insert = '';
				foreach($columns as $key => $value){
						
						
						 if ($rows[$i][$key] == '0' || $rows[$i][$key] != ''){
							if ($csv_enclosed == '')
							{
								$schema_insert .= $rows[$i][$key];
							} else
							{
								$schema_insert .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $rows[$i][$key]) . $csv_enclosed;
							}
						 }else{
							$schema_insert .= '';
						 }
						
						
						
						if ($j < $fields_cnt - 1)
						{
							$schema_insert .= $csv_separator;
						}
						$j++;
				}
				$out .= $schema_insert;
				$out .= $csv_terminated;
			}
			
			if($format=="csv"){
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Length: " . strlen($out));	
				header("Content-type: text/x-csv");
				header("Content-type: text/csv");
				header("Content-type: application/csv");
				header("Content-Disposition: attachment; filename=$filename");
			}elseif($format=="xls"){
				
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Length: " . strlen($out));
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=$filename");
				header("Pragma: no-cache");
				header("Expires: 0");
			}
			
			echo $out;
			exit;
		 
		}
		function get_order_columns(){
			$column =  array();
			/*Billing Details*/
			$column["order_id"]   			=__("Order ID","niwooexport");
			$column["order_status"]   		=__("Order Status","niwooexport");
			$column["order_date"]   		=__("Order Date","niwooexport");
			
			$column["product_name"]  		=__("Product Name","niwooexport");
			
			$column["billing_first_name"]   =__("Billing First Name","niwooexport");
			$column["billing_last_name"]  	=__("Billing Last Name","niwooexport");
			$column["billing_company"]  	=__("Billing Company","niwooexport");
			$column["billing_address_1"]  	=__("Billing Address 1","niwooexport");
			$column["billing_address_2"]  	=__("Billing Address 2","niwooexport");
			$column["billing_city"]  		=__("Billing City","niwooexport");
			$column["billing_state"]  		=__("Billing State","niwooexport");
			$column["billing_postcode"]  	=__("Billing Postcode","niwooexport");
			$column["billing_country"]  	=__("Billing Country","niwooexport");
			$column["billing_email"]  		=__("Billing Email","niwooexport");
			$column["billing_phone"]  		=__("Billing Phone","niwooexport");
			
			/*Shipping Details*/
			$column["shipping_first_name"]  =__("Shipping First Name","niwooexport");
			$column["shipping_last_name"]  	=__("Shipping Last Name","niwooexport");
			$column["shipping_company"]  	=__("Shipping Company","niwooexport");
			$column["shipping_address_1"]  	=__("Shipping Address 1","niwooexport");
			$column["shipping_address_2"]  	=__("Shipping Address 2","niwooexport");
			$column["shipping_city"]  		=__("Shipping City","niwooexport");
			$column["shipping_state"]  		=__("Shipping State","niwooexport");
			$column["shipping_postcode"]  	=__("Shipping Postcode","niwooexport");
			$column["shipping_country"]  	=__("Shipping Country","niwooexport");
			
			/*Currency*/
			$column["order_currency"]  		=__("Order Currency","niwooexport");
			$column["cart_discount"]  		=__("Cart Discount","niwooexport");
			$column["cart_discount_tax"]  	=__("Cart Discount Tax","niwooexport");
			$column["order_shipping"]  		=__("Order shipping","niwooexport");
			$column["order_shipping_tax"]  	=__("Order Shipping tax","niwooexport");
			$column["order_tax"]  			=__("Order Tax","niwooexport");
			$column["order_total"]  		=__("Order Total","niwooexport");
			
		
			
			
			//$column["order_product"]  		=__("order_product1","niwooexport");
			
			return apply_filters('ni_order_export_columns', $column );
			//return $column;
		}
		function get_order_status_dropdown_html($selected_value =array()){
			$status = array();
			$status =		$this->get_order_status();
			?>
            <select multiple="multiple" name="order_status[]" id="order_status">
            <?php
			foreach($status as $key=>$value){
			?>
            <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
            <?php	
			}
			?>
            </select>
            <?php
		}
		function get_order_status(){
			global $wpdb;
			$order_status = array();
			$query = "";
			$query .= " SELECT  posts.post_status  as order_status";
			$query .= "	FROM {$wpdb->prefix}posts as posts		";
			$query .= "	WHERE 1 = 1 ";
			$query .= " AND posts.post_status NOT IN ('auto-draft','inherit')";
			$query .= " AND posts.post_type ='shop_order'";
			$query .= " group By order_status";
			
			$row = $wpdb->get_results( $query);	
			foreach($row as $key=>$value){
				$order_status[ $value->order_status] =  ucfirst( str_replace("wc-","", $value->order_status)) ; 
			}	
			return $order_status;
		}
		function get_billing_country(){
			global $wpdb;
			$billing_country = array();
			$query = "";
			$query .= " SELECT billing_country.meta_value as billing_country";
			$query .= "	FROM {$wpdb->prefix}posts as posts		";
			$query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id=posts.ID ";
			
			$query .= "	WHERE 1 = 1 ";
			$query .= " AND posts.post_status NOT IN ('auto-draft','inherit')";
			$query .= " AND posts.post_type ='shop_order'";
			
			$query .= " AND billing_country.meta_key = '_billing_country'";	 
			 
			$query .= " group By billing_country.meta_value";	
			
			$row = $wpdb->get_results( $query);	
			foreach($row as $key=>$value){
				$billing_country[$value->billing_country] =  $this->get_country_name($value->billing_country); 
			}	
		//	$this->prettyPrint($billing_country);
			return $billing_country;
			
		}
		function get_billing_country_dropdown_html($selected_value =array()){
			$row = array();
			$row =		$this->get_billing_country();
			?>
            <select multiple="multiple" name="billing_country[]" id="billing_country">
            <?php
			foreach($row as $key=>$value){
			?>
            <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
            <?php	
			}
			?>
            </select>
            <?php
		}
	}
	
}
?>