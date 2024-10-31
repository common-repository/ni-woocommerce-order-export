<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}

if( !class_exists( 'Ni_Order_Setting' ) ) { 
	include_once('ni-function.php'); 
	class Ni_Order_Setting extends ni_function{
		
		var $options = array();
		
		function __construct(){
		}
		function page_init(){
		$input_type = "text";
		$input_type = "hidden";	
		?>
        
        <form method="post" name="nioe_setting" id="nioe_setting">
        	<input type="<?php echo $input_type; ?>" name="action" value="ni_order_export_action">
            <input type="<?php echo $input_type; ?>" name="page" value="<?php echo $_REQUEST["page"]; ?>" />
            <input type="submit" value="Save" class="wooreport_button2">
            <?php $this->get_column_table(); ?>
        </form>
        <div class="_ajax_nioe_setting"></div>
        <?php
		}
		function get_column_table(){
			$selected_columns  = array();
			$this->options	   = get_option( 'ni_order_export_option');
			
			$columns			   = $this->get_order_columns();
			$selected_columns  = isset($this->options["ni_order_export_columns"])?$this->options["ni_order_export_columns"]:array();
			$columns = array_merge($columns,$selected_columns);
			
			
			//$this->niwoocust_print($selected_columns);
			
			//$people = array("Peter", "Joe", "Glenn", "Cleveland");
			//$this->niwoocust_print($people);
			//$this->niwoocust_print($column);		
			?> <ul id="sortable"><?php	
			foreach($columns as $key=>$value){
				if (array_key_exists($key , $selected_columns)):
				 ?>
                 <li class="ui-state-default">
                 <input type="checkbox" name="ni_order_export_columns[<?php echo $key ?>]" checked="checked" value="<?php echo $value ?>" ><?php echo $value; ?> 			</li>
                 <?php
				 else: 
				  ?>
                 <li class="ui-state-default">
                 <input type="checkbox" name="ni_order_export_columns[<?php echo $key ?>]" value="<?php echo $value ?>" ><?php echo $value; ?>
                 </li>
                 <?php
				 endif;
			}	
			?></ul><?php
		}
		function page_ajax(){
			//$this->print_data($_REQUEST);
			update_option("ni_order_export_option",$_REQUEST);		
			echo "Record Saved.";
		}	
	}
}
?>