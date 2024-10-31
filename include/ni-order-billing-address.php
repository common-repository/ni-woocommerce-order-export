<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
include_once('ni-function.php'); 
if( !class_exists( 'ni_order_list' ) ) {
	class ni_order_billing_address extends ni_function{
		public function __construct(){
			
		}
		public function page_init(){
		$input_type = "text";
		$input_type = "hidden"
		?>
        <div class="wooreport_container">
           <div class="wooreport_content">
              <div class="wooreport_search_form">
                 <form method="post" name="ni_frm_order_export" id="ni_frm_order_export">
                    <div class="wooreport_search_title">Billing Address</div>
                    <div  class="wooreport_search_row">
                       <div  class="wooreport_field_wrapper">
                          <label for="select_order">Order period</label>
                          <select name="select_order" id="select_order" style="width:250px" >
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last_7_days">Last 7 days</option>
                            <option value="last_30_days">Last 30 days</option>
                             <option value="last_60_days">Last 60 days</option>
                            <option value="this_year">This year</option>
                            </select>
                       </div>
                        <div  class="wooreport_field_wrapper">
                          <label for="order_id">Order ID</label>
                          <input id="order_id" name="order_id" type="text">
                       </div>
                       <div style="clear:both"></div>
                    </div>
                    <div style="clear:both"></div>
                    <div  class="wooreport_search_row">
                       <div style="padding:5px; padding-right:23px;">
                          <input type="submit" value="Search" class="wooreport_button" />
                          <div style="clear:both"></div>
                       </div>
                       <div style="clear:both"></div>
                    </div>
                    <input type="<?php echo $input_type; ?>" name="action" value="ni_order_export_action">
                    <input type="<?php echo $input_type; ?>" name="ni_action_ajax" value="ni_order_billing_address" />
                    <input type="<?php echo $input_type; ?>" name="page" value="<?php echo $_REQUEST["page"] ?>" />
                 </form>
              </div>
              <div style="margin-top:20px;">
                 <div class="ajax_content"></div>
              </div>
           </div>
        </div>
        <?php
			
		}
		function get_order_query($type="DEFAULT"){
			global $wpdb;	
			$today = date("Y-m-d");
	    	$select_order = $this->get_request("select_order");
			$order_id 		 	= $this->get_request("order_id");
			
			$query = "SELECT 
				posts.ID as order_id
				,post_status as order_status
				, date_format( posts.post_date, '%Y-%m-%d') as order_date 
				FROM {$wpdb->prefix}posts as posts			
				WHERE 1=1 ";
				$query .= " AND posts.post_type ='shop_order' ";
				/*Order ID*/
				if ( $order_id!="" ){
					$query .= " AND posts.ID = '{$order_id}'";
				}
				 switch ($select_order) {
					case "today":
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
						break;
					case "yesterday":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') = date_format( DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d')";
						break;
					case "last_7_days":
						$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;
					case "last_30_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;
					case "last_60_days":
							$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 60 DAY), '%Y-%m-%d') AND   '{$today}' ";
						break;		
					case "this_year":
						$query .= " AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(CURDATE(), '%Y-%m-%d'))";			
						break;		
					default:
						$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
				}
			$query .= "order by posts.post_date DESC";	
			
		 if ($type=="ARRAY_A") /*Export*/
		 	$results = $wpdb->get_results( $query, ARRAY_A );
		 if($type=="DEFAULT") /*default*/
		 	$results = $wpdb->get_results( $query);	
		 if($type=="COUNT") /*Count only*/	
		 	$results = $wpdb->get_var($query);		
			
			return $results;	

			
		}
		/*get_order_list*/
		function get_order_list()
		{
			$this->get_order_grid();	
			
			
		}
		public static function get_postmeta($order_ids = '0', $columns = array(), $extra_meta_keys = array(), $type = 'all'){
			
			global $wpdb;
			
			$post_meta_keys = array();
			
			if(count($columns)>0)
			foreach($columns as $key => $label){
				$post_meta_keys[] = $key;
			}
			
			foreach($extra_meta_keys as $key => $label){
				$post_meta_keys[] = $label;
			}
			
			foreach($post_meta_keys as $key => $label){
				$post_meta_keys[] = "_".$label;
			}
			
			$post_meta_key_string = implode("', '",$post_meta_keys);
			
			$sql = " SELECT * FROM {$wpdb->postmeta} AS postmeta";
			
			$sql .= " WHERE 1*1";
			
			if(strlen($order_ids) >0){
				$sql .= " AND postmeta.post_id IN ($order_ids)";
			}
			
			if(strlen($post_meta_key_string) >0){
				$sql .= " AND postmeta.meta_key IN ('{$post_meta_key_string}')";
			}
			
			if($type == 'total'){
				$sql .= " AND (LENGTH(postmeta.meta_value) > 0 AND postmeta.meta_value > 0)";
			}
			
			$sql .= " ORDER BY postmeta.post_id ASC, postmeta.meta_key ASC";
			
			//echo $sql;return '';
			
			$order_meta_data = $wpdb->get_results($sql);			
			
			if($wpdb->last_error){
				echo $wpdb->last_error;
			}else{
				$order_meta_new = array();	
					
				foreach($order_meta_data as $key => $order_meta){
					
					$meta_value	= $order_meta->meta_value;
					
					$meta_key	= $order_meta->meta_key;
					
					$post_id	= $order_meta->post_id;
					
					$meta_key 	= ltrim($meta_key, "_");
					
					$order_meta_new[$post_id][$meta_key] = $meta_value;
					
				}
			}
			
			return $order_meta_new;
			
		}
		function get_items_id_list($order_items = array(),$field_key = 'order_id', $return_default = '-1' , $return_formate = 'string'){
			$list 	= array();
			$string = $return_default;
			if(count($order_items) > 0){
				foreach ($order_items as $key => $order_item) {
					if(isset($order_item->$field_key))
						$list[] = $order_item->$field_key;
				}
				
				$list = array_unique($list);
				
				if($return_formate == "string"){
					$string = implode(",",$list);
				}else{
					$string = $list;
				}
			}
			return $string;
		}
		function get_order_data()
		{
			$order_query = $this->get_order_query("DEFAULT");
			
			
			$extra_meta_keys 	= array();
			$post_ids 			= $this->get_items_id_list($order_query,'order_id');
			$columns = $this->get_columns();
			$postmeta_datas 	= $this->get_postmeta($post_ids, $columns,$extra_meta_keys);
			
			//$this->print_data($postmeta_datas );
			//die;
		
			if(count($order_query)> 0){
				foreach($order_query as $k => $v){
					
					/*Order Data*/
					$order_id =$v->order_id;
					
					/*$order_detail = $this->get_order_detail($order_id);
					foreach($order_detail as $dkey => $dvalue)
					{
							$order_query[$k]->$dkey =$dvalue;
						
					}*/
					
					$postmeta_data 	= isset($postmeta_datas[$order_id]) ? $postmeta_datas[$order_id] : array();
										
					foreach($postmeta_data as $postmeta_key => $postmeta_value){
						
						//if(!empty($postmeta_key))
							$order_query[$k]->{$postmeta_key}	= $postmeta_value;
					}
				}
			}
			else
			{
				echo "No Record Found";
			}
			return $order_query;
		}
		function get_order_grid()
		{
			$order_total = 0;
			$order_data = $this->get_order_data();
			
			//$this->print_data ($order_data);
			
			if(count($order_data)> 0)
			{
				?>
                <div style="text-align:right;margin-bottom:10px">
                <form id="ni_frm_order_export" action="" method="post">
                    <input type="submit" value="Excel" name="btn_excel_export_billing" class="ni_btn wooreport_button" id="btn_excel_export" />
                    <input type="submit" value="Print" name="btn_print" class="ni_btn wooreport_button" id="btn_print" style="display:none" />
                    <input type="hidden" name="select_order" value="<?php echo $this->get_request("select_order");  ?>" />
                </form>
                </div>
				<div class="data-table">
				<table class="wooreport_default_table">
                	<thead>
                    	<tr>
                            <th>#ID</th>
                            <th style="width:15%">Order Date</th>
                            <th>Order Status</th>
                            <th>First Name</th> 
                            <th>Last Name</th> 
                            <th>Company</th> 
                            <th>Address 1</th> 
                            <th>Address 2</th>
                            <th>City</th>  
                            <th>Post Code</th>  
                            <th>Country</th>  
                            <th>State</th>  
                            <th>Phone</th>  
                           
                        </tr>
                    </thead>
					
				
				<?php
				foreach($order_data as $k => $v){
					$order_total += isset($v->order_total)?$v->order_total:0;
				?>
					<tr>
						<td> <?php echo $v->order_id;?> </td>
						<td> <?php echo $v->order_date;?> </td>
                        <td> <?php echo ucfirst ( str_replace("wc-","", $v->order_status));?> </td>
						<td> <?php echo isset($v->billing_first_name)?$v->billing_first_name:"";?> </td>
						<td> <?php echo isset( $v->billing_last_name)? $v->billing_last_name:"";?> </td>
                        <td> <?php echo isset($v->billing_company)?$v->billing_company:"";?> </td>
                        <td> <?php echo isset( $v->billing_address_1)? $v->billing_address_1:"";?> </td>
                        <td> <?php echo  isset($v->billing_address_2)?$v->billing_address_2:"";?> </td>
                        <td> <?php echo isset($v->billing_city)?$v->billing_city:"";?> </td>
                        <td> <?php echo isset($v->billing_postcode)?$v->billing_postcode:"";?> </td>
                        <td> <?php echo $this->get_country_name(isset($v->billing_country)?$v->billing_country:"");?> </td>
                        <td> <?php echo isset($v->billing_state)?$v->billing_state:"";?> </td>
                        <td> <?php echo isset($v->billing_phone)?$v->billing_phone:"";?> </td>
					</tr>	
				<?php }?>
				</table>
               
               
              
                </div>
				<?php
				
				//$this->print_data(	$order_data );
			}
		}
		/*Get Order Header information*/
		function get_order_detail($order_id)
		{
			$order_detail	= get_post_meta($order_id);
			$order_detail_array = array();
			foreach($order_detail as $k => $v)
			{
				$k =substr($k,1);
				$order_detail_array[$k] =$v[0];
			}
			return 	$order_detail_array;
		}
		function get_columns(){
			$columns = array(					
				 "order_id"				=>"#ID"
				,"order_date"			=>"Order Date"
				,"order_status"			=>"Order Status"
				,"billing_first_name"	=>"Billing First Name"
				,"billing_last_name"	=>"Billing Last Name"
				,"billing_company"		=>"Billing Company"
				,"billing_address_1"	=>"Billing Address 1"
				,"billing_address_2"	=>"Billing Address 2"
				,"billing_city"			=>"Billing City"
				,"billing_postcode"		=>"Billing Post Code"
				,"billing_country"		=>"Billing Country"
				,"billing_state"		=>"Billing State"
				,"billing_phone"		=>"Billing Phone"
				
				//,"billing_postcode" => "billing_postcode"
			  );
			  
			return $columns;  
		}
		function ni_order_export($file_name,$file_format)
		{
			
			  $columns = $this->get_columns();
			  $rows =$this->get_order_data();
			  //$this->print_data(  $rows);
			  $i = 0;
			$export_rows = array();
			foreach ( $rows as $rkey => $rvalue ):	
					foreach($columns as $key => $value):
						switch ($key) {
							case "order_status":
								$export_rows[$i][$key] = ucfirst ( str_replace("wc-","",   $rvalue->$key));
								break;
							case "billing_country":
								$export_rows[$i][$key] = $this->get_country_name(isset($rvalue->billing_country)?$rvalue->billing_country:"");
								break;	
							default:
								$export_rows[$i][$key] = isset($rvalue->$key) ? $rvalue->$key : '';
								break;
				}
					endforeach;
					$i++;
			endforeach;
			$this->ExportToCsv($file_name ,$export_rows,$columns,$file_format); 
			//die;
		}
		function get_print_content(){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Print</title>
			<link rel='stylesheet' id='sales-report-style-css'  href='<?php echo  plugins_url( '../assets/css/ni-order-export-style.css', __FILE__ ); ?>' type='text/css' media='all' />
			</head>
			
			<body>
				<?php 
					$this->get_order_grid();
				?>
			  <div class="print_hide" style="text-align:right; margin-top:15px"><input type="button" value="Back" onClick="window.history.go(-1)"> <input type="button" value="Print this page" onClick="window.print()">	</div>
			 
			</body>
			</html>

		<?php
		}
	}
}
?>