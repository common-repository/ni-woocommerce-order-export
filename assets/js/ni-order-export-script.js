// JavaScript Document
jQuery(function($){


	$("#ni_frm_order_export" ).submit(function( event ) {
		$.ajax({
			url:ni_order_export_ajax_object.ni_order_export_ajaxurl,
			data:$( "#ni_frm_order_export" ).serialize() ,
			success:function(data) {
				$(".ajax_content").html(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
				//alert("e");
			}
		}); 
		
		return false;
	});
	
	/*Setting form*/
	$(document).on('submit','#nioe_setting',  function(event){
		event.preventDefault();
		//alert(niwoocust_object.niwoocust_ajaxurl);
		$.ajax({
			url:ni_order_export_ajax_object.ni_order_export_ajaxurl,
			data:$( this ).serialize(),
			success:function(respose) {
				$("._ajax_nioe_setting").html(respose);
				alert(JSON.stringify(respose));
			},
			error: function(respose){
				console.log(respose);
				alert(JSON.stringify(respose));
				//alert("e");
			}
		}); 
	});
	
	
	/*Form Submit*/
	$("#ni_frm_order_export").trigger("submit");
});